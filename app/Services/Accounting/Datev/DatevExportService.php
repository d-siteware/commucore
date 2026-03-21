<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

use App\Enums\DatevExportType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\DatevSettingsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Generiert einen DATEV Buchungsstapel (CSV) aus einem abgeschlossenen FiscalYear.
 *
 * Format: DATEV EXTF Buchungsstapel, Formatversion 700, Datensatzversion 12
 *
 * Aufbau der CSV:
 *   Zeile 1: DATEV-Metaheader (Formatkennung, Versionsnummern, Beraternr. etc.)
 *   Zeile 2: Spaltenüberschriften
 *   Zeile 3+: Buchungsdatensätze
 *
 * Voraussetzungen:
 *   - FiscalYear muss geschlossen sein (closed_at gesetzt)
 *   - DatevSettingsService muss konfiguriert sein (isConfigured())
 *   - Transaktionen müssen booking_account_id gesetzt haben
 *
 * @see https://developer.datev.de/datev/platform/de/dtvf/formate/buchungsstapel
 */
final class DatevExportService
{
    public function __construct(
        private readonly DatevSettingsService $settings,
    ) {}

    /**
     * Exportiert das FiscalYear als DATEV Buchungsstapel CSV.
     *
     * @return string Storage-Pfad der erzeugten Datei (relativ zu storage/app/private/)
     *
     * @throws \RuntimeException wenn FiscalYear nicht geschlossen
     */
    public function export(FiscalYear $fiscalYear): string
    {
        $this->guardFiscalYear($fiscalYear);
        $this->guardSettings();

        $transactions = $this->loadTransactions($fiscalYear);

        if ($transactions->isEmpty()) {
            Log::info('DatevExportService: Keine gebuchten Transaktionen für Jahr '.$fiscalYear->year);
        }

        $csv = $this->buildCsv($fiscalYear, $transactions);
        $path = DatevExportType::BUCHUNGSSTAPEL->storagePath($fiscalYear->year);

        Storage::disk('local')->put('private/'.$path, $csv);

        Log::info('DatevExportService: Export erstellt', [
            'year' => $fiscalYear->year,
            'path' => $path,
            'transaction_count' => $transactions->count(),
        ]);

        return $path;
    }

    // ==================== Guards ====================

    private function guardFiscalYear(FiscalYear $fiscalYear): void
    {
        if ($fiscalYear->isOpen()) {
            throw new \RuntimeException(
                "FiscalYear {$fiscalYear->year} ist noch nicht geschlossen. DATEV-Export nicht möglich."
            );
        }
    }

    private function guardSettings(): void
    {
        if (! $this->settings->isConfigured()) {
            Log::warning('DatevExportService: Platzhalter-Nummern aktiv – Export enthält keine echten DATEV-Nummern.');
        }
    }

    // ==================== Data Loading ====================

    /**
     * Lädt alle gebuchten Transaktionen des FiscalYear mit eager-loaded Relations.
     * Transfer-Buchungen werden ausgeschlossen (kein DATEV-Eintrag).
     *
     * @return Collection<int, Transaction>
     */
    private function loadTransactions(FiscalYear $fiscalYear): Collection
    {
        /** @var Collection<int, Transaction> $transactions */
        $transactions = $fiscalYear->transactions()
            ->with(['bookingAccount', 'account'])
            ->where('status', TransactionStatus::booked->value)
            ->whereNot('type', TransactionType::Transfer->value)
            ->whereNotNull('booking_account_id')
            ->orderBy('date')
            ->get();

        $missing = $fiscalYear->transactions()
            ->where('status', TransactionStatus::booked->value)
            ->whereNot('type', TransactionType::Transfer->value)
            ->whereNull('booking_account_id')
            ->count();

        if ($missing > 0) {
            Log::warning("DatevExportService: {$missing} Transaktionen ohne booking_account_id übersprungen.", [
                'year' => $fiscalYear->year,
            ]);
        }

        return $transactions;
    }

    // ==================== CSV Builder ====================

    /**
     * @param  Collection<int, Transaction>  $transactions
     */
    private function buildCsv(FiscalYear $fiscalYear, Collection $transactions): string
    {
        $lines = [];
        $lines[] = $this->buildMetaHeader($fiscalYear, $transactions->count());
        $lines[] = $this->buildColumnHeader();

        foreach ($transactions as $transaction) {
            $line = $this->buildDataRow($transaction);
            if ($line !== null) {
                $lines[] = $line;
            }
        }

        return implode("\r\n", $lines);
    }

    // ==================== Metaheader (Zeile 1) ====================

