<?php

declare(strict_types=1);

use App\Enums\ReportStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Livewire\Accounting\Report\Index\Page;
use App\Mail\DatevExportMail;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\Receipt;
use App\Models\Accounting\Transaction;
use App\Models\User;
use App\Services\Accounting\Datev\DatevExportMailService;
use App\Services\Accounting\Datev\DatevExportService;
use App\Services\Accounting\DatevSettingsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

// ---------------------------------------------------------------------------
// DatevExportService::exportForReport()
// ---------------------------------------------------------------------------

describe('DatevExportService::exportForReport', function (): void {

    it('throws when report status is not audited', function (ReportStatus $status): void {
        $report = AccountReport::factory()->create(['status' => $status]);

        /** @var DatevExportService $service */
        $service = app(DatevExportService::class);

        expect(fn () => $service->exportForReport($report))
            ->toThrow(RuntimeException::class);

    })->with([
        'draft' => [ReportStatus::draft],
        'submitted' => [ReportStatus::submitted],
        'rejected' => [ReportStatus::rejected],
    ]);

    it('creates a CSV file in storage for an audited report', function (): void {
        Storage::fake('local');

        $account = Account::factory()->create();
        $report = AccountReport::factory()->create([
            'account_id' => $account->id,
            'status' => ReportStatus::audited,
            'period_start' => '2025-11-01',
            'period_end' => '2025-11-30',
        ])->fresh();

        // Eine gebuchte Transaktion im Zeitraum
        Transaction::factory()->create([
            'account_id' => $account->id,
            'status' => TransactionStatus::booked,
            'type' => TransactionType::Deposit,
            'date' => '2025-11-15',
            'amount_gross' => 15000, // 150,00 €
            'booking_account_id' => BookingAccount::factory()->create()->id,
        ]);

        /** @var DatevExportService $service */
        $service = app(DatevExportService::class);
        $path = $service->exportForReport($report);

        Storage::disk('local')->assertExists('private/'.$path);

        $content = Storage::disk('local')->get('private/'.$path);
        expect($content)
            ->toContain('EXTF')
            ->toContain('Buchungsstapel')
            ->toContain('20251101') // datumVon im Header
            ->toContain('20251130'); // datumBis im Header
    });

    it('skips transactions outside the report period', function (): void {
        Storage::fake('local');

        $account = Account::factory()->create();
        $report = AccountReport::factory()->create([
            'account_id' => $account->id,
            'status' => ReportStatus::audited,
            'period_start' => '2025-11-01',
            'period_end' => '2025-11-30',
        ]);

        $bookingAccount = BookingAccount::factory()->create();

        // Transaktion innerhalb
        Transaction::factory()->create([
            'account_id' => $account->id,
            'status' => TransactionStatus::booked,
            'type' => TransactionType::Deposit,
            'date' => '2025-11-10',
            'amount_gross' => 5000,
            'booking_account_id' => $bookingAccount->id,
            'label' => 'Innerhalb',
        ]);

        // Transaktion außerhalb
        Transaction::factory()->create([
            'account_id' => $account->id,
            'status' => TransactionStatus::booked,
            'type' => TransactionType::Deposit,
            'date' => '2025-12-01',
            'amount_gross' => 9900,
            'booking_account_id' => $bookingAccount->id,
            'label' => 'Außerhalb',
        ]);

        /** @var DatevExportService $service */
        $service = app(DatevExportService::class);
        $path = $service->exportForReport($report);

        $content = Storage::disk('local')->get('private/'.$path);

        expect($content)
            ->toContain('Innerhalb')
            ->not->toContain('Außerhalb');
    });

    it('produces an empty (header-only) export when no transactions exist', function (): void {
        Storage::fake('local');

        $account = Account::factory()->create();
        $report = AccountReport::factory()->create([
            'account_id' => $account->id,
            'status' => ReportStatus::audited,
            'period_start' => '2025-06-01',
            'period_end' => '2025-06-30',
        ]);

        /** @var DatevExportService $service */
        $service = app(DatevExportService::class);
        $path = $service->exportForReport($report);

        $lines = explode("\r\n", trim(Storage::disk('local')->get('private/'.$path) ?? ''));

        // Nur Metaheader + Spaltenheader, keine Datenzeile
        expect($lines)->toHaveCount(2);
    });

});

// ---------------------------------------------------------------------------
// DatevExportMailService::sendForReport()
// ---------------------------------------------------------------------------

