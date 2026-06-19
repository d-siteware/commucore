<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Enums\TransactionType;
use App\Models\Accounting\FiscalYear;
use Illuminate\Support\Collection;

final class FiscalYearReportPdf extends BasePdfTemplate
{
    private array $snapshotData;

    private Collection $transactions;

    public function __construct(
        int $year,
        array $snapshotData,
        Collection $transactions,
        string $locale = 'de',
    ) {
        $title = "Jahresabschluss {$year}";
        parent::__construct($locale, $title, showPageNumbers: true);

        //        $this->fiscalYear = $year;
        $this->snapshotData = $snapshotData;
        $this->transactions = $transactions;

        $this->SetCreator('Vereinsverwaltung');
        $this->SetAuthor(setting('organization.name'));
        $this->SetTitle($title);
    }

    public function generateContent(): void
    {
        $this->AddPage();
        $this->renderMetaSection();
        $this->renderSummarySection();
        $this->renderTransactionTable();
    }

    private function renderMetaSection(): void
    {
        $meta = $this->snapshotData['metadata'] ?? [];

        $this->SetFont($this->font, 'B', 11);
        $this->Cell(0, 8, 'Zeitraum & Verantwortliche', 0, 1);
        $this->SetFont($this->font, '', 9);
        $this->ln(1);

        $openedAt = isset($meta['opened_at']) ? $meta['opened_at']->format('d.m.Y H:i') : '-';
        $closedAt = isset($meta['closed_at']) ? $meta['closed_at']->format('d.m.Y H:i') : '-';
        $openedBy = $meta['opened_by'] ?? '-';
        $closedBy = $meta['closed_by'] ?? '-';

        $colW = 85;

        $this->SetFont($this->font, 'B', 9);
        $this->Cell($colW, 6, 'Eröffnet am:', 0, 0);
        $this->Cell(0, 6, 'Geschlossen am:', 0, 1);

        $this->SetFont($this->font, '', 9);
        $this->Cell($colW, 6, $openedAt.' (durch '.$openedBy.')', 0, 0);
        $this->Cell(0, 6, $closedAt.' (durch '.$closedBy.')', 0, 1);

        $this->ln(4);
    }

    private function renderSummarySection(): void
    {
        $summary = $this->snapshotData['summary'] ?? [];

        $this->SetFont($this->font, 'B', 11);
        $this->Cell(0, 8, 'Zusammenfassung', 0, 1);
        $this->ln(1);

        // Hintergrundbox
        $this->SetFillColor(245, 245, 245);
        $colW = 56;

        // Header-Zeile
        $this->SetFont($this->font, 'B', 9);
        $this->Cell($colW, 7, 'Einnahmen', 1, 0, 'C', true);
        $this->Cell($colW, 7, 'Ausgaben', 1, 0, 'C', true);
        $this->Cell(0, 7, 'Saldo', 1, 1, 'C', true);

        // Werte-Zeile
        $income = $summary['total_income'] ?? 0;
        $expense = $summary['total_expense'] ?? 0;
        $balance = $summary['balance'] ?? 0;

        $this->SetFont($this->font, '', 10);
        $this->SetFillColor(255, 255, 255);
        $this->Cell($colW, 8, $this->nf($income).' €', 1, 0, 'R', true);
        $this->Cell($colW, 8, $this->nf($expense).' €', 1, 0, 'R', true);
        $this->Cell(0, 8, $this->nf($balance).' €', 1, 1, 'R', true);

        $this->ln(2);

        $this->SetFont($this->font, '', 9);
        $txCount = $summary['transaction_count'] ?? 0;
        $this->Cell(0, 6, "Gesamtzahl der Buchungen: {$txCount}", 0, 1);

        $this->ln(4);
    }

    private function renderTransactionTable(): void
    {
        if ($this->transactions->isEmpty()) {
            $this->SetFont($this->font, 'I', 9);
            $this->Cell(0, 8, 'Keine Transaktionen vorhanden.', 0, 1);

            return;
        }

        $this->SetFont($this->font, 'B', 11);
        $this->Cell(0, 8, 'Buchungen', 0, 1);
        $this->ln(1);

        // Spaltenbreiten: Datum | Beschreibung | Konto | Typ | Betrag
        $cols = [25, 81, 14, 22, 0];
        $headers = ['Datum', 'Beschreibung', 'Konto', 'Typ', 'Betrag (€)'];

        $this->SetFillColor(230, 230, 230);
        $this->SetFont($this->font, 'B', 8);

        foreach ($headers as $i => $header) {
            $align = ($i === 4) ? 'R' : 'L';
            $this->Cell($cols[$i], 7, $header, 1, 0, $align, true);
        }
        $this->ln();

        $this->SetFont($this->font, '', 8);
        $this->SetFillColor(255, 255, 255);

        $rowFill = false;
        foreach ($this->transactions as $tx) {
            $this->SetFillColor($rowFill ? 248 : 255, $rowFill ? 248 : 255, $rowFill ? 248 : 255);

            $date = $tx['date']?->format('d.m.Y') ?? $tx['created_at']?->format('d.m.Y') ?? '-';
            $label = mb_strimwidth($tx['label'], 0, 60, '…');
            $account = str_pad($tx['booking_account'], 4, '0', STR_PAD_LEFT);
            $type = $tx['type'] === TransactionType::Deposit->value ? 'Einnahme' : 'Ausgabe';
            $amount = ($tx['amount'] ?? 0);

            $this->Cell($cols[0], 6, $date, 1, 0, 'L', true);
            $this->Cell($cols[1], 6, $label, 1, 0, 'L', true);
            $this->Cell($cols[2], 6, $account, 1, 0, 'C', true);
            $this->Cell($cols[3], 6, $type, 1, 0, 'L', true);
            $this->Cell($cols[4], 6, \App\Helpers\MoneyHelper::formatCents($amount, withSymbol: false), 1, 1, 'R', true);

            $rowFill = ! $rowFill;
        }
    }
}