    private function buildMetaHeader(FiscalYear $fiscalYear, int $count): string
    {
        // closed_at ist durch guardFiscalYear() garantiert nicht null
        $closedAt = $fiscalYear->closed_at;
        $wjBeginn = sprintf('%d0101', $fiscalYear->year);
        $datumVon = $fiscalYear->opened_at->format('Ymd');
        $datumBis = $closedAt->format('Ymd');
        $erstellt = now()->format('YmdHis').'000';

        $fields = [
            'EXTF',                                  // 1  DATEV-Format-Kennzeichen
            '700',                                   // 2  Versionsnummer
            '21',                                    // 3  Datenkategorie (21 = Buchungsstapel)
            'Buchungsstapel',                        // 4  Formatname
            '12',                                    // 5  Formatversion
            $erstellt,                               // 6  Erzeugt am (YYYYMMDDHHmmssmmm)
            '',                                      // 7  Importiert am
            '',                                      // 8  Herkunft
            $this->settings->applicationInfo(),      // 9  Exportiert von
            '',                                      // 10 Importiert von
            $this->settings->beraterNr(),            // 11 Beraternummer
            $this->settings->mandantNr(),            // 12 Mandantennummer
            $wjBeginn,                               // 13 WJ-Beginn (YYYYMMDD)
            (string) $this->settings->kontoLaenge(), // 14 Sachkontonummernlänge
            $datumVon,                               // 15 Datum von (YYYYMMDD)
            $datumBis,                               // 16 Datum bis (YYYYMMDD)
            '',                                      // 17 Bezeichnung
            '',                                      // 18 Diktatkürzel
            '1',                                     // 19 Buchungstyp (1 = Finanzbuchführung)
            '0',                                     // 20 Rechnungslegungszweck
            '0',                                     // 21 Festschreibung (0 = nein)
            'EUR',                                   // 22 WKZ
            '',                                      // 23 Derivatskennzeichen
            '',                                      // 24 SKR
            '',                                      // 25 Branchen-Lösungs-ID
            '',                                      // 26 Anwendungsinformation
            '',                                      // 27 Länge Konto
            '',                                      // 28 Länge Gegenkonto
        ];

        return $this->encodeLine($fields);
    }

    // ==================== Spaltenheader (Zeile 2) ====================

    private function buildColumnHeader(): string
    {
        return $this->encodeLine([
            'Umsatz (ohne Soll/Haben-Kz)',
            'Soll/Haben-Kennzeichen',
            'WKZ Umsatz',
            'Kurs',
            'Basis-Umsatz',
            'WKZ Basis-Umsatz',
            'Konto',
            'Gegenkonto (ohne BU-Schlüssel)',
            'BU-Schlüssel',
            'Belegdatum',
            'Belegfeld 1',
            'Belegfeld 2',
            'Skonto',
            'Buchungstext',
            'Postensperre',
            'Diverse Adressnummer',
            'Geschäftspartnerbank',
            'Sachverhalt',
            'Zinssperre',
            'Beleglink',
            'Beleginfo - Art 1',
            'Beleginfo - Inhalt 1',
            'Beleginfo - Art 2',
            'Beleginfo - Inhalt 2',
            'Beleginfo - Art 3',
            'Beleginfo - Inhalt 3',
            'Beleginfo - Art 4',
            'Beleginfo - Inhalt 4',
            'Beleginfo - Art 5',
            'Beleginfo - Inhalt 5',
            'Beleginfo - Art 6',
            'Beleginfo - Inhalt 6',
            'Beleginfo - Art 7',
            'Beleginfo - Inhalt 7',
            'Beleginfo - Art 8',
            'Beleginfo - Inhalt 8',
            'KOST1 - Kostenstelle',
            'KOST2 - Kostenstelle',
            'Kost-Menge',
            'EU-Land u. UStID',
            'EU-Steuersatz',
            'Abw. Versteuerungsart',
            'Sachverhalt L+L',
            'Funktionsergänzung L+L',
            'BU 49 Hauptfunktionstyp',
            'BU 49 Hauptfunktionsnummer',
            'BU 49 Funktionsergänzung',
            'Zusatzinformation - Art 1',
            'Zusatzinformation - Inhalt 1',
            'Zusatzinformation - Art 2',
            'Zusatzinformation - Inhalt 2',
            'Stück',
            'Gewicht',
            'Zahlweise',
            'Forderungsart',
            'Veranlagungsjahr',
            'Zugeordnete Fälligkeit',
            'Skontotyp',
            'Auftragsnummer',
            'Land',
            'Abrechnungsreferenz',
            'BVV-Position',
            'EU-Mitgliedstaat u. UStID Ursprung',
            'EU-Steuersatz Ursprung',
        ]);
    }

    // ==================== Datensatz (Zeile 3+) ====================

