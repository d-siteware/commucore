<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Helpers\DateHelper;
use App\Helpers\MoneyHelper;

final class ProjectFundingReportPdf extends BasePdfTemplate
{
    private const C_HEADER = [35, 65, 95];
    private const C_LIGHT = [239, 246, 255];
    private const C_ALT = [248, 250, 252];
    private const C_WHITE = [255, 255, 255];

    public function __construct(
        private readonly string $subjectType,
        private readonly string $variant,
        private readonly array $data,
        string $locale = 'de',
    ) {
        parent::__construct($locale, $this->title(), true);
    }

    public function generateContent(): void
    {
        $this->AddPage();
        $this->renderSummary();

        if ($this->variant === 'statusbericht') {
            $this->AddPage();
            $this->renderPositions();

            return;
        }

        if ($this->variant === 'detailed') {
            $this->AddPage();
            $this->renderTransactions();

            $relations = $this->subjectType === 'project' ? $this->data['fundings'] : $this->data['projects'];
            if ($relations !== []) {
                $this->AddPage();
                $this->renderRelations();
            }
        }
    }

    private function title(): string
    {
        if ($this->variant === 'statusbericht') {
            return 'Status- und Mittelverwendungsbericht';
        }

        $subject = $this->subjectType === 'project' ? 'Projektbericht' : 'Förderbericht';
        $variant = $this->variant === 'summary' ? 'Executive Summary' : 'Detailbericht';

        return $subject.' - '.$variant;
    }

    private function renderSummary(): void
    {
        $this->sectionTitle($this->title());
        $this->SetFont($this->font, '', 9);

        $this->Cell(45, 6, 'Titel', 0, 0);
        $this->MultiCell(0, 6, (string) $this->data['title'], 0, 'L');
        $this->Cell(45, 6, 'Status', 0, 0);
        $this->Cell(0, 6, (string) $this->data['status'], 0, 1);
        $this->Cell(45, 6, 'Zeitraum', 0, 0);
        $this->Cell(0, 6, $this->data['period'], 0, 1);
        $this->Cell(45, 6, 'Erstellt am', 0, 0);
        $this->Cell(0, 6, DateHelper::formatDateTime(now()), 0, 1);

        if (($this->data['reference'] ?? '') !== '') {
            $this->Cell(45, 6, 'Aktenzeichen', 0, 0);
            $this->Cell(0, 6, (string) $this->data['reference'], 0, 1);
        }

        $this->Ln(6);
        $this->renderBoxes();

        if (($this->data['warnings']['missing_booking_account'] ?? 0) > 0) {
            $this->Ln(5);
            $this->SetFillColor(255, 251, 235);
            $this->SetTextColor(146, 64, 14);
            $this->SetFont($this->font, '', 8);
            $typeName = $this->data['booking_account_type_name'] ?? 'Buchungskonto';
            $this->MultiCell(0, 6, $this->data['warnings']['missing_booking_account'].' Buchungen haben noch kein '.$typeName.'-Buchungskonto. Für DATEV-Exporte muss dies vor Übergabe ergänzt werden.', 1, 'L', true);
            $this->SetTextColor(0, 0, 0);
        }

        if (($this->data['description'] ?? '') !== '') {
            $this->Ln(6);
            $this->sectionSubtitle('Beschreibung');
            $this->SetFont($this->font, '', 8);
            $this->MultiCell(0, 5, (string) $this->data['description'], 0, 'L');
        }
    }

    private function renderBoxes(): void
    {
        $boxes = $this->subjectType === 'project'
            ? [
                ['Einnahmen', $this->money($this->data['income'])],
                ['Ausgaben', $this->money($this->data['expense'])],
                ['Saldo', $this->money($this->data['balance'])],
                ['Förderdeckung', $this->data['coverage_rate'].' %'],
            ]
            : [
                ['Bewilligt', $this->money($this->data['approved_amount'])],
                ['Erhalten', $this->money($this->data['received'])],
                ['Zugeordnet', $this->money($this->data['allocated_to_projects'])],
                ['Rest', $this->money($this->data['remaining'])],
            ];

        $x = $this->GetX();
        $y = $this->GetY();
        $w = 39;
        $gap = 4;

        foreach ($boxes as $index => [$label, $value]) {
            $this->SetXY($x + ($w + $gap) * $index, $y);
            $this->SetFillColor(...self::C_LIGHT);
            $this->Rect($this->GetX(), $this->GetY(), $w, 22, 'F');
            $this->SetFont($this->font, '', 7);
            $this->Cell($w, 7, $label, 0, 2, 'C');
            $this->SetFont($this->font, 'B', 10);
            $this->MultiCell($w, 8, $value, 0, 'C');
        }

        $this->SetY($y + 27);
    }

