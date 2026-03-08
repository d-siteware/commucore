<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Enums\BookingAccountArea;
use App\Enums\TransactionType;
use Illuminate\Support\Collection;

/**
 * Jahresbericht-PDF
 *
 * Erwartet $snapshot aus AnnualReportService::build():
 *
 *   $data     = AnnualReportService::build(year: 2024);
 *   $pdf      = new AnnualReportPdf(
 *                   year:         $data['year'],
 *                   snapshot:     $data['snapshot'],
 *                   transactions: $data['transactions'],
 *               );
 *   $pdf->generateContent();
 *   return $pdf->Output('Jahresbericht-2024.pdf', 'S');
 *
 * Aufbau:
 *   Seite 1  – Deckblatt & Übersicht
 *   Seite 2  – EÜR nach USt-Satz
 *   Seite 3  – Sphären-Aufteilung
 *   Seite 4  – Event-Auswertung
 *   Seite 5  – Buchungskonto-Übersicht (SKR49)
 *   Seite 6+ – Einzelbuchungen (Anhang)
 */
final class AnnualReportPdf extends BasePdfTemplate
{
    private int $year;

    private array $snapshot;

    private Collection $transactions;

    // Layout
    private const ROW_H = 6;

    private const HEADER_H = 7;

    private const SECTION_GAP = 5;

    // Farben [R, G, B]
    private const C_HEADER = [30,  60,  90];

    private const C_INCOME = [39, 174,  96];

    private const C_EXPENSE = [192,  57,  43];

    private const C_NEUTRAL = [52, 152, 219];

    private const C_LIGHT = [245, 245, 245];

    private const C_ALT = [248, 249, 250];

    private const C_WHITE = [255, 255, 255];

    public function __construct(
        int $year,
        array $snapshot,
        Collection $transactions,
        string $locale = 'de',
    ) {
        parent::__construct($locale, "Jahresbericht {$year}", showPageNumbers: true);

        $this->year = $year;
        $this->snapshot = $snapshot;
        $this->transactions = $transactions;

        $this->SetCreator('Vereinsverwaltung');
        $this->SetAuthor(setting('organization.name'));
        $this->SetTitle("Jahresbericht {$year}");
    }

    // =========================================================================
    // Entry
    // =========================================================================

    public function generateContent(): void
    {
        $this->AddPage();
        $this->renderCover();
        $this->renderSummaryBoxes();

        $this->AddPage();
        $this->renderEurByVat();

        $this->AddPage();
        $this->renderEurBySphere();

        if (! empty($this->snapshot['events'])) {
            $this->AddPage();
            $this->renderEvents();
        }

        if (! empty($this->snapshot['eur']['by_booking_account'])) {
            $this->AddPage();
            $this->renderBookingAccounts();
        }

        if ($this->transactions->isNotEmpty()) {
            $this->AddPage();
            $this->renderTransactions();
        }
    }

    // =========================================================================
    // Seite 1 – Deckblatt & Kurzübersicht
    // =========================================================================

    private function renderCover(): void
    {
        $this->ln(4);

        [$r, $g, $b] = self::C_HEADER;
        $this->SetTextColor($r, $g, $b);
        $this->SetFont($this->font, 'B', 24);
        $this->Cell(0, 14, "Jahresbericht {$this->year}", 0, 1, 'C');

        $this->SetFont($this->font, '', 11);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 7, 'Einnahmen-Überschuss-Rechnung & Veranstaltungsauswertung', 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);

        $this->renderHRule();
        $this->ln(4);

        // Metadaten
        $meta = $this->snapshot['metadata'] ?? [];
        $this->SetFont($this->font, '', 9);
        $this->SetTextColor(100, 100, 100);
        $generatedAt = isset($meta['generated_at']) ? $meta['generated_at']->format('d.m.Y H:i') : now()->format('d.m.Y H:i');
        $generatedBy = $meta['generated_by'] ?? '-';
        $this->Cell(0, 5, "Erstellt am: {$generatedAt}  |  Erstellt von: {$generatedBy}", 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);