describe('DatevExportMailService::sendForReport', function (): void {

    it('includes current Documents and legacy receipts in the ZIP', function (): void {
        Storage::fake('local');
        Mail::fake();

        $user = User::factory()->create(['is_admin' => true, 'locale' => 'de']);
        $this->actingAs($user);

        $settings = app(DatevSettingsService::class);
        $settings->setBeraterNr('12345');
        $settings->setMandantNr('67890');
        $settings->setRecipientEmail('accountant@example.test');

        $account = Account::factory()->create();
        $bookingAccount = BookingAccount::factory()->create(['number' => '21100']);
        $report = AccountReport::factory()->create([
            'account_id' => $account->id,
            'status' => ReportStatus::audited,
            'period_start' => '2025-11-01',
            'period_end' => '2025-11-30',
        ]);
        $transaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'booking_account_id' => $bookingAccount->id,
            'status' => TransactionStatus::booked,
            'type' => TransactionType::Withdrawal,
            'date' => '2025-11-15',
            'label' => 'Büromaterial',
            'amount_gross' => 1190,
            'amount_net' => 1000,
            'vat' => 19,
        ]);

        Storage::disk('local')->put('accounting/receipts/legacy-file', 'legacy receipt content');
        Receipt::create([
            'transaction_id' => $transaction->id,
            'file_name' => 'legacy-file',
            'file_name_original' => 'legacy-invoice.pdf',
        ]);

        $documentUuid = (string) Str::uuid();
        $documentPath = "documents/transaction/{$transaction->id}/{$documentUuid}";
        Storage::disk('local')->put($documentPath, 'current document content');
        $transaction->documents()->create([
            'uploaded_by_user_id' => $user->id,
            'uuid' => $documentUuid,
            'original_name' => 'current-invoice.pdf',
            'disk' => 'local',
            'path' => $documentPath,
            'mime_type' => 'application/pdf',
            'size' => strlen('current document content'),
        ]);

        $export = app(DatevExportMailService::class)->sendForReport($report);
        $zipPath = Storage::disk('local')->path($export->zip_path);
        $zip = new ZipArchive;

        expect($zip->open($zipPath))->toBeTrue();

        $entries = collect(range(0, $zip->numFiles - 1))
            ->map(fn (int $index): string => $zip->getNameIndex($index) ?: '')
            ->values();
        $attachmentEntries = $entries->filter(fn (string $entry): bool => str_ends_with($entry, '.pdf'));
        $attachmentContents = $attachmentEntries
            ->map(fn (string $entry): string|false => $zip->getFromName($entry));

        expect($attachmentEntries)->toHaveCount(2)
            ->and($attachmentContents)->toContain('current document content')
            ->and($attachmentContents)->toContain('legacy receipt content');

        $zip->close();

        Mail::assertSent(DatevExportMail::class);
    });
});

// ---------------------------------------------------------------------------
// Livewire Page::exportDatev() / confirmExportDatev()
// ---------------------------------------------------------------------------

describe('Page::exportDatev', function (): void {

    it('does not download for a non-audited report', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $report = AccountReport::factory()->create(['status' => ReportStatus::draft]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->call('exportDatev', $report->id)
            ->assertNoFileDownloaded();
    });

    it('opens checklist modal with validation results for audited report', function (): void {
        Storage::fake('local');

        $user = User::factory()->create(['is_admin' => true]);
        $account = Account::factory()->create();
        $bookingAccount = BookingAccount::factory()->create(['number' => '21100']);
        $report = AccountReport::factory()->create([
            'account_id' => $account->id,
            'status' => ReportStatus::audited,
            'period_start' => '2025-11-01',
            'period_end' => '2025-11-30',
        ]);

        Transaction::factory()->create([
            'date' => '2025-11-15',
            'label' => 'Testbuchung',
            'amount_gross' => 1190,
            'vat' => 19,
            'amount_net' => 1000,
            'account_id' => $account->id,
            'booking_account_id' => $bookingAccount->id,
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::booked,
        ]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->call('exportDatev', $report->id)
            ->assertSet('datevExportReportId', $report->id)
            ->assertSet('datevValidationChecks', fn (array $checks) => count($checks) > 0)
            ->assertNoFileDownloaded();
    });

    it('triggers a file download via confirmExportDatev', function (): void {
        Storage::fake('local');

        $user = User::factory()->create(['is_admin' => true]);
        $account = Account::factory()->create();
        $bookingAccount = BookingAccount::factory()->create(['number' => '21100']);
        $report = AccountReport::factory()->create([
            'account_id' => $account->id,
            'status' => ReportStatus::audited,
            'period_start' => '2025-11-01',
            'period_end' => '2025-11-30',
        ]);

        Transaction::factory()->create([
            'date' => '2025-11-15',
            'label' => 'Testbuchung',
            'amount_gross' => 1190,
            'vat' => 19,
            'amount_net' => 1000,
            'account_id' => $account->id,
            'booking_account_id' => $bookingAccount->id,
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::booked,
        ]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->set('datevExportReportId', $report->id)
            ->call('confirmExportDatev')
            ->assertFileDownloaded('EXTF_Buchungsstapel_2025-11_'.str_replace(' ', '-', $account->name).'.csv');
    });

    it('is not accessible without the required privilege', function (): void {
        $report = AccountReport::factory()->create(['status' => ReportStatus::audited]);

        // Als User ohne Privileg
        Livewire::actingAs(User::factory()->create())
            ->test(Page::class)
            ->call('exportDatev', $report->id)
            ->assertNoFileDownloaded();
    });

});