    private function renderTransactions(): void
    {
        $this->sectionTitle('Buchungsliste');
        $this->tableHeader(['Datum', 'ID', 'Typ', 'Buchung', 'Konto', 'Betrag'], [24, 14, 24, 58, 25, 30]);

        foreach ($this->data['transactions'] as $index => $transaction) {
            $this->ensureSpace(8);
            $this->SetFillColor(...($index % 2 === 0 ? self::C_WHITE : self::C_ALT));
            $this->SetFont($this->font, '', 7);
            $this->Cell(24, 6, $transaction['date'], 1, 0, 'L', true);
            $this->Cell(14, 6, (string) $transaction['id'], 1, 0, 'R', true);
            $this->Cell(24, 6, $transaction['type'], 1, 0, 'L', true);
            $this->Cell(58, 6, mb_substr($transaction['label'], 0, 38), 1, 0, 'L', true);
            $this->Cell(25, 6, $transaction['booking_account'] ?? '-', 1, 0, 'L', true);
            $this->Cell(30, 6, $this->money($transaction['amount']), 1, 1, 'R', true);
        }
    }

    private function renderRelations(): void
    {
        $title = $this->subjectType === 'project' ? 'Verknüpfte Förderungen' : 'Zugeordnete Projekte';
        $this->sectionTitle($title);

        $headers = $this->subjectType === 'project'
            ? ['Fördergeber', 'Förderung', 'Betrag']
            : ['Projekt', 'Status', 'Betrag'];
        $widths = [65, 75, 35];

        $this->tableHeader($headers, $widths);
        $rows = $this->subjectType === 'project' ? $this->data['fundings'] : $this->data['projects'];

        foreach ($rows as $index => $row) {
            $this->ensureSpace(8);
            $this->SetFillColor(...($index % 2 === 0 ? self::C_WHITE : self::C_ALT));
            $this->SetFont($this->font, '', 7);

            if ($this->subjectType === 'project') {
                $this->Cell($widths[0], 6, mb_substr($row['funder'], 0, 42), 1, 0, 'L', true);
                $this->Cell($widths[1], 6, mb_substr($row['title'], 0, 48), 1, 0, 'L', true);
            } else {
                $this->Cell($widths[0], 6, mb_substr($row['title'], 0, 42), 1, 0, 'L', true);
                $this->Cell($widths[1], 6, mb_substr($row['status'], 0, 48), 1, 0, 'L', true);
            }

            $this->Cell($widths[2], 6, $this->money($row['allocated_amount']), 1, 1, 'R', true);
        }
    }

