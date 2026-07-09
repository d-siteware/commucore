<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

use App\Enums\DatevExportType;
use App\Enums\ReportStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\DatevSettingsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Generiert einen DATEV Buchungsstapel (CSV) aus einem abgeschlossenen FiscalYear
 * oder einem geprüften AccountReport (Monatsbericht).
 *
 * Format: DATEV-Format (EXTF), Hauptversion 700, Buchungsstapel Formatversion 12
 *
 * Aufbau der CSV (gemäß offizieller Formatbeschreibung):
 *   Zeile 1: Metaheader – 31 Felder
 *   Zeile 2: Spaltenüberschriften – 125 Felder
 *   Zeile 3+: Buchungsdatensätze – 125 Felder
 *
 * Konventionen:
 *   - Zeichensatz: Windows-1252 (CP1252) – DATEV-Default für den Import
 *   - Trennzeichen: Semikolon, Zeilenende: CRLF (auch nach der letzten Zeile)
 *   - Textfelder werden in Anführungszeichen eingeschlossen
 *   - Umsatz (Feld 1) ist immer positiv; die Richtung bestimmt das
 *     Soll/Haben-Kennzeichen (Feld 2), das sich auf das Konto (Feld 7) bezieht
 *   - Kassenbuch-Konvention: Konto (Feld 7) = Geldkonto (Kasse/Bank/PayPal),
 *     Gegenkonto (Feld 8) = SKR42-Sachkonto (BookingAccount).
 *     S = Geldeingang, H = Geldausgang.
 *     Storno-Gegenbuchungen (negativer Betrag) drehen das Kennzeichen.
 *   - Der BU-Schlüssel (Feld 9) wirkt auf das Gegenkonto (Sachkonto)
 *
 * Voraussetzungen (FiscalYear):
 *   - FiscalYear muss geschlossen sein (closed_at gesetzt)
 *
 * Voraussetzungen (AccountReport):
 *   - AccountReport muss Status ReportStatus::audited haben
 *   - Transaktionen müssen booking_account_id gesetzt haben
 *
 * Validierung: DATEV stellt das "DATEV-Format-Prüfprogramm" bereit
 * (developer.datev.de → DATEV-Format → Tools), mit dem erzeugte Dateien
 * vor dem Import technisch geprüft werden können.
 *
 * @see https://developer.datev.de/de/file-format/details/datev-format/format-description/header
 * @see https://developer.datev.de/de/file-format/details/datev-format/format-description/booking-batch
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

        Storage::disk('local')->put('private/'.$path, $this->encodeToCp1252($csv));

        Log::info('DatevExportService: Export erstellt', [
            'year' => $fiscalYear->year,
            'path' => $path,
            'transaction_count' => $transactions->count(),
        ]);

        return $path;
    }

    /**
     * Exportiert einen einzelnen geprüften Monatsbericht als DATEV Buchungsstapel CSV.
     *
     * Der AccountReport muss den Status ReportStatus::audited besitzen.
     * Der Zeitraum wird aus period_start / period_end des Berichts abgeleitet.
     *
     * @return string Storage-Pfad der erzeugten Datei (relativ zu storage/app/private/)
     *
     * @throws \RuntimeException wenn Bericht nicht geprüft ist
     */
    public function exportForReport(AccountReport $report): string
    {
        $this->guardAccountReport($report);
        $this->guardSettings();

        $transactions = $this->loadTransactionsForReport($report);

        if ($transactions->isEmpty()) {
            Log::info('DatevExportService: Keine gebuchten Transaktionen für Bericht', [
                'report_id' => $report->id,
                'period_start' => $report->period_start->toDateString(),
                'period_end' => $report->period_end->toDateString(),
            ]);
        }

        $csv = $this->buildCsvForReport($report, $transactions);

        // Dateiname mit DATEV-Pflichtpräfix EXTF_, z.B. datev/EXTF_Buchungsstapel_2025-11_Vereinskasse.csv
        $slug = $report->period_start->format('Y-m')
            .'_'.str_replace(' ', '-', $report->account->name ?? 'bericht');
        $path = 'datev/EXTF_Buchungsstapel_'.$slug.'.csv';

        Storage::disk('local')->put('private/'.$path, $this->encodeToCp1252($csv));

        Log::info('DatevExportService: Monatsbericht-Export erstellt', [
            'report_id' => $report->id,
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

    private function guardAccountReport(AccountReport $report): void
    {
        if ($report->status !== ReportStatus::audited) {

            throw new \RuntimeException(
                "Bericht #{$report->id} hat Status {$report->status->value} – Export nur für geprüfte Berichte möglich."
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
     * Lädt alle gebuchten Transaktionen des FiscalYear.
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

    /**
     * Lädt alle gebuchten Transaktionen im Zeitraum des AccountReport,
     * gefiltert auf das zum Bericht gehörende Konto (account_id).
     *
     * @return Collection<int, Transaction>
     */
    private function loadTransactionsForReport(AccountReport $report): Collection
    {
        /** @var Collection<int, Transaction> $transactions */
        $transactions = Transaction::query()
            ->with(['bookingAccount', 'account'])
            ->where('account_id', $report->account_id)
            ->where('status', TransactionStatus::booked->value)
            ->whereNot('type', TransactionType::Transfer->value)
            ->whereNotNull('booking_account_id')
            ->whereBetween('date', [
                $report->period_start->startOfDay(),
                $report->period_end->endOfDay(),
            ])
            ->orderBy('date')
            ->get();

        $missing = Transaction::query()
            ->where('account_id', $report->account_id)
            ->where('status', TransactionStatus::booked->value)
            ->whereNot('type', TransactionType::Transfer->value)
            ->whereNull('booking_account_id')
            ->whereBetween('date', [
                $report->period_start->startOfDay(),
                $report->period_end->endOfDay(),
            ])
            ->count();

        if ($missing > 0) {
            Log::warning("DatevExportService: {$missing} Transaktionen ohne booking_account_id übersprungen.", [
                'report_id' => $report->id,
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
        $lines[] = $this->buildMetaHeader($fiscalYear);
        $lines[] = $this->buildColumnHeader();

        foreach ($transactions as $transaction) {
            $line = $this->buildDataRow($transaction);
            if ($line !== null) {
                $lines[] = $line;
            }
        }

        return implode("\r\n", $lines)."\r\n";
    }

    /**
     * @param  Collection<int, Transaction>  $transactions
     */
    private function buildCsvForReport(AccountReport $report, Collection $transactions): string
    {
        $lines = [];
        $lines[] = $this->buildMetaHeaderForReport($report);
        $lines[] = $this->buildColumnHeader();

        foreach ($transactions as $transaction) {
            $line = $this->buildDataRow($transaction);
            if ($line !== null) {
                $lines[] = $line;
            }
        }

        return implode("\r\n", $lines)."\r\n";
    }

    // ==================== Metaheader (Zeile 1) ====================

    private function buildMetaHeader(FiscalYear $fiscalYear): string
    {
        return $this->assembleMetaHeader(
            wjBeginn: sprintf('%d0101', $fiscalYear->year),
            datumVon: $fiscalYear->opened_at->format('Ymd'),
            datumBis: $fiscalYear->closed_at->format('Ymd'),
            bezeichnung: 'Buchungsstapel '.$fiscalYear->year,
        );
    }

    private function buildMetaHeaderForReport(AccountReport $report): string
    {
        // WJ-Beginn = 1. Januar des Jahres, in dem der Berichtszeitraum beginnt
        return $this->assembleMetaHeader(
            wjBeginn: $report->period_start->format('Y').'0101',
            datumVon: $report->period_start->format('Ymd'),
            datumBis: $report->period_end->format('Ymd'),
            bezeichnung: 'Buchungsstapel '.$report->period_start->format('Y-m'),
        );
    }

    /**
     * Metaheader gemäß offizieller Formatbeschreibung: 31 Felder.
     *
     * Textfelder werden in Anführungszeichen eingeschlossen,
     * Zahlen- und Datumsfelder nicht.
     */
    private function assembleMetaHeader(
        string $wjBeginn,
        string $datumVon,
        string $datumBis,
        string $bezeichnung,
    ): string {
        $erstellt = now()->format('YmdHis').'000';

        $fields = [
            $this->text('EXTF'),                            // 1  DATEV-Format-Kennzeichen (EXTF = externes Programm)
            '700',                                          // 2  Versionsnummer des Formats
            '21',                                           // 3  Datenkategorie (21 = Buchungsstapel)
            $this->text('Buchungsstapel'),                  // 4  Formatname
            '12',                                           // 5  Formatversion
            $erstellt,                                      // 6  Erzeugt am (YYYYMMDDHHmmssfff)
            '',                                             // 7  Importiert (wird beim Import gesetzt)
            '',                                             // 8  Herkunft (wird beim Import durch "SV" ersetzt)
            $this->text(mb_substr($this->settings->applicationInfo(), 0, 25)), // 9  Exportiert von
            '',                                             // 10 Importiert von (wird beim Import gesetzt)
            $this->settings->beraterNr(),                   // 11 Beraternummer
            $this->settings->mandantNr(),                   // 12 Mandantennummer
            $wjBeginn,                                      // 13 WJ-Beginn (YYYYMMDD)
            (string) $this->settings->kontoLaenge(),        // 14 Sachkontonummernlänge
            $datumVon,                                      // 15 Datum von (YYYYMMDD)
            $datumBis,                                      // 16 Datum bis (YYYYMMDD)
            $this->text(mb_substr($bezeichnung, 0, 30)),    // 17 Bezeichnung des Buchungsstapels
            '',                                             // 18 Diktatkürzel
            '1',                                            // 19 Buchungstyp (1 = Finanzbuchführung)
            '0',                                            // 20 Rechnungslegungszweck (0 = unabhängig)
            '0',                                            // 21 Festschreibung (0 = keine Festschreibung)
            $this->text('EUR'),                             // 22 WKZ
            '',                                             // 23 reserviert
            '',                                             // 24 Derivatskennzeichen
            '',                                             // 25 reserviert
            '',                                             // 26 reserviert
            $this->text($this->settings->skr()),            // 27 SKR (42 = Vereine/Stiftungen)
            '',                                             // 28 Branchenlösungs-ID
            '',                                             // 29 reserviert
            '',                                             // 30 reserviert
            '',                                             // 31 Anwendungsinformation
        ];

        return implode(';', $fields);
    }

    // ==================== Spaltenheader (Zeile 2) ====================

    /**
     * Offizielle Spaltenüberschriften des Buchungsstapels (Formatversion 12): 125 Felder.
     */
    private function buildColumnHeader(): string
    {
        $columns = [
            'Umsatz (ohne Soll/Haben-Kz)',            // 1
            'Soll/Haben-Kennzeichen',                 // 2
            'WKZ Umsatz',                             // 3
            'Kurs',                                   // 4
            'Basis-Umsatz',                           // 5
            'WKZ Basis-Umsatz',                       // 6
            'Konto',                                  // 7
            'Gegenkonto (ohne BU-Schlüssel)',         // 8
            'BU-Schlüssel',                           // 9
            'Belegdatum',                             // 10
            'Belegfeld 1',                            // 11
            'Belegfeld 2',                            // 12
            'Skonto',                                 // 13
            'Buchungstext',                           // 14
            'Postensperre',                           // 15
            'Diverse Adressnummer',                   // 16
            'Geschäftspartnerbank',                   // 17
            'Sachverhalt',                            // 18
            'Zinssperre',                             // 19
            'Beleglink',                              // 20
        ];

        // 21–36: Beleginfo Art/Inhalt 1–8
        foreach (range(1, 8) as $i) {
            $columns[] = 'Beleginfo - Art '.$i;
            $columns[] = 'Beleginfo - Inhalt '.$i;
        }

        $columns = [...$columns,
            'KOST1 - Kostenstelle',                   // 37
            'KOST2 - Kostenstelle',                   // 38
            'Kost-Menge',                             // 39
            'EU-Land u. UStID (Bestimmung)',          // 40
            'EU-Steuersatz (Bestimmung)',             // 41
            'Abw. Versteuerungsart',                  // 42
            'Sachverhalt L+L',                        // 43
            'Funktionsergänzung L+L',                 // 44
            'BU 49 Hauptfunktionstyp',                // 45
            'BU 49 Hauptfunktionsnummer',             // 46
            'BU 49 Funktionsergänzung',               // 47
        ];

        // 48–87: Zusatzinformation Art/Inhalt 1–20
        foreach (range(1, 20) as $i) {
            $columns[] = 'Zusatzinformation - Art '.$i;
            $columns[] = 'Zusatzinformation - Inhalt '.$i;
        }

        $columns = [...$columns,
            'Stück',                                  // 88
            'Gewicht',                                // 89
            'Zahlweise',                              // 90
            'Forderungsart',                          // 91
            'Veranlagungsjahr',                       // 92
            'Zugeordnete Fälligkeit',                 // 93
            'Skontotyp',                              // 94
            'Auftragsnummer',                         // 95
            'Buchungstyp',                            // 96
            'USt-Schlüssel (Anzahlungen)',            // 97
            'EU-Mitgliedstaat (Anzahlungen)',         // 98
            'Sachverhalt L+L (Anzahlungen)',          // 99
            'EU-Steuersatz (Anzahlungen)',            // 100
            'Erlöskonto (Anzahlungen)',               // 101
            'Herkunft-Kz',                            // 102
            'Leerfeld',                               // 103
            'KOST-Datum',                             // 104
            'SEPA-Mandatsreferenz',                   // 105
            'Skontosperre',                           // 106
            'Gesellschaftername',                     // 107
            'Beteiligtennummer',                      // 108
            'Identifikationsnummer',                  // 109
            'Zeichnernummer',                         // 110
            'Postensperre bis',                       // 111
            'Bezeichnung SoBil-Sachverhalt',          // 112
            'Kennzeichen SoBil-Buchung',              // 113
            'Festschreibung',                         // 114
            'Leistungsdatum',                         // 115
            'Datum Zuord. Steuerperiode',             // 116
            'Fälligkeit',                             // 117
            'Generalumkehr (GU)',                     // 118
            'Steuersatz',                             // 119
            'Land',                                   // 120
            'Abrechnungsreferenz',                    // 121
            'BVV-Position',                           // 122
            'EU-Mitgliedstaat u. UStID (Ursprung)',   // 123
            'EU-Steuersatz (Ursprung)',               // 124
            'Abw. Skontokonto',                       // 125
        ];

        return implode(';', array_map(
            fn (string $column): string => $this->text($column),
            $columns,
        ));
    }

    // ==================== Datensatz (Zeile 3+) ====================

    /**
     * Buchungsdatensatz mit 125 Feldern (Formatversion 12).
     *
     * Kassenbuch-Konvention:
     *   Konto (7)      = Geldkonto (Kasse/Bank/PayPal)
     *   Gegenkonto (8) = SKR42-Sachkonto (BookingAccount)
     *   S/H (2)        = Wirkung auf das Geldkonto: S = Geldeingang, H = Geldausgang
     *
     * Der Umsatz (1) ist immer positiv. Negative Beträge (Storno-Gegenbuchungen)
     * werden absolut ausgegeben; die Richtung dreht das S/H-Kennzeichen.
     */
    private function buildDataRow(Transaction $transaction): ?string
    {
        $bookingAccount = $transaction->bookingAccount;
        if ($bookingAccount === null) {
            return null;
        }

        $gegenkonto = ltrim($bookingAccount->number, '0');
        if ($gegenkonto === '') {
            return null;
        }

        // Saldo-Wirkung auf das Geldkonto: > 0 = Geldeingang (S), < 0 = Geldausgang (H)
        $effect = $transaction->amount_gross * $transaction->type->multiplier();
        if ($effect === 0) {
            return null;
        }

        $konto = DatevGeldkontoResolver::resolve($transaction->account);
        $sollHaben = $effect > 0 ? 'S' : 'H';
        $umsatz = number_format(abs($transaction->amount_gross) / 100, 2, ',', '');
        $belegdatum = $transaction->date->format('dm');
        $buKey = DatevBuKeyMapping::toCsvValue($transaction->vat, $transaction->type->isExpense());
        $buchungstext = mb_substr($transaction->label, 0, 60);

        // Belegfeld 1/2: erlaubte Zeichen lt. Spezifikation: a-z A-Z 0-9 $ & % * + - /
        $belegfeld1 = mb_substr(
            preg_replace('/[^a-zA-Z0-9$&%*+\-\/]/', '', $transaction->reference ?? '') ?? '',
            0, 36
        );
        $belegfeld2 = (string) $transaction->id;

        $kost1 = ($transaction->area ?? $bookingAccount->area)->datevKost1();

        $fields = array_fill(0, 124, '""');

        $fields[0] = $umsatz;                       // 1  Umsatz (immer positiv)
        $fields[1] = $this->text($sollHaben);       // 2  Soll/Haben-Kennzeichen (bezieht sich auf Feld 7)
        $fields[2] = $this->text('EUR');            // 3  WKZ Umsatz
        $fields[6] = $konto;                        // 7  Konto (Geldkonto)
        $fields[7] = $gegenkonto;                   // 8  Gegenkonto (Sachkonto, ohne BU-Schlüssel)
        $fields[8] = $this->text($buKey);           // 9  BU-Schlüssel (wirkt auf das Gegenkonto)
        $fields[9] = $belegdatum;                   // 10 Belegdatum (TTMM)
        $fields[10] = $this->text($belegfeld1);     // 11 Belegfeld 1 (externe Referenz)
        $fields[11] = $this->text($belegfeld2);     // 12 Belegfeld 2 (interne Transaction-ID)
        $fields[13] = $this->text($buchungstext);   // 14 Buchungstext
        $fields[36] = $this->text($kost1);          // 37 KOST1 (steuerliche Sphäre 1–4)

        return implode(';', $fields);
    }

    // ==================== Encoding ====================

    /**
     * Textfeld für die CSV-Ausgabe: in Anführungszeichen eingeschlossen,
     * innere Anführungszeichen verdoppelt. Leere Werte bleiben leer.
     */
    private function text(string $value): string
    {
        if ($value === '') {
            return '""';
        }

        return '"'.str_replace('"', '""', $value).'"';
    }

    /**
     * Konvertiert die fertige CSV von UTF-8 nach Windows-1252 (DATEV-Default).
     * Nicht abbildbare Zeichen werden ersetzt.
     */
    private function encodeToCp1252(string $csv): string
    {
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $csv);

        if ($converted === false) {
            $converted = mb_convert_encoding($csv, 'Windows-1252', 'UTF-8');
        }

        return $converted;
    }
}
