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

/**
 * Hilfsfunktion: Legt FiscalYear + Transaktion an und liefert den CSV-Inhalt
 * (zurückkonvertiert von Windows-1252 nach UTF-8) als Zeilen-Array.
 */
function datevExportLines(array $transactionAttributes = [], ?callable $setup = null): array
{
    $account = Account::factory()->create(['type' => AccountType::bank->value]);
    $bookingAccount = BookingAccount::factory()->create(['number' => '21100']);

    $fiscalYear = FiscalYear::factory()->create([
        'year' => 2025,
        'opened_at' => now()->subYear()->startOfYear(),
        'closed_at' => now()->subYear()->endOfYear(),
    ]);

    $transaction = Transaction::factory()->create(array_merge([
        'date' => now()->subYear()->addMonths(3),
        'label' => 'Testbuchung',
        'amount_gross' => 1190,
        'vat' => 19,
        'amount_net' => 1000,
        'account_id' => $account->id,
        'booking_account_id' => $bookingAccount->id,
        'type' => TransactionType::Deposit,
        'status' => TransactionStatus::booked,
    ], $transactionAttributes));

    $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => now()]);

    if ($setup !== null) {
        $setup($fiscalYear, $account, $bookingAccount);
    }

    $service = app(DatevExportService::class);
    $path = $service->export($fiscalYear);
    $raw = Storage::disk('local')->get('private/'.$path);

    // Datei ist Windows-1252-kodiert – für Assertions nach UTF-8 zurückwandeln
    $contents = iconv('Windows-1252', 'UTF-8', $raw);

    return explode("\r\n", $contents);
}

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
            ->toThrow(RuntimeException::class, 'noch nicht geschlossen');
    });

    it('creates a CSV file in the correct storage path', function (): void {
        $account = Account::factory()->create(['type' => AccountType::bank->value]);
        $bookingAccount = BookingAccount::factory()->create(['number' => '21100']);

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

        $fiscalYear->transactions()->attach(
            Transaction::latest()->first()->id,
            ['locked_at' => now()]
        );

        $service = app(DatevExportService::class);
        $path = $service->export($fiscalYear);

        Storage::disk('local')->assertExists('private/'.$path);
        expect(basename($path))->toStartWith('EXTF_');
    });

    it('generates a spec-compliant DATEV metaheader with 31 fields', function (): void {
        $lines = datevExportLines();

        // Zeile 1: Metaheader – Textfelder in Anführungszeichen
        expect($lines[0])->toStartWith('"EXTF";700;21;"Buchungsstapel";12;');

        $headerFields = explode(';', $lines[0]);
        expect($headerFields)->toHaveCount(31);

        // Feld 13: WJ-Beginn, Feld 14: Sachkontenlänge (SKR42 = 5), Feld 27: SKR
        expect($headerFields[12])->toBe('20250101')
            ->and($headerFields[13])->toBe('5')
            ->and($headerFields[26])->toBe('"42"');
    });

    it('generates column header and data rows with 125/124 fields', function (): void {
        $lines = datevExportLines();

        expect($lines[1])->toContain('Umsatz (ohne Soll/Haben-Kz)')
            ->and($lines[1])->toContain('Konto')
            ->and($lines[1])->toContain('Belegdatum');

        expect(explode(';', $lines[1]))->toHaveCount(125)
            ->and(explode(';', $lines[2]))->toHaveCount(124);
    });

    it('uses Kassenbuch convention: Konto = Geldkonto, Gegenkonto = Sachkonto', function (): void {
        $lines = datevExportLines();
        $fields = explode(';', $lines[2]);

        // Deposit auf Bankkonto: S (Geldeingang), Konto 18000 (Bank), Gegenkonto 21100
        expect($fields[0])->toBe('11,90')       // 1 Umsatz
            ->and($fields[1])->toBe('"S"')      // 2 Soll/Haben
            ->and($fields[6])->toBe('18000')    // 7 Konto (Geldkonto Bank)
            ->and($fields[7])->toBe('21100')    // 8 Gegenkonto (Sachkonto)
            ->and($fields[8])->toBe('"3"');     // 9 BU-Schlüssel (19% USt, Einnahme)
    });

    it('marks withdrawals with H and Vorsteuer BU key', function (): void {
        $lines = datevExportLines([
            'type' => TransactionType::Withdrawal,
            'vat' => 19,
        ]);
        $fields = explode(';', $lines[2]);

        expect($fields[1])->toBe('"H"')         // Geldausgang
            ->and($fields[8])->toBe('"9"');     // 19% Vorsteuer
    });

    it('exports storno counter-bookings with positive Umsatz and flipped S/H', function (): void {
        // Storno einer Einnahme: Deposit mit negativem Betrag
        $lines = datevExportLines([
            'label' => 'STORNO-Testbuchung',
            'amount_gross' => -1190,
            'amount_net' => -1000,
        ]);
        $fields = explode(';', $lines[2]);

        expect($fields[0])->toBe('11,90')       // Umsatz immer positiv
            ->and($fields[1])->toBe('"H"');     // Kennzeichen gedreht (Geldabfluss)
    });

    it('skips transactions with zero amount', function (): void {
        $lines = array_filter(datevExportLines([
            'amount_gross' => 0,
            'vat' => 0,
            'amount_net' => 0,
        ]));

        // Nur Metaheader + Spaltenheader
        expect(count($lines))->toBe(2);
    });

    it('encodes the file as Windows-1252', function (): void {
        $account = Account::factory()->create(['type' => AccountType::bank->value]);
        $bookingAccount = BookingAccount::factory()->create(['number' => '21100']);

        $fiscalYear = FiscalYear::factory()->create([
            'year' => 2025,
            'opened_at' => now()->subYear()->startOfYear(),
            'closed_at' => now()->subYear()->endOfYear(),
        ]);

        $transaction = Transaction::factory()->create([
            'date' => now()->subYear()->addMonths(3),
            'label' => 'Mitgliedsbeitrag März – Grüße',
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
        $raw = Storage::disk('local')->get('private/'.$path);

        // Umlaute liegen als CP1252-Einzelbytes vor (ä = 0xE4, ü = 0xFC)
        expect(str_contains($raw, "M\xE4rz"))->toBeTrue()
            ->and(str_contains($raw, "Gr\xFC"))->toBeTrue()
            ->and(mb_check_encoding($raw, 'UTF-8'))->toBeFalse();
    });

    it('ends the file with CRLF', function (): void {
        $account = Account::factory()->create(['type' => AccountType::bank->value]);
        $bookingAccount = BookingAccount::factory()->create(['number' => '21100']);

        $fiscalYear = FiscalYear::factory()->create([
            'year' => 2025,
            'opened_at' => now()->subYear()->startOfYear(),
            'closed_at' => now()->subYear()->endOfYear(),
        ]);

        $transaction = Transaction::factory()->create([
            'date' => now()->subYear()->addMonths(3),
            'account_id' => $account->id,
            'booking_account_id' => $bookingAccount->id,
            'type' => TransactionType::Deposit,
            'status' => TransactionStatus::booked,
        ]);

        $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => now()]);

        $service = app(DatevExportService::class);
        $path = $service->export($fiscalYear);
        $raw = Storage::disk('local')->get('private/'.$path);

        expect(str_ends_with($raw, "\r\n"))->toBeTrue();
    });

    it('strips disallowed characters from Belegfeld 1', function (): void {
        $lines = datevExportLines([
            'reference' => 'RE_2025/001 äöü#',
        ]);
        $fields = explode(';', $lines[2]);

        // Unterstrich, Leerzeichen, Umlaute und # sind unzulässig
        expect($fields[10])->toBe('"RE2025/001"');
    });

    it('excludes transfer transactions from export', function (): void {
        $lines = array_filter(datevExportLines([
            'label' => 'Umbuchung',
            'amount_gross' => 5000,
            'vat' => 0,
            'amount_net' => 5000,
            'type' => TransactionType::Transfer,
        ]));

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
        $lines = datevExportLines([
            'label' => 'Betrag Test',
            'amount_gross' => 1196, // 11,96 €
            'vat' => 19,
            'amount_net' => 1005,
        ]);

        // Umsatz als erstes Feld mit Komma als Dezimaltrennzeichen
        expect($lines[2])->toStartWith('11,96;');
    });

});