    /**
     * Statusbericht: Plan (Budget je Position) gegen Ist (verknüpfte Buchungen)
     * plus Abweichung, gruppiert nach Kategorie.
     */
    private function renderPositions(): void
    {
        $this->sectionTitle('Plan / Ist je Förderposition');
        $widths = [75, 33, 33, 34];

        $groups = $this->data['position_groups'] ?? [];

        if ($groups === []) {
            $this->SetFont($this->font, '', 8);
            $this->MultiCell(0, 6, 'Für diese Förderung sind noch keine Positionen definiert.', 0, 'L');
        }

        $totalBudget = 0;
        $totalActual = 0;

        foreach ($groups as $group) {
            $this->ensureSpace(22);

            if (($group['category'] ?? '') !== '') {
                $this->sectionSubtitle($group['category']);
            }

            $this->tableHeader(['Position', 'Plan', 'Ist', 'Abweichung'], $widths);

            foreach ($group['positions'] as $index => $position) {
                $this->ensureSpace(8);
                $this->SetFillColor(...($index % 2 === 0 ? self::C_WHITE : self::C_ALT));
                $this->SetFont($this->font, '', 7);
                $this->Cell($widths[0], 6, mb_substr($position['title'], 0, 50), 1, 0, 'L', true);
                $this->Cell($widths[1], 6, $this->money($position['budget']), 1, 0, 'R', true);
                $this->Cell($widths[2], 6, $this->money($position['actual']), 1, 0, 'R', true);
                $this->Cell($widths[3], 6, $this->money($position['remaining']), 1, 1, 'R', true);
            }

            // Kategorie-Zwischensumme
            $this->SetFillColor(...self::C_LIGHT);
            $this->SetFont($this->font, 'B', 7);
            $this->Cell($widths[0], 6, 'Summe '.($group['category'] ?: 'Ohne Kategorie'), 1, 0, 'L', true);
            $this->Cell($widths[1], 6, $this->money($group['budget_sum']), 1, 0, 'R', true);
            $this->Cell($widths[2], 6, $this->money($group['actual_sum']), 1, 0, 'R', true);
            $this->Cell($widths[3], 6, $this->money($group['budget_sum'] - $group['actual_sum']), 1, 1, 'R', true);
            $this->Ln(4);

            $totalBudget += $group['budget_sum'];
            $totalActual += $group['actual_sum'];
        }

        $unassigned = (int) ($this->data['unassigned_actual'] ?? 0);

        if ($unassigned !== 0) {
            $this->ensureSpace(8);
            $this->SetFillColor(...self::C_ALT);
            $this->SetFont($this->font, 'I', 7);
            $this->Cell($widths[0], 6, 'Noch keiner Position zugeordnet', 1, 0, 'L', true);
            $this->Cell($widths[1], 6, '-', 1, 0, 'R', true);
            $this->Cell($widths[2], 6, $this->money($unassigned), 1, 0, 'R', true);
            $this->Cell($widths[3], 6, '-', 1, 1, 'R', true);
            $this->Ln(4);
        }

        if ($groups !== []) {
            $this->ensureSpace(10);
            $this->SetFillColor(...self::C_HEADER);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont($this->font, 'B', 8);
            $this->Cell($widths[0], 7, 'Gesamt', 1, 0, 'L', true);
            $this->Cell($widths[1], 7, $this->money($totalBudget), 1, 0, 'R', true);
            $this->Cell($widths[2], 7, $this->money($totalActual + $unassigned), 1, 0, 'R', true);
            $this->Cell($widths[3], 7, $this->money($totalBudget - $totalActual), 1, 1, 'R', true);
            $this->SetTextColor(0, 0, 0);

            $this->Ln(4);
            $this->SetFont($this->font, '', 7);
            $this->SetTextColor(100, 100, 100);
            $this->MultiCell(0, 4, 'Interner Statusbericht: Plan = Budget je Position, Ist = gebuchte Ausgaben mit Positionszuordnung. Abweichung = Plan abzgl. zugeordnetem Ist.', 0, 'L');
            $this->SetTextColor(0, 0, 0);
        }
    }

    private function tableHeader(array $headers, array $widths): void
    {
        $this->SetFillColor(...self::C_HEADER);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont($this->font, 'B', 7);

        foreach ($headers as $index => $header) {
            $align = $index === count($headers) - 1 ? 'R' : 'L';
            $this->Cell($widths[$index], 7, $header, 1, 0, $align, true);
        }

        $this->Ln();
        $this->SetTextColor(0, 0, 0);
    }

    private function sectionTitle(string $title): void
    {
        $this->SetFont($this->font, 'B', 13);
        $this->SetTextColor(...self::C_HEADER);
        $this->Cell(0, 9, $title, 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }

    private function sectionSubtitle(string $title): void
    {
        $this->SetFont($this->font, 'B', 9);
        $this->Cell(0, 7, $title, 0, 1);
    }

    private function ensureSpace(float $height): void
    {
        if ($this->GetY() + $height > $this->getPageHeight() - 30) {
            $this->AddPage();
        }
    }

    private function money(int $amount): string
    {
        return MoneyHelper::formatCents($amount);
    }
}
