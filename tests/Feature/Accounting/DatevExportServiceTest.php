<?php

declare(strict_types=1);

use App\Enums\AccountType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\Datev\DatevExportService;
use Illuminate\Support\Facades\Storage;

describe('DatevExportService', function (): void {

    beforeEach(function (): void {
        Storage::fake('local');
    });

    it('throws RuntimeException when FiscalYear is still open', function (): void {
        $fiscalYear = FiscalYear::factory()->create([
            'year' => 2025,
            'opened_at' => now()->subYear(),
            'closed_at' => null,
        ]);

        $service = app(DatevExportService::class);

        expect(fn () => $service->export($fiscalYear))
            ->toThrow(\RuntimeException::class, 'noch nicht geschlossen');
    });

    it('creates a CSV file in the correct storage path', function (): void {
        $account = Account::factory()->create(['type' => AccountType::bank->value]);

        $bookingAccount = BookingAccount::factory()->create([
            'number' => '2110',
        ]);

        $fiscalYear = FiscalYear::factory()->create([
            'year' => 2025,
            'opened_at' => now()->subYear()->startOfYear(),
            'closed_at' => now()->subYear()->endOfYear(),
        ]);

        Transaction::factory()->create([
            'date' => now()->subYear()->addMonths(3),
            'label' => 'Mitgliedsbeitrag Test',
            'amount_gross' => 2500,
            'vat' => 0,
            'amount_net' => 2500,
            'account_id' => $account->id,
            'booking_account_id' => $bookingAccount->id,
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::booked,
        ]);

        // Transaktion dem FiscalYear zuordnen
        $fiscalYear->transactions()->attach(
            Transaction::latest()->first()->id,
            ['locked_at' => now()]
        );

        $service = app(DatevExportService::class);
        $path = $service->export($fiscalYear);

        Storage::disk('local')->assertExists('private/'.$path);
    });

    it('generates a CSV with DATEV metaheader on first line', function (): void {
        $account = Account::factory()->create(['type' => AccountType::bank->value]);

        $bookingAccount = BookingAccount::factory()->create([
            'number' => '2110',
        ]);

        $fiscalYear = FiscalYear::factory()->create([
            'year' => 2025,
            'opened_at' => now()->subYear()->startOfYear(),
            'closed_at' => now()->subYear()->endOfYear(),
        ]);

        $transaction = Transaction::factory()->create([
            'date' => now()->subYear()->addMonths(3),
            'label' => 'Testbuchung',
            'amount_gross' => 1190,
            'vat' => 19,
            'amount_net' => 1000,
            'account_id' => $account->id,
            'booking_account_id' => $bookingAccount->id,
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::booked,
        ]);

        $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => now()]);

        $service = app(DatevExportService::class);
        $path = $service->export($fiscalYear);
        $contents = Storage::disk('local')->get('private/'.$path);

        $lines = explode("\r\n", $contents);

        // Zeile 1: DATEV-Metaheader beginnt mit EXTF
        expect($lines[0])->toStartWith('EXTF;');

        // Zeile 2: Spaltenheader enthält Pflichtfelder
        expect($lines[1])->toContain('Umsatz (ohne Soll/Haben-Kz)');
        expect($lines[1])->toContain('Konto');
        expect($lines[1])->toContain('Belegdatum');
    });

    it('excludes transfer transactions from export', function (): void {
        $account = Account::factory()->create(['type' => AccountType::bank->value]);
        $bookingAccount = BookingAccount::factory()->create(['number' => '945']);

        $fiscalYear = FiscalYear::factory()->create([
            'year' => 2025,
            'opened_at' => now()->subYear()->startOfYear(),
            'closed_at' => now()->subYear()->endOfYear(),
        ]);

        $transfer = Transaction::factory()->create([
            'date' => now()->subYear()->addMonth(),
            'label' => 'Umbuchung',
            'amount_gross' => 5000,
            'vat' => 0,
            'amount_net' => 5000,
            'account_id' => $account->id,
            'booking_account_id' => $bookingAccount->id,
            'type' => TransactionType::Transfer,
            'status' => TransactionStatus::booked,
        ]);

        $fiscalYear->transactions()->attach($transfer->id, ['locked_at' => now()]);

        $service = app(DatevExportService::class);
        $path = $service->export($fiscalYear);
        $contents = Storage::disk('local')->get('private/'.$path);

        $lines = array_filter(explode("\r\n", $contents));

        // Nur Metaheader (Z.1) und Spaltenheader (Z.2) – keine Datensätze
        expect(count($lines))->toBe(2);
    });

    it('excludes transactions without booking_account_id', function (): void {
        $account = Account::factory()->create(['type' => AccountType::bank->value]);

        $fiscalYear = FiscalYear::factory()->create([
            'year' => 2025,
            'opened_at' => now()->subYear()->startOfYear(),
            'closed_at' => now()->subYear()->endOfYear(),
        ]);

        $transaction = Transaction::factory()->create([
            'date' => now()->subYear()->addMonth(),
            'label' => 'Ohne Buchungskonto',
            'amount_gross' => 1000,
            'vat' => 0,
            'amount_net' => 1000,
            'account_id' => $account->id,
            'booking_account_id' => null,
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::booked,
        ]);

        $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => now()]);

        $service = app(DatevExportService::class);
        $path = $service->export($fiscalYear);
        $contents = Storage::disk('local')->get('private/'.$path);

        $lines = array_filter(explode("\r\n", $contents));

        expect(count($lines))->toBe(2);
    });

    it('formats amount correctly with German decimal separator', function (): void {
        $account = Account::factory()->create(['type' => AccountType::bank->value]);
        $bookingAccount = BookingAccount::factory()->create(['number' => '2110']);

        $fiscalYear = FiscalYear::factory()->create([
            'year' => 2025,
            'opened_at' => now()->subYear()->startOfYear(),
            'closed_at' => now()->subYear()->endOfYear(),
        ]);

        $transaction = Transaction::factory()->create([
            'date' => now()->subYear()->addMonth(),
            'label' => 'Betrag Test',
            'amount_gross' => 1196, // 11,96 €
            'vat' => 19,
            'amount_net' => 1005,
            'account_id' => $account->id,
            'booking_account_id' => $bookingAccount->id,
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::booked,
        ]);

        $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => now()]);

        $service = app(DatevExportService::class);
        $path = $service->export($fiscalYear);
        $contents = Storage::disk('local')->get('private/'.$path);

        $lines = explode("\r\n", $contents);
        $dataRow = $lines[2]; // Erste Datenzeile

        // Umsatz als erstes Feld mit Komma als Dezimaltrennzeichen
        expect($dataRow)->toStartWith('11,96;');
    });

});