        $this->ln(self::SECTION_GAP);
    }

    private function renderSummaryBoxes(): void
    {
        $this->renderSectionTitle('Kurzübersicht');

        $summary = $this->snapshot['summary'] ?? [];
        $income = $summary['total_income'] ?? 0;
        $expense = $summary['total_expense'] ?? 0;
        $balance = $summary['balance'] ?? 0;
        $count = $summary['transaction_count'] ?? 0;

        $pageW = $this->getPageWidth() - 38; // margins 23+15
        $boxW = ($pageW - 6) / 3;           // 3 Boxen, je 2px Abstand
        $boxH = 20;

        // Box 1 – Einnahmen
        $this->renderBox(
            x: $this->GetX(),
            y: $this->GetY(),
            w: $boxW,
            h: $boxH,
            color: self::C_INCOME,
            title: 'Einnahmen',
            value: $this->nf($income).' €',
        );

        // Box 2 – Ausgaben
        $this->SetXY($this->GetX() + $boxW + 3, $this->GetY());
        $this->renderBox(
            x: $this->GetX(),
            y: $this->GetY(),
            w: $boxW,
            h: $boxH,
            color: self::C_EXPENSE,
            title: 'Ausgaben',
            value: $this->nf($expense).' €',
        );

        // Box 3 – Saldo
        $balanceColor = $balance >= 0 ? self::C_INCOME : self::C_EXPENSE;
        $this->SetXY($this->GetX() + $boxW + 3, $this->GetY());
        $this->renderBox(
            x: $this->GetX(),
            y: $this->GetY(),
            w: $boxW,
            h: $boxH,
            color: $balanceColor,
            title: 'Saldo',
            value: $this->nf($balance).' €',
        );

        $this->ln($boxH + self::SECTION_GAP);

        $this->SetFont($this->font, '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, "Anzahl Buchungen im Geschäftsjahr: {$count}", 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->ln(self::SECTION_GAP);
    }

    // =========================================================================
    // Seite 2 – EÜR nach USt-Satz
    // =========================================================================

    private function renderEurByVat(): void
    {
        $this->renderSectionTitle('Einnahmen-Überschuss-Rechnung (EÜR) – nach Umsatzsteuersatz');

        $this->SetFont($this->font, '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, 'Beträge in Euro (Brutto). Steuerbetrag = Brutto − Netto.', 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->ln(2);

        $byVat = $this->snapshot['eur']['by_vat'] ?? [];

        // Spalten: USt-Satz | Einnahmen | Ausgaben | Saldo
        $cols = [40, 45, 45, 0];
        $headers = ['USt-Satz', 'Einnahmen (€)', 'Ausgaben (€)', 'Saldo (€)'];

        $this->renderTableHeader($cols, $headers);

        $totalIncome = 0;
        $totalExpense = 0;
        $alt = false;

        foreach ($byVat as $row) {
            $income = $row['income'] ?? 0;
            $expense = $row['expense'] ?? 0;
            $balance = $income - $expense;
            $totalIncome += $income;
            $totalExpense += $expense;

            $this->setAltFill($alt);
            $this->Cell($cols[0], self::ROW_H, ($row['vat'] ?? 0).' %', 1, 0, 'L', true);
            $this->Cell($cols[1], self::ROW_H, $this->nf($income), 1, 0, 'R', true);
            $this->Cell($cols[2], self::ROW_H, $this->nf($expense), 1, 0, 'R', true);
            $this->renderBalanceCell($cols[3], $balance);
            $alt = ! $alt;
        }

        // Summenzeile
        $totalBalance = $totalIncome - $totalExpense;
        $this->SetFont($this->font, 'B', 9);
        $this->SetFillColor(...self::C_LIGHT);
        $this->Cell($cols[0], self::ROW_H, 'Gesamt', 1, 0, 'L', true);
        $this->Cell($cols[1], self::ROW_H, $this->nf($totalIncome), 1, 0, 'R', true);
        $this->Cell($cols[2], self::ROW_H, $this->nf($totalExpense), 1, 0, 'R', true);
        $this->renderBalanceCell($cols[3], $totalBalance, bold: true);

        $this->ln(self::SECTION_GAP);
    }

    // =========================================================================
    // Seite 3 – EÜR nach steuerlicher Sphäre
    // =========================================================================

    private function renderEurBySphere(): void
    {
        $this->renderSectionTitle('Steuerliche Sphären-Aufteilung');

        $this->SetFont($this->font, '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->MultiCell(0, 5,
            'Zuordnung gemäß §§ 51 ff. AO (Gemeinnützigkeit). '.
            'Grundlage ist die Sphären-Kennzeichnung am Buchungskonto (SKR49).',
            0, 'L'
        );
        $this->SetTextColor(0, 0, 0);
        $this->ln(3);

        $bySphere = $this->snapshot['eur']['by_sphere'] ?? [];

        $cols = [70, 45, 45, 0];
        $headers = ['Sphäre', 'Einnahmen (€)', 'Ausgaben (€)', 'Saldo (€)'];

        $this->renderTableHeader($cols, $headers);

        $totalIncome = 0;
        $totalExpense = 0;
        $alt = false;

        foreach (BookingAccountArea::cases() as $area) {
            $row = $bySphere[$area->value] ?? ['income' => 0, 'expense' => 0];
            $income = $row['income'] ?? 0;
            $expense = $row['expense'] ?? 0;
            $balance = $income - $expense;
            $totalIncome += $income;
            $totalExpense += $expense;

            $this->setAltFill($alt);
            $this->Cell($cols[0], self::ROW_H, $area->label(), 1, 0, 'L', true);
            $this->Cell($cols[1], self::ROW_H, $this->nf($income), 1, 0, 'R', true);
            $this->Cell($cols[2], self::ROW_H, $this->nf($expense), 1, 0, 'R', true);
            $this->renderBalanceCell($cols[3], $balance);
            $alt = ! $alt;
        }

        // Summe
        $this->SetFont($this->font, 'B', 9);
        $this->SetFillColor(...self::C_LIGHT);
        $this->Cell($cols[0], self::ROW_H, 'Gesamt', 1, 0, 'L', true);
        $this->Cell($cols[1], self::ROW_H, $this->nf($totalIncome), 1, 0, 'R', true);
        $this->Cell($cols[2], self::ROW_H, $this->nf($totalExpense), 1, 0, 'R', true);
        $this->renderBalanceCell($cols[3], $totalIncome - $totalExpense, bold: true);

        $this->ln(self::SECTION_GAP);

        // Hinweis Mittelverwendung
        $this->SetFont($this->font, 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->MultiCell(0, 5,
            'Hinweis: Überschüsse im ideellen Bereich und im Zweckbetrieb sind zeitnah '.
            'und satzungsgemäß zu verwenden (§ 55 Abs. 1 Nr. 5 AO). '.
            'Überschüsse im wirtschaftlichen Geschäftsbetrieb unterliegen der Körperschaft- und Gewerbesteuer '.
            'sofern die Freigrenze von 45.000 € (§ 64 Abs. 3 AO) überschritten wird.',
            0, 'L'
        );
        $this->SetTextColor(0, 0, 0);
    }

    // =========================================================================
    // Seite 4 – Event-Auswertung
    // =========================================================================

    private function renderEvents(): void
    {
        $this->renderSectionTitle('Veranstaltungsauswertung');

        $events = $this->snapshot['events'] ?? [];

        $cols = [55, 22, 30, 30, 0, 20];
        // Sonderfall: letzte Spalte für Besucher braucht feste Breite
        // Wir rechnen die "0"-Spalte (Saldo) manuell
        $pageW = $this->getPageWidth() - 38;
        $fixedSum = $cols[0] + $cols[1] + $cols[2] + $cols[3] + $cols[5];
        $salW = $pageW - $fixedSum;

        $headers = ['Veranstaltung', 'Datum', 'Einnahmen (€)', 'Ausgaben (€)', 'Saldo (€)', 'Besucher'];
        $widths = [$cols[0], $cols[1], $cols[2], $cols[3], $salW, $cols[5]];

        // Header
        $this->SetFillColor(...self::C_HEADER);
        $this->SetTextColor(...self::C_WHITE);
        $this->SetFont($this->font, 'B', 8);
        foreach ($headers as $i => $h) {
            $align = $i >= 2 ? 'R' : 'L';
            $this->Cell($widths[$i], self::HEADER_H, $h, 1, 0, $align, true);
        }
        $this->ln();
        $this->SetTextColor(0, 0, 0);

        $totalIncome = 0;
        $totalExpense = 0;
        $totalVisitors = 0;
        $alt = false;

        foreach ($events as $event) {
            $income = $event['income'] ?? 0;
            $expense = $event['expense'] ?? 0;
            $balance = $event['balance'] ?? ($income - $expense);
            $visitors = $event['visitor_count'] ?? 0;
            $totalIncome += $income;
            $totalExpense += $expense;
            $totalVisitors += $visitors;

            $title = mb_strimwidth($event['title'] ?? '-', 0, 38, '…');

            $this->setAltFill($alt);
            $this->SetFont($this->font, '', 8);
            $this->Cell($widths[0], self::ROW_H, $title, 1, 0, 'L', true);
            $this->Cell($widths[1], self::ROW_H, $event['date'] ?? '-', 1, 0, 'C', true);
            $this->Cell($widths[2], self::ROW_H, $this->nf($income), 1, 0, 'R', true);
            $this->Cell($widths[3], self::ROW_H, $this->nf($expense), 1, 0, 'R', true);
            $this->renderBalanceCell($widths[4], $balance);
            $this->Cell($widths[5], self::ROW_H, (string) $visitors, 1, 1, 'R', true);
            $alt = ! $alt;
        }

        // Summe
        $this->SetFont($this->font, 'B', 8);
        $this->SetFillColor(...self::C_LIGHT);
        $this->Cell($widths[0], self::ROW_H, 'Gesamt', 1, 0, 'L', true);
        $this->Cell($widths[1], self::ROW_H, '', 1, 0, 'C', true);
        $this->Cell($widths[2], self::ROW_H, $this->nf($totalIncome), 1, 0, 'R', true);
        $this->Cell($widths[3], self::ROW_H, $this->nf($totalExpense), 1, 0, 'R', true);
        $this->renderBalanceCell($widths[4], $totalIncome - $totalExpense, bold: true);
        $this->Cell($widths[5], self::ROW_H, (string) $totalVisitors, 1, 1, 'R', true);

        $this->ln(self::SECTION_GAP);
    }

    // =========================================================================
    // Seite 5 – Buchungskonto-Übersicht (SKR49)
    // =========================================================================

    private function renderBookingAccounts(): void
    {
        $this->renderSectionTitle('Buchungskonto-Übersicht (SKR49)');

        $accounts = $this->snapshot['eur']['by_booking_account'] ?? [];

        $cols = [18, 65, 35, 30, 30, 0];
        $headers = ['Nr.', 'Bezeichnung', 'Sphäre', 'Einnahmen (€)', 'Ausgaben (€)', 'Saldo (€)'];

        $this->renderTableHeader($cols, $headers);

        // Gruppiert nach Sphäre ausgeben
        $grouped = collect($accounts)->groupBy('area');

        foreach (BookingAccountArea::cases() as $area) {
            $rows = $grouped->get($area->value, collect());
            if ($rows->isEmpty()) {
                continue;
            }

            // Sphären-Trennzeile
            $this->SetFillColor(...self::C_LIGHT);
            $this->SetFont($this->font, 'B', 8);
            $totalCols = array_sum(array_slice($cols, 0, -1));
            $this->Cell(
                array_sum($cols),
                self::ROW_H - 1,
                $area->label(),
                'LRB', 1, 'L', true
            );

            $this->SetFont($this->font, '', 8);
            $alt = false;

            foreach ($rows as $row) {
                $income = $row['income'] ?? 0;
                $expense = $row['expense'] ?? 0;
                $balance = $income - $expense;

                $this->setAltFill($alt);
                $this->Cell($cols[0], self::ROW_H, str_pad($row['number'] ?? '', 4, '0', STR_PAD_LEFT), 1, 0, 'L', true);
                $this->Cell($cols[1], self::ROW_H, mb_strimwidth($row['label'] ?? '', 0, 40, '…'), 1, 0, 'L', true);
                $this->Cell($cols[2], self::ROW_H, $area->label(), 1, 0, 'L', true);
                $this->Cell($cols[3], self::ROW_H, $this->nf($income), 1, 0, 'R', true);
                $this->Cell($cols[4], self::ROW_H, $this->nf($expense), 1, 0, 'R', true);
                $this->renderBalanceCell($cols[5], $balance);
                $alt = ! $alt;
            }
        }

        $this->ln(self::SECTION_GAP);
    }

    // =========================================================================
    // Seite 6+ – Einzelbuchungen (Anhang)
    // =========================================================================

    private function renderTransactions(): void
    {
        $this->renderSectionTitle('Anhang: Einzelbuchungen');

        $this->SetFont($this->font, 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 5, 'Alle Buchungen des Geschäftsjahres '.$this->year.' in chronologischer Reihenfolge.', 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->ln(2);

        $cols = [22, 14, 60, 26, 18, 0];
        $headers = ['Datum', 'Kto.', 'Bezeichnung', 'Typ', 'USt.', 'Betrag (€)'];

        $this->renderTableHeader($cols, $headers);

        $alt = false;
        foreach ($this->transactions as $tx) {
            // Seitenumbruch mit Header-Wiederholung
            if ($this->GetY() > $this->getPageHeight() - 40) {
                $this->AddPage();
                $this->renderTableHeader($cols, $headers);
                $alt = false;
            }

            $date = $tx->date?->format('d.m.Y') ?? '-';
            $account = str_pad((string) ($tx->bookingAccount !== null ? $tx->bookingAccount->number : ''), 4, '0', STR_PAD_LEFT);
            $label = mb_strimwidth($tx->label ?? '', 0, 48, '…');
            $type = $tx->type === TransactionType::Deposit ? 'Einnahme' : 'Ausgabe';
            $vat = ($tx->vat ?? 0).' %';
            $amount = $tx->amount_gross ?? 0;

            $this->setAltFill($alt);
            $this->SetFont($this->font, '', 7);
            $this->Cell($cols[0], self::ROW_H - 1, $date, 1, 0, 'L', true);
            $this->Cell($cols[1], self::ROW_H - 1, $account, 1, 0, 'C', true);
            $this->Cell($cols[2], self::ROW_H - 1, $label, 1, 0, 'L', true);
            $this->Cell($cols[3], self::ROW_H - 1, $type, 1, 0, 'L', true);
            $this->Cell($cols[4], self::ROW_H - 1, $vat, 1, 0, 'C', true);
            $this->renderBalanceCell($cols[5], $amount, rowH: self::ROW_H - 1, signed: false);
            $alt = ! $alt;
        }

        $this->ln(self::SECTION_GAP);
    }

    // =========================================================================
    // Helpers – Rendering
    // =========================================================================

    private function renderSectionTitle(string $title): void
    {
        [$r, $g, $b] = self::C_HEADER;
        $this->SetFont($this->font, 'B', 12);
        $this->SetTextColor($r, $g, $b);
        $this->Cell(0, 9, $title, 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->font, '', 9);
        $this->ln(1);
    }

    private function renderTableHeader(array $cols, array $headers): void
    {
        $this->SetFillColor(...self::C_HEADER);
        $this->SetTextColor(...self::C_WHITE);
        $this->SetFont($this->font, 'B', 8);

        $last = count($headers) - 1;
        foreach ($headers as $i => $h) {
            $align = $i >= 1 ? 'R' : 'L';
            // Erste und letzte Spalte immer links
            if ($i === 0 || $i === $last) {
                $align = 'L';
            }
            $this->Cell($cols[$i], self::HEADER_H, $h, 1, 0, $align, true);
        }
        $this->ln();
        $this->SetTextColor(0, 0, 0);
    }

    /**
     * Rendert eine Saldo-Zelle mit grüner/roter Farbe je nach Vorzeichen.
     */
    private function renderBalanceCell(
        float $w,
        int $value,
        bool $bold = false,
        int $rowH = self::ROW_H,
        bool $signed = true,
    ): void {
        if ($signed) {
            [$r, $g, $b] = $value >= 0 ? self::C_INCOME : self::C_EXPENSE;
        } else {
            [$r, $g, $b] = self::C_NEUTRAL;
        }

        $this->SetTextColor($r, $g, $b);
        $this->SetFont($this->font, $bold ? 'B' : '', $bold ? 9 : 8);
        $this->Cell($w, $rowH, $this->nf($value), 1, 1, 'R', true);
        $this->SetTextColor(0, 0, 0);
    }

    private function renderBox(
        float $x,
        float $y,
        float $w,
        float $h,
        array $color,
        string $title,
        string $value,
    ): void {
        [$r, $g, $b] = $color;

        // Hintergrund
        $this->SetFillColor($r, $g, $b);
        $this->Rect($x, $y, $w, $h, 'F');

        // Titel
        $this->SetXY($x, $y + 2);
        $this->SetFont($this->font, '', 8);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($w, 5, $title, 0, 1, 'C');

        // Wert
        $this->SetXY($x, $y + 8);
        $this->SetFont($this->font, 'B', 12);
        $this->Cell($w, 8, $value, 0, 1, 'C');

        $this->SetTextColor(0, 0, 0);
        $this->SetXY($x, $y); // Reset für nächste Box
    }

    private function renderHRule(): void
    {
        $this->SetDrawColor(...self::C_HEADER);
        $this->SetLineWidth(0.6);
        $this->Line(23, $this->GetY() + 2, $this->getPageWidth() - 15, $this->GetY() + 2);
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(0, 0, 0);
        $this->ln(5);
    }

    private function setAltFill(bool $alt): void
    {
        $this->SetFillColor(...($alt ? self::C_ALT : self::C_WHITE));
    }
}