    private function buildDataRow(Transaction $transaction): ?string
    {
        $bookingAccount = $transaction->bookingAccount;
        if ($bookingAccount === null) {
            return null;
        }

        // Kontonummer ohne führende Null (DATEV-konform)
        $konto = ltrim($bookingAccount->number, '0');
        if ($konto === '') {
            return null;
        }

        $gegenkonto = DatevGegenkontoResolver::resolve($transaction->account);

        $sollHaben = match ($transaction->type) {
            TransactionType::Deposit => 'S',
            TransactionType::Withdrawal => 'H',
            TransactionType::Reversal => 'H',
            default => 'S',
        };

        $umsatz = number_format($transaction->amount_gross / 100, 2, ',', '');
        $belegdatum = $transaction->date->format('dm');
        $buKey = DatevBuKeyMapping::toCsvValue($transaction->vat);
        $buchungstext = mb_substr($transaction->label, 0, 60);

        $belegfeld1 = mb_substr(
            preg_replace('/[^a-zA-Z0-9\-_\/]/', '', $transaction->reference ?? '') ?? '',
            0, 36
        );
        $belegfeld2 = (string) $transaction->id;

        // KOST1: numerischer DATEV-Wert der steuerlichen Sphäre (SKR42)
        // Fallback auf Sphäre des Buchungskontos wenn Transaction kein area-Feld hat
        //        $kost1 = $bookingAccount->area->datevKost1();
        $kost1 = ($transaction->area ?? $bookingAccount->area)->datevKost1();

        $fields = [
            $umsatz,        // 1  Umsatz
            $sollHaben,     // 2  Soll/Haben
            'EUR',          // 3  WKZ Umsatz
            '',             // 4  Kurs
            '',             // 5  Basis-Umsatz
            '',             // 6  WKZ Basis-Umsatz
            $konto,         // 7  Konto
            $gegenkonto,    // 8  Gegenkonto
            $buKey,         // 9  BU-Schlüssel
            $belegdatum,    // 10 Belegdatum
            $belegfeld1,    // 11 Belegfeld 1
            $belegfeld2,    // 12 Belegfeld 2
            '',             // 13 Skonto
            $buchungstext,  // 14 Buchungstext
            '',             // 15 Postensperre
            '',             // 16 Diverse Adressnummer
            '',             // 17 Geschäftspartnerbank
            '',             // 18 Sachverhalt
            '',             // 19 Zinssperre
            '',             // 20 Beleglink
            '', '',         // 21-22 Beleginfo Art/Inhalt 1
            '', '',         // 23-24 Beleginfo Art/Inhalt 2
            '', '',         // 25-26 Beleginfo Art/Inhalt 3
            '', '',         // 27-28 Beleginfo Art/Inhalt 4
            '', '',         // 29-30 Beleginfo Art/Inhalt 5
            '', '',         // 31-32 Beleginfo Art/Inhalt 6
            '', '',         // 33-34 Beleginfo Art/Inhalt 7
            '', '',         // 35-36 Beleginfo Art/Inhalt 8
            $kost1,         // 37 KOST1 – numerische Sphäre (1-4)
            '',             // 38 KOST2
            '',             // 39 Kost-Menge
            '',             // 40 EU-Land u. UStID
            '',             // 41 EU-Steuersatz
            '',             // 42 Abw. Versteuerungsart
            '',             // 43 Sachverhalt L+L
            '',             // 44 Funktionsergänzung L+L
            '', '', '',     // 45-47 BU 49 Felder
            '', '',         // 48-49 Zusatzinfo Art/Inhalt 1
            '', '',         // 50-51 Zusatzinfo Art/Inhalt 2
            '', '',         // 52-53 Stück, Gewicht
            '', '', '', '', // 54-57 Zahlweise, Forderungsart, Veranlagungsjahr, Fälligkeit
            '', '', '', '', // 58-61 Skontotyp, Auftragsnummer, Land, Abrechnungsreferenz
            '', '',         // 62-63 BVV-Position, EU-Mitgliedstaat Ursprung
            '',             // 64 EU-Steuersatz Ursprung
        ];

        return $this->encodeLine($fields);
    }

    // ==================== Encoding ====================

    /**
     * Kodiert eine Zeile als DATEV-konforme CSV.
     *
     * DATEV erwartet Semikolon als Trennzeichen.
     * UTF-8 wird ab DATEV 2019 akzeptiert.
     *
     * @param  string[]  $fields
     */
    private function encodeLine(array $fields): string
    {
        return implode(';', array_map(
            fn (string $field): string => $this->quoteField($field),
            $fields
        ));
    }

    private function quoteField(string $value): string
    {
        if (str_contains($value, ';') || str_contains($value, '"') || str_contains($value, "\n")) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }
}
