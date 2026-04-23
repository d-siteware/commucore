<?php

declare(strict_types=1);

use App\Enums\ReportStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Livewire\Accounting\Report\Index\Page;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\Datev\DatevExportService;
use Illuminate\Support\Facades\Storage;
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
            ->toThrow(\RuntimeException::class);

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
            'booking_account_id' => \App\Models\Accounting\BookingAccount::factory()->create()->id,
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

        $bookingAccount = \App\Models\Accounting\BookingAccount::factory()->create();

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
// Livewire Page::exportDatev()
// ---------------------------------------------------------------------------

describe('Page::exportDatev', function (): void {

    it('does not download for a non-audited report', function (): void {
        $user = \App\Models\User::factory()->create(['is_admin' => true]);
        $report = AccountReport::factory()->create(['status' => ReportStatus::draft]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->call('exportDatev', $report->id)
            ->assertNoFileDownloaded();
    });

    it('triggers a file download for an audited report', function (): void {
        Storage::fake('local');

        $user = \App\Models\User::factory()->create(['is_admin' => true]);
        $account = Account::factory()->create();
        $report = AccountReport::factory()->create([
            'account_id' => $account->id,
            'status' => ReportStatus::audited,
            'period_start' => '2025-11-01',
            'period_end' => '2025-11-30',
        ]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->call('exportDatev', $report->id)
            ->assertFileDownloaded('DATEV_2025-11_'.str_replace(' ', '-', $account->name).'.csv');
    });

    it('is not accessible without the required privilege', function (): void {
        $report = AccountReport::factory()->create(['status' => ReportStatus::audited]);

        // Als User ohne Privileg
        Livewire::actingAs(\App\Models\User::factory()->create())
            ->test(Page::class)
            ->call('exportDatev', $report->id)
            ->assertNoFileDownloaded();
    });

});
