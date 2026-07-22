<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Enums\BookingAccountArea;
use App\Enums\TransactionType;
use App\Helpers\DateHelper;
use Illuminate\Support\Collection;

/**
 * Jahresbericht-PDF
 *
 * Erwartet $snapshot aus AnnualReportService::build():
 *
 *   $data = AnnualReportService::build(year: 2024);
 *   $pdf  = new AnnualReportPdf(
 *               year:         $data['year'],
 *               snapshot:     $data['snapshot'],
 *               transactions: $data['transactions'],
 *           );
 *   $pdf->generateContent();
 *   return $pdf->Output('Jahresbericht-2024.pdf', 'S');
 *
 * Aufbau:
 *   Seite 1  – Deckblatt & Übersicht
 *   Seite 2  – EÜR nach USt-Satz
 *   Seite 3  – Sphären-Aufteilung
 *   Seite 4  – Event-Auswertung
 *   Seite 5  – Projektauswertung
 *   Seite 6  – Förderungsübersicht (Verwendungsnachweis-Basis)
 *   Seite 7  – Buchungskonto-Übersicht (SKR42)
 *   Seite 8+ – Einzelbuchungen (Anhang)
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
        parent::__construct($locale, __('pdf.annual_report.title', ['year' => $year]), showPageNumbers: true);

        $this->year = $year;
        $this->snapshot = $snapshot;
        $this->transactions = $transactions;

        $this->SetCreator('Vereinsverwaltung');
        $this->SetAuthor(setting('organization.name'));
        $this->SetTitle(__('pdf.annual_report.title', ['year' => $year]));
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

        if (! empty($this->snapshot['projects'])) {
            $this->AddPage();
            $this->renderProjects();
        }

        if (! empty($this->snapshot['fundings'])) {
            $this->AddPage();
            $this->renderFundings();
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
        $this->Cell(0, 14, __('pdf.annual_report.title', ['year' => $this->year]), 0, 1, 'C');

        $this->SetFont($this->font, '', 11);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 7, __('pdf.annual_report.cover_subtitle'), 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);

        $this->renderHRule();
        $this->ln(4);

        $meta = $this->snapshot['metadata'] ?? [];
        $generatedAt = isset($meta['generated_at']) ? DateHelper::formatDateTime($meta['generated_at']) : DateHelper::formatDateTime(now());
        $generatedBy = $meta['generated_by'] ?? '-';

        $this->SetFont($this->font, '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, __('pdf.annual_report.meta_line', ['date' => $generatedAt, 'user' => $generatedBy]), 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);

        $this->ln(self::SECTION_GAP);
    }

    private function renderSummaryBoxes(): void
    {
        $this->renderSectionTitle(__('pdf.annual_report.summary'));

        $summary = $this->snapshot['summary'] ?? [];
        $income = $summary['total_income'] ?? 0;
        $expense = $summary['total_expense'] ?? 0;
        $balance = $summary['balance'] ?? 0;
        $count = $summary['transaction_count'] ?? 0;

        $pageW = $this->getPageWidth() - 38;
        $boxW = ($pageW - 6) / 3;
        $boxH = 20;

        $this->renderBox($this->GetX(), $this->GetY(), $boxW, $boxH, self::C_INCOME, __('pdf.annual_report.income'), $this->nf($income).' €');
        $this->SetXY($this->GetX() + $boxW + 3, $this->GetY());
        $this->renderBox($this->GetX(), $this->GetY(), $boxW, $boxH, self::C_EXPENSE, __('pdf.annual_report.expenses'), $this->nf($expense).' €');
        $this->SetXY($this->GetX() + $boxW + 3, $this->GetY());
        $this->renderBox($this->GetX(), $this->GetY(), $boxW, $boxH, $balance >= 0 ? self::C_INCOME : self::C_EXPENSE, __('pdf.annual_report.balance'), $this->nf($balance).' €');

        $this->ln($boxH + self::SECTION_GAP);

        // Mini-Stats: Projekte & Förderungen
        $projectCount = count($this->snapshot['projects'] ?? []);
        $fundingCount = count($this->snapshot['fundings'] ?? []);

        $this->SetFont($this->font, '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, __('pdf.annual_report.meta_stats', ['count' => $count, 'project_count' => $projectCount, 'funding_count' => $fundingCount]), 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->ln(self::SECTION_GAP);
    }

    // =========================================================================
    // Seite 2 – EÜR nach USt-Satz
    // =========================================================================

    private function renderEurByVat(): void
    {
        $this->renderSectionTitle(__('pdf.annual_report.eur_by_vat_title'));

        $this->SetFont($this->font, '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, __('pdf.annual_report.eur_by_vat_note'), 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->ln(2);

        $byVat = $this->snapshot['eur']['by_vat'] ?? [];
        $cols = [40, 45, 45, 0];
        $headers = [__('pdf.annual_report.vat_rate'), __('pdf.annual_report.income_eur'), __('pdf.annual_report.expenses_eur'), __('pdf.annual_report.balance_eur')];
        $aligns = ['L', 'R', 'R', 'R'];

        $this->renderTableHeader($cols, $headers, $aligns);

        $totalIncome = 0;
        $totalExpense = 0;
        $alt = false;

        foreach ($byVat as $row) {
            $income = $row['income'] ?? 0;
            $expense = $row['expense'] ?? 0;
            $totalIncome += $income;
            $totalExpense += $expense;

            $this->setAltFill($alt);
            $this->Cell($cols[0], self::ROW_H, ($row['vat'] ?? 0).' %', 1, 0, 'L', true);
            $this->Cell($cols[1], self::ROW_H, $this->nf($income), 1, 0, 'R', true);
            $this->Cell($cols[2], self::ROW_H, $this->nf($expense), 1, 0, 'R', true);
            $this->renderBalanceCell($cols[3], $income - $expense);
            $alt = ! $alt;
        }

        $this->SetFont($this->font, 'B', 9);
        $this->SetFillColor(...self::C_LIGHT);
        $this->Cell($cols[0], self::ROW_H, __('pdf.annual_report.total'), 1, 0, 'L', true);
        $this->Cell($cols[1], self::ROW_H, $this->nf($totalIncome), 1, 0, 'R', true);
        $this->Cell($cols[2], self::ROW_H, $this->nf($totalExpense), 1, 0, 'R', true);
        $this->renderBalanceCell($cols[3], $totalIncome - $totalExpense, bold: true);

        $this->ln(self::SECTION_GAP);
    }

    // =========================================================================
    // Seite 3 – EÜR nach steuerlicher Sphäre
    // =========================================================================

    private function renderEurBySphere(): void
    {
        $this->renderSectionTitle(__('pdf.annual_report.eur_by_sphere_title'));

        $this->SetFont($this->font, '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->MultiCell(0, 5,
            __('pdf.annual_report.eur_by_sphere_note'),
            0, 'L'
        );
        $this->SetTextColor(0, 0, 0);
        $this->ln(3);

        $bySphere = $this->snapshot['eur']['by_sphere'] ?? [];
        $cols = [70, 38, 38, 0];
        $headers = [__('pdf.annual_report.sphere'), __('pdf.annual_report.income_eur'), __('pdf.annual_report.expenses_eur'), __('pdf.annual_report.balance_eur')];
        $aligns = ['L', 'R', 'R', 'R'];

        $this->renderTableHeader($cols, $headers, $aligns);

        $totalIncome = 0;
        $totalExpense = 0;
        $alt = false;

        foreach (BookingAccountArea::cases() as $area) {
            $row = $bySphere[$area->value] ?? ['income' => 0, 'expense' => 0];
            $income = $row['income'] ?? 0;
            $expense = $row['expense'] ?? 0;
            $totalIncome += $income;
            $totalExpense += $expense;

            $this->setAltFill($alt);
            $this->Cell($cols[0], self::ROW_H, $area->label(), 1, 0, 'L', true);
            $this->Cell($cols[1], self::ROW_H, $this->nf($income), 1, 0, 'R', true);
            $this->Cell($cols[2], self::ROW_H, $this->nf($expense), 1, 0, 'R', true);
            $this->renderBalanceCell($cols[3], $income - $expense);
            $alt = ! $alt;
        }

        $this->SetFont($this->font, 'B', 9);
        $this->SetFillColor(...self::C_LIGHT);
        $this->Cell($cols[0], self::ROW_H, __('pdf.annual_report.total'), 1, 0, 'L', true);
        $this->Cell($cols[1], self::ROW_H, $this->nf($totalIncome), 1, 0, 'R', true);
        $this->Cell($cols[2], self::ROW_H, $this->nf($totalExpense), 1, 0, 'R', true);
        $this->renderBalanceCell($cols[3], $totalIncome - $totalExpense, bold: true);

        $this->ln(self::SECTION_GAP);

        $this->SetFont($this->font, 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->MultiCell(0, 5,
            __('pdf.annual_report.sphere_legal_note'),
            0, 'L'
        );
        $this->SetTextColor(0, 0, 0);
    }

    // =========================================================================
    // Seite 4 – Event-Auswertung
    // =========================================================================

    private function renderEvents(): void
    {
        $this->renderSectionTitle(__('pdf.annual_report.events_title'));

        $events = $this->snapshot['events'] ?? [];
        $pageW = $this->getPageWidth() - 38;
        $cols = [55, 22, 27, 27, 14];
        $salW = $pageW - array_sum($cols);
        $widths = [$cols[0], $cols[1], $cols[2], $cols[3], $salW, $cols[4]];
        $headers = [__('pdf.annual_report.event'), __('pdf.annual_report.date'), __('pdf.annual_report.income_eur'), __('pdf.annual_report.expenses_eur'), __('pdf.annual_report.balance_eur'), __('pdf.annual_report.visitors')];

        $this->SetFillColor(...self::C_HEADER);
        $this->SetTextColor(...self::C_WHITE);
        $this->SetFont($this->font, 'B', 8);
        foreach ($headers as $i => $h) {
            $this->Cell($widths[$i], self::HEADER_H, $h, 1, 0, $i >= 2 ? 'R' : 'L', true);
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
            $visitors = $event['visitor_count'] ?? 0;
            $totalIncome += $income;
            $totalExpense += $expense;
            $totalVisitors += $visitors;

            $this->setAltFill($alt);
            $this->SetFont($this->font, '', 8);
            $this->Cell($widths[0], self::ROW_H, mb_strimwidth($event['title'] ?? '-', 0, 38, '…'), 1, 0, 'L', true);
            $this->Cell($widths[1], self::ROW_H, $event['date'] ?? '-', 1, 0, 'C', true);
            $this->Cell($widths[2], self::ROW_H, $this->nf($income), 1, 0, 'R', true);
            $this->Cell($widths[3], self::ROW_H, $this->nf($expense), 1, 0, 'R', true);
            $this->renderBalanceCell($widths[4], $income - $expense, false, self::ROW_H, true, 0);
            $this->Cell($widths[5], self::ROW_H, (string) $visitors, 1, 1, 'R', true);
            $alt = ! $alt;
        }

        $this->SetFont($this->font, 'B', 8);
        $this->SetFillColor(...self::C_LIGHT);
        $this->Cell($widths[0], self::ROW_H, __('pdf.annual_report.total'), 1, 0, 'L', true);
        $this->Cell($widths[1], self::ROW_H, '', 1, 0, 'C', true);
        $this->Cell($widths[2], self::ROW_H, $this->nf($totalIncome), 1, 0, 'R', true);
        $this->Cell($widths[3], self::ROW_H, $this->nf($totalExpense), 1, 0, 'R', true);
        $this->renderBalanceCell($widths[4], $totalIncome - $totalExpense, true, self::ROW_H, true, 0);
        $this->Cell($widths[5], self::ROW_H, (string) $totalVisitors, 1, 1, 'R', true);

        $this->ln(self::SECTION_GAP);
    }

    // =========================================================================
    // Seite 5 – Projektauswertung (Summary + Detail-Karten)
    // =========================================================================

    private function renderProjects(): void
    {
        $this->renderSectionTitle(__('pdf.annual_report.projects_title'));

        $projects = $this->snapshot['projects'] ?? [];

        if (empty($projects)) {
            $this->SetFont($this->font, 'I', 9);
            $this->SetTextColor(120, 120, 120);
            $this->Cell(0, 6, __('pdf.annual_report.no_projects'), 0, 1);
            $this->SetTextColor(0, 0, 0);

            return;
        }

        // --- Summary-Tabelle ---
        $this->renderProjectSummary($projects);

        $this->ln(self::SECTION_GAP);

        // --- Detail-Karten (2 pro Zeile) ---
        $this->renderSectionSubtitle(__('pdf.annual_report.project_details'));
        $this->renderProjectCards($projects);

        $this->ln(self::SECTION_GAP);
    }

    /**
     * Kompakte Übersichtstabelle aller Projekte.
     */
    private function renderProjectSummary(array $projects): void
    {
        $pageW = $this->getPageWidth() - 38;

        // 5 Spalten: Projekt | Status | Einnahmen | Ausgaben | Saldo
        $cols = [
            $pageW - 18 - 28 - 28 - 28, // Projekt – Rest
            18,  // Status
            28,  // Einnahmen
            28,  // Ausgaben
            28,  // Saldo
        ];

        $headers = [__('pdf.annual_report.project'), __('pdf.annual_report.status'), __('pdf.annual_report.income_eur'), __('pdf.annual_report.expenses_eur'), __('pdf.annual_report.balance_eur')];
        $aligns = ['L', 'L', 'R', 'R', 'R'];
        $this->renderTableHeader($cols, $headers, $aligns);

        $totalIncome = 0;
        $totalExpense = 0;
        $alt = false;

        foreach ($projects as $project) {
            $income = $project['income'] ?? 0;
            $expense = $project['expense'] ?? 0;
            $totalIncome += $income;
            $totalExpense += $expense;

            $this->setAltFill($alt);
            $this->SetFont($this->font, '', 8);
            $this->Cell($cols[0], self::ROW_H, mb_strimwidth($project['title'] ?? '-', 0, 42, '…'), 1, 0, 'L', true);
            $this->Cell($cols[1], self::ROW_H, mb_strimwidth($project['status'] ?? '-', 0, 10, '…'), 1, 0, 'L', true);
            $this->Cell($cols[2], self::ROW_H, $this->nf($income), 1, 0, 'R', true);
            $this->Cell($cols[3], self::ROW_H, $this->nf($expense), 1, 0, 'R', true);
            $this->renderBalanceCell($cols[4], $income - $expense);
            $alt = ! $alt;
        }

        // Summenzeile
        $this->SetFont($this->font, 'B', 8);
        $this->SetFillColor(...self::C_LIGHT);
        $this->Cell($cols[0], self::ROW_H, __('pdf.annual_report.total'), 1, 0, 'L', true);
        $this->Cell($cols[1], self::ROW_H, '', 1, 0, 'L', true);
        $this->Cell($cols[2], self::ROW_H, $this->nf($totalIncome), 1, 0, 'R', true);
        $this->Cell($cols[3], self::ROW_H, $this->nf($totalExpense), 1, 0, 'R', true);
        $this->renderBalanceCell($cols[4], $totalIncome - $totalExpense, bold: true);
    }

    /**
     * Detail-Karten: 2 Karten nebeneinander, automatischer Seitenumbruch.
     */
    private function renderProjectCards(array $projects): void
    {
        $pageW = $this->getPageWidth() - 38;
        $cardW = ($pageW - 4) / 2; // 2 Karten mit 4mm Abstand
        $cardGap = 4;
        $leftX = $this->GetX();

        $chunks = array_chunk($projects, 2);

        foreach ($chunks as $pair) {
            $cardHeight = $this->estimateCardHeight($pair);

            // Seitenumbruch wenn nicht genug Platz
            if ($this->GetY() + $cardHeight > $this->getPageHeight() - 20) {
                $this->AddPage();
                $this->renderSectionSubtitle(__('pdf.annual_report.project_details_continued'));
            }

            $rowY = $this->GetY();

            // Linke Karte
            $this->renderProjectCard($pair[0], $leftX, $rowY, $cardW);

            // Rechte Karte (falls vorhanden)
            if (isset($pair[1])) {
                $this->renderProjectCard($pair[1], $leftX + $cardW + $cardGap, $rowY, $cardW);
            }

            // Cursor unter die höchste Karte setzen
            $this->SetXY($leftX, $rowY + $cardHeight + 4);
        }
    }

    /**
     * Schätzt die Höhe einer Karten-Reihe anhand der Förderungs-Subzeilen.
     */
    private function estimateCardHeight(array $pair): float
    {
        $maxFundings = 0;
        foreach ($pair as $project) {
            $maxFundings = max($maxFundings, count($project['fundings'] ?? []));
        }

        // Basis: Header (8) + 4 Kennzahlen-Zeilen (5 each) + Trennlinie + Förderungs-Header
        $base = 8 + (4 * 5) + 2 + 5;

        // Pro Förderung: 1 Subzeile à 4mm
        return $base + ($maxFundings * 4) + 4;
    }

    /**
     * Zeichnet eine einzelne Projektkarte.
     */
    private function renderProjectCard(array $project, float $x, float $y, float $w): void
    {
        $income = $project['income'] ?? 0;
        $expense = $project['expense'] ?? 0;
        $saldo = $income - $expense;
        $funding = $project['funding_allocated'] ?? 0;
        $coverage = $project['coverage_rate'];
        $fundings = $project['fundings'] ?? [];

        $lineH = 5;
        $padding = 3;

        // --- Karten-Header (dunkelblau) ---
        $this->SetFillColor(...self::C_HEADER);
        $this->SetTextColor(...self::C_WHITE);
        $this->SetFont($this->font, 'B', 8);
        $this->SetXY($x, $y);
        $this->Cell($w, 8, mb_strimwidth($project['title'] ?? '-', 0, (int) ($w / 1.8), '…'), 0, 1, 'L', true);

        // Status & Zeitraum
        $this->SetFont($this->font, '', 7);
        $this->SetXY($x, $this->GetY());
        $period = trim(($project['start_date'] ?? '').' – '.($project['end_date'] ?? ''));
        $statusLine = ($project['status'] ?? '-').($period !== ' – ' ? '  ·  '.$period : '');
        $this->Cell($w, 5, mb_strimwidth($statusLine, 0, (int) ($w / 1.5), '…'), 0, 1, 'L', true);

        $this->SetTextColor(0, 0, 0);

        // --- Trennlinie ---
        $lineY = $this->GetY();
        $this->SetDrawColor(...self::C_HEADER);
        $this->Line($x, $lineY, $x + $w, $lineY);
        $this->SetDrawColor(0, 0, 0);

        // --- Kennzahlen ---
        $labelW = $w * 0.55;
        $valueW = $w - $labelW;

        $rows = [
            [__('pdf.annual_report.income'),  $this->nf($income),   false],
            [__('pdf.annual_report.expenses'),   $this->nf($expense),   false],
            [__('pdf.annual_report.balance'),      $this->nf($saldo),     true],
            [__('pdf.annual_report.funding'),  $this->nf($funding),   false],
        ];

        foreach ($rows as [$label, $value, $colored]) {
            $curY = $this->GetY();
            $this->SetFont($this->font, '', 7.5);
            $this->SetTextColor(80, 80, 80);
            $this->SetXY($x + $padding, $curY);
            $this->Cell($labelW - $padding, $lineH, $label, 0, 0, 'L');

            if ($colored) {
                [$r, $g, $b] = $saldo >= 0 ? self::C_INCOME : self::C_EXPENSE;
                $this->SetTextColor($r, $g, $b);
                $this->SetFont($this->font, 'B', 7.5);
            } else {
                $this->SetTextColor(30, 30, 30);
            }

            $this->SetXY($x + $labelW, $curY);
            $this->Cell($valueW - $padding, $lineH, $value.' €', 0, 1, 'R');
            $this->SetTextColor(0, 0, 0);
        }

        // Deckungsgrad
        if ($coverage !== null) {
            $curY = $this->GetY();
            $this->SetFont($this->font, '', 7.5);
            $this->SetTextColor(80, 80, 80);
            $this->SetXY($x + $padding, $curY);
            $this->Cell($labelW - $padding, $lineH, __('pdf.annual_report.coverage_rate'), 0, 0, 'L');
            $this->SetTextColor(30, 30, 30);
            $this->SetXY($x + $labelW, $curY);
            $this->Cell($valueW - $padding, $lineH, $coverage.' %', 0, 1, 'R');
            $this->SetTextColor(0, 0, 0);
        }

        // --- Förderungen als Subzeilen ---
        if (! empty($fundings)) {
            $subY = $this->GetY() + 1;
            $this->SetDrawColor(200, 200, 200);
            $this->Line($x, $subY, $x + $w, $subY);
            $this->SetDrawColor(0, 0, 0);
            $this->SetY($subY + 1);

            foreach ($fundings as $f) {
                $curY = $this->GetY();
                $this->SetFont($this->font, 'I', 6.5);
                $this->SetTextColor(120, 120, 120);
                $funderLabel = '↳ '.mb_strimwidth(
                    ($f['funder'] ? $f['funder'].' – ' : '').$f['title'],
                    0,
                    (int) ($w / 2.2),
                    '…'
                );
                $this->SetXY($x + $padding, $curY);
                $this->Cell($labelW - $padding, 4, $funderLabel, 0, 0, 'L');
                $this->SetXY($x + $labelW, $curY);
                $this->Cell($valueW - $padding, 4, $this->nf($f['allocated_amount']).' €', 0, 1, 'R');
                $this->SetTextColor(0, 0, 0);
            }
        }

        // --- Kartenrahmen ---
        $cardH = $this->GetY() - $y + 2;
        $this->SetDrawColor(...self::C_HEADER);
        $this->SetLineWidth(0.3);
        $this->Rect($x, $y, $w, $cardH);
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(0, 0, 0);
    }

    /**
     * Kleiner Untertitel zwischen Summary und Karten.
     */
    private function renderSectionSubtitle(string $title): void
    {
        $this->SetFont($this->font, 'B', 9);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 6, $title, 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->ln(1);
    }

    // =========================================================================
    // Seite 6 – Förderungsübersicht (Verwendungsnachweis-Basis)
    // =========================================================================

    private function renderFundings(): void
    {
        $this->renderSectionTitle(__('pdf.annual_report.fundings_title'));

        $this->SetFont($this->font, '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->MultiCell(0, 5,
            __('pdf.annual_report.fundings_note'),
            0, 'L'
        );
        $this->SetTextColor(0, 0, 0);
        $this->ln(3);

        $fundings = $this->snapshot['fundings'] ?? [];

        // Kopf-Tabelle pro Förderung
        $pageW = $this->getPageWidth() - 38;
        $cols = [48, 24, 28, 28, 28, 0];
        $cols[5] = $pageW - array_sum(array_slice($cols, 0, 5));
        $headers = [__('pdf.annual_report.funding_provider'), __('pdf.annual_report.status'), __('pdf.annual_report.approved'), __('pdf.annual_report.received'), __('pdf.annual_report.allocated'), __('pdf.annual_report.remaining')];
        $aligns = ['L', 'L', 'R', 'R', 'R', 'R'];

        $this->renderTableHeader($cols, $headers, $aligns);

        $totalApproved = 0;
        $totalReceived = 0;
        $totalAllocated = 0;
        $alt = false;

        foreach ($fundings as $funding) {
            if ($this->GetY() > $this->getPageHeight() - 45) {
                $this->AddPage();
                $this->renderTableHeader($cols, $headers, $aligns);
                $alt = false;
            }

            $approved = $funding['approved_amount'] ?? 0;
            $received = $funding['received'] ?? 0;
            $allocated = $funding['allocated_to_projects'] ?? 0;
            $remaining = $funding['remaining'] ?? ($approved - $allocated);
            $totalApproved += $approved;
            $totalReceived += $received;
            $totalAllocated += $allocated;

            $funderLabel = mb_strimwidth(
                ($funding['funder'] !== '' ? $funding['funder'].' – ' : '').$funding['title'],
                0, 40, '…'
            );

            $this->setAltFill($alt);
            $this->SetFont($this->font, '', 8);
            $this->Cell($cols[0], self::ROW_H, $funderLabel, 1, 0, 'L', true);
            $this->Cell($cols[1], self::ROW_H, mb_strimwidth($funding['status'] ?? '-', 0, 14, '…'), 1, 0, 'L', true);
            $this->Cell($cols[2], self::ROW_H, $this->nf($approved), 1, 0, 'R', true);
            $this->Cell($cols[3], self::ROW_H, $this->nf($received), 1, 0, 'R', true);
            $this->Cell($cols[4], self::ROW_H, $this->nf($allocated), 1, 0, 'R', true);
            $this->renderBalanceCell($cols[5], $remaining);
            $alt = ! $alt;

            // Sub-Zeilen: verknüpfte Projekte
            if (! empty($funding['projects'])) {
                foreach ($funding['projects'] as $p) {
                    $this->setAltFill(false);
                    $this->SetFont($this->font, 'I', 7);
                    $this->SetTextColor(120, 120, 120);
                    $label = '  ↳ '.mb_strimwidth($p['title'], 0, 38, '…').' ('.$p['status'].')';
                    $this->Cell($cols[0] + $cols[1] + $cols[2] + $cols[3] + $cols[4], self::ROW_H - 1, $label, 'LB', 0, 'L', true);
                    $this->Cell($cols[5], self::ROW_H - 1, $this->nf($p['allocated_amount']), 'RB', 1, 'R', true);
                    $this->SetTextColor(0, 0, 0);
                }
            }

            // Förderzeitraum als Hinweiszeile
            $periodStart = $funding['period_start'] ?? null;
            $periodEnd = $funding['period_end'] ?? null;
            if ($periodStart || $periodEnd) {
                $periodLabel = trim(($periodStart ?? '?').' – '.($periodEnd ?? '?'));
                $this->setAltFill(false);
                $this->SetFont($this->font, 'I', 7);
                $this->SetTextColor(150, 150, 150);
                $this->Cell(array_sum($cols), self::ROW_H - 2, '  '.__('pdf.annual_report.funding_period', ['period' => $periodLabel]), 'LRB', 1, 'L', true);
                $this->SetTextColor(0, 0, 0);
            }
        }

        // Summenzeile
        $totalRemaining = $totalApproved - $totalAllocated;
        $this->SetFont($this->font, 'B', 8);
        $this->SetFillColor(...self::C_LIGHT);
        $this->Cell($cols[0], self::ROW_H, __('pdf.annual_report.total'), 1, 0, 'L', true);
        $this->Cell($cols[1], self::ROW_H, '', 1, 0, 'L', true);
        $this->Cell($cols[2], self::ROW_H, $this->nf($totalApproved), 1, 0, 'R', true);
        $this->Cell($cols[3], self::ROW_H, $this->nf($totalReceived), 1, 0, 'R', true);
        $this->Cell($cols[4], self::ROW_H, $this->nf($totalAllocated), 1, 0, 'R', true);
        $this->renderBalanceCell($cols[5], $totalRemaining, bold: true);

        $this->ln(self::SECTION_GAP);

        // Legende
        $this->SetFont($this->font, 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 5, __('pdf.annual_report.fundings_legend'), 0, 1);
        $this->SetTextColor(0, 0, 0);
    }

    // =========================================================================
    // Seite 7 – Buchungskonto-Übersicht
    // =========================================================================

    private function renderBookingAccounts(): void
    {
        $this->renderSectionTitle(__('pdf.annual_report.booking_accounts_title'));

        $accounts = $this->snapshot['eur']['by_booking_account'] ?? [];
        $cols = [12, 58, 35, 23, 23, 23];
        $headers = [__('pdf.annual_report.number'), __('pdf.annual_report.label'), __('pdf.annual_report.sphere'), __('pdf.annual_report.income_eur'), __('pdf.annual_report.expenses_eur'), __('pdf.annual_report.balance_eur')];
        $aligns = ['L', 'L', 'L', 'R', 'R', 'R'];

        $this->renderTableHeader($cols, $headers, $aligns);

        $grouped = collect($accounts)->groupBy('area');

        foreach (BookingAccountArea::cases() as $area) {
            $rows = $grouped->get($area->value, collect());
            if ($rows->isEmpty()) {
                continue;
            }

            $this->SetFillColor(...self::C_LIGHT);
            $this->SetFont($this->font, 'B', 8);
            $this->Cell(array_sum($cols), self::ROW_H - 1, $area->label(), 'LRB', 1, 'L', true);

            $this->SetFont($this->font, '', 8);
            $alt = false;

            foreach ($rows as $row) {
                $income = $row['income'] ?? 0;
                $expense = $row['expense'] ?? 0;

                $this->setAltFill($alt);
                $this->Cell($cols[0], self::ROW_H, $row['number'], 1, 0, 'L', true);
                $this->Cell($cols[1], self::ROW_H, mb_strimwidth($row['label'] ?? '', 0, 40, '…'), 1, 0, 'L', true);
                $this->Cell($cols[2], self::ROW_H, $area->label(), 1, 0, 'L', true);
                $this->Cell($cols[3], self::ROW_H, $this->nf($income), 1, 0, 'R', true);
                $this->Cell($cols[4], self::ROW_H, $this->nf($expense), 1, 0, 'R', true);
                $this->renderBalanceCell($cols[5], $income - $expense);
                $alt = ! $alt;
            }
        }

        $this->ln(self::SECTION_GAP);
    }

    // =========================================================================
    // Seite 8+ – Einzelbuchungen (Anhang)
    // =========================================================================

    private function renderTransactions(): void
    {
        $this->renderSectionTitle(__('pdf.annual_report.transactions_title'));

        $this->SetFont($this->font, 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 5, __('pdf.annual_report.transactions_note', ['year' => $this->year]), 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->ln(2);

        $cols = [22, 14, 50, 22, 18, 18, 0];
        $headers = [__('pdf.annual_report.date'), __('pdf.annual_report.account_short'), __('pdf.annual_report.label'), __('pdf.annual_report.type'), __('pdf.annual_report.vat_short'), __('pdf.annual_report.project_event'), __('pdf.annual_report.amount')];
        $aligns = ['L', 'L', 'L', 'L', 'L', 'L', 'R'];

        $this->renderTableHeader($cols, $headers, $aligns);

        $alt = false;
        foreach ($this->transactions as $tx) {
            if ($this->GetY() > $this->getPageHeight() - 40) {
                $this->AddPage();
                $this->renderTableHeader($cols, $headers, $aligns);
                $alt = false;
            }

            $date = DateHelper::formatDate($tx->date) ?: '-';
            $account = str_pad((string) ($tx->bookingAccount->number ?? ''), 4, '0', STR_PAD_LEFT);
            $label = mb_strimwidth($tx->label ?? '', 0, 36, '…');
            $type = $tx->type === TransactionType::Deposit ? __('pdf.annual_report.deposit') : __('pdf.annual_report.withdrawal');
            $vat = ($tx->vat ?? 0).' %';
            $amount = $tx->amount_gross ?? 0;

            // Kurzkontext: Projekt oder Event
            $context = '-';
            if ($tx->project_transaction?->project) {
                $context = mb_strimwidth($tx->project_transaction->project->title, 0, 14, '…');
            } elseif ($tx->event_transaction?->event) {
                $title = $tx->event_transaction->event->title;
                $context = mb_strimwidth(is_array($title) ? (reset($title)) : $title, 0, 14, '…');
            } elseif ($tx->fundingTransactions->isNotEmpty()) {
                // Mehrfachförderung: erste Förderung + "+n" für jede weitere
                // (Spalte ist nur 14 Zeichen breit – bewusste Anzeige-Entscheidung).
                $titles = $tx->fundingTransactions
                    ->map(fn ($ft) => $ft->funding?->title)
                    ->filter()
                    ->values();
                $context = $titles->count() > 1
                    ? mb_strimwidth((string) $titles->first(), 0, 11, '…').'+'.($titles->count() - 1)
                    : mb_strimwidth((string) $titles->first(), 0, 14, '…');
            }

            $this->setAltFill($alt);
            $this->SetFont($this->font, '', 7);
            $this->Cell($cols[0], self::ROW_H - 1, $date, 1, 0, 'L', true);
            $this->Cell($cols[1], self::ROW_H - 1, $account, 1, 0, 'C', true);
            $this->Cell($cols[2], self::ROW_H - 1, $label, 1, 0, 'L', true);
            $this->Cell($cols[3], self::ROW_H - 1, $type, 1, 0, 'L', true);
            $this->Cell($cols[4], self::ROW_H - 1, $vat, 1, 0, 'C', true);
            $this->Cell($cols[5], self::ROW_H - 1, $context, 1, 0, 'L', true);
            $this->renderBalanceCell($cols[6], $amount, rowH: self::ROW_H - 1, signed: false);
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

    private function renderTableHeader(array $cols, array $headers, array $aligns): void
    {
        $this->SetFillColor(...self::C_HEADER);
        $this->SetTextColor(...self::C_WHITE);
        $this->SetFont($this->font, 'B', 8);

        foreach ($headers as $i => $h) {
            $this->Cell($cols[$i], self::HEADER_H, $h, 1, 0, $aligns[$i], true);
        }
        $this->ln();
        $this->SetTextColor(0, 0, 0);
    }

    /**
     * Saldo-Zelle: grün/rot je nach Vorzeichen, oder neutral (blau) für Beträge.
     */
    private function renderBalanceCell(
        float $w,
        int $value,
        bool $bold = false,
        int $rowH = self::ROW_H,
        bool $signed = true,
        int $ln = 1
    ): void {
        if ($signed) {
            [$r, $g, $b] = $value >= 0 ? self::C_INCOME : self::C_EXPENSE;
        } else {
            [$r, $g, $b] = self::C_NEUTRAL;
        }

        $this->SetTextColor($r, $g, $b);
        $this->SetFont($this->font, $bold ? 'B' : '', $bold ? 9 : 8);
        $this->Cell($w, $rowH, $this->nf($value), 1, $ln, 'R', true);
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
        $this->SetFillColor($r, $g, $b);
        $this->Rect($x, $y, $w, $h, 'F');

        $this->SetXY($x, $y + 2);
        $this->SetFont($this->font, '', 8);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($w, 5, $title, 0, 1, 'C');

        $this->SetXY($x, $y + 8);
        $this->SetFont($this->font, 'B', 12);
        $this->Cell($w, 8, $value, 0, 1, 'C');

        $this->SetTextColor(0, 0, 0);
        $this->SetXY($x, $y);
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
