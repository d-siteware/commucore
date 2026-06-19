<?php

declare(strict_types=1);

namespace App\Pdfs;

use Illuminate\Support\Collection;

final class MembershipFeesPdf extends BasePdfTemplate
{
    protected Collection $memberPayments;

    protected array $summary;

    protected int $year;

    public function __construct(Collection $memberPayments, array $summary, int $year, string $locale = 'de')
    {
        $this->memberPayments = $memberPayments;
        $this->summary = $summary;
        $this->year = $year;

        parent::__construct($locale, __('pdf.membership_fees.title', ['year' => $year]));
    }

    public function generateContent(): void
    {
        $this->AddPage();
        $this->SetFont($this->font, '', 10);

        // Zusammenfassung
        $this->generateSummary();

        $this->Ln(5);

        // Tabelle
        $this->generateTable();
    }

    private function generateSummary(): void
    {
        $this->SetFont($this->font, 'B', 12);
        $this->Cell(0, 8, __('pdf.membership_fees.summary'), 0, 1);
        $this->Ln(2);

        $this->SetFont($this->font, '', 10);

        // 4-spaltige Übersicht
        $colWidth = 45;

        // Erste Zeile
        $this->SetFillColor(240, 240, 240);
        $this->Cell($colWidth, 6, __('pdf.membership_fees.members'), 1, 0, 'L', true);
        $this->Cell($colWidth, 6, __('pdf.membership_fees.booked'), 1, 0, 'L', true);
        $this->Cell($colWidth, 6, __('pdf.membership_fees.submitted'), 1, 0, 'L', true);
        $this->Cell(0, 6, __('pdf.membership_fees.transactions'), 1, 1, 'L', true);

        // Zweite Zeile - Werte
        $this->SetFont($this->font, 'B', 10);
        $this->Cell($colWidth, 8, $this->summary['total_members'], 1, 0, 'C');

        $this->SetTextColor(0, 128, 0);
        $this->Cell($colWidth, 8, $this->nf($this->summary['total_paid']).' €', 1, 0, 'R');

        $this->SetTextColor(255, 140, 0);
        $this->Cell($colWidth, 8, $this->nf($this->summary['total_pending']).' €', 1, 0, 'R');

        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 8, $this->summary['total_transactions'], 1, 1, 'C');

        // Unter-Info
        $this->SetFont($this->font, '', 8);
        $this->SetX(23 + $colWidth);
        $this->Cell($colWidth, 4, __('pdf.membership_fees.paid_bookings', ['count' => $this->summary['paid_count']]), 0, 0, 'R');
        $this->Cell($colWidth, 4, __('pdf.membership_fees.pending_bookings', ['count' => $this->summary['pending_count']]), 0, 1, 'R');
    }

    private function generateTable(): void
    {
        $this->SetFont($this->font, 'B', 11);
        $this->Cell(0, 8, __('pdf.membership_fees.members_detail'), 0, 1);
        $this->Ln(2);

        // Tabellen-Header
        $this->SetFont($this->font, 'B', 9);
        $this->SetFillColor(200, 200, 200);

        $this->Cell(60, 7, __('pdf.membership_fees.member'), 1, 0, 'L', true);
        $this->Cell(25, 7, __('pdf.membership_fees.payments'), 1, 0, 'C', true);
        $this->Cell(25, 7, __('pdf.membership_fees.date'), 1, 0, 'C', true);
        $this->Cell(30, 7, __('pdf.membership_fees.paid'), 1, 0, 'R', true);
        $this->Cell(30, 7, __('pdf.membership_fees.open'), 1, 1, 'R', true);

        // Tabellen-Inhalt
        $this->SetFont($this->font, '', 9);
        $fill = false;

        foreach ($this->memberPayments as $item) {
            // Prüfe ob neue Seite nötig
            if ($this->GetY() > 250) {
                $this->AddPage();
                // Header wiederholen
                $this->SetFont($this->font, 'B', 9);
                $this->SetFillColor(200, 200, 200);
                $this->Cell(60, 7, __('pdf.membership_fees.member'), 1, 0, 'L', true);
                $this->Cell(25, 7, __('pdf.membership_fees.payments'), 1, 0, 'C', true);
                $this->Cell(25, 7, __('pdf.membership_fees.date'), 1, 0, 'C', true);
                $this->Cell(30, 7, __('pdf.membership_fees.paid'), 1, 0, 'R', true);
                $this->Cell(30, 7, __('pdf.membership_fees.open'), 1, 1, 'R', true);
                $this->SetFont($this->font, '', 9);
            }

            $this->SetFillColor(245, 245, 245);

            // Member Name
            $memberName = $item->member->fullName();

            $this->MultiCell(60, 7, $memberName, 1, 'L', $fill, 0);

            // Typ
            $this->Cell(25, 7, $item->transaction_count, 1, 0, 'C', $fill);

            // Datum
            $date = \App\Helpers\DateHelper::formatDate($item->latest_transaction?->transaction?->date) ?: '-';
            $this->Cell(25, 7, $date, 1, 0, 'C', $fill);

            // Bezahlt (grün)
            //   $this->SetTextColor(0, 128, 0);
            $this->SetFont($this->font, '', 9);
            $this->Cell(30, 7, $this->nf($item->total_paid).' €', 1, 0, 'R', $fill);

            // Offen (orange)
            //   $this->SetTextColor(255, 140, 0);
            $openAmount = $item->total_pending > 0 ? $this->nf($item->total_pending).' €' : '-';
            $this->Cell(30, 7, $openAmount, 1, 1, 'R', $fill);

            $fill = ! $fill;
        }

        // Gesamtsummen Footer
        $this->Ln(3);
        $this->SetFont($this->font, 'B', 10);
        $this->SetFillColor(220, 220, 220);

        $this->Cell(110, 7, __('pdf.membership_fees.total'), 1, 0, 'R', true);

        //  $this->SetTextColor(0, 128, 0);
        $totalPaid = $this->memberPayments->sum('total_paid');
        $this->Cell(30, 7, $this->nf($totalPaid).' €', 1, 0, 'R', true);

        //    $this->SetTextColor(255, 140, 0);
        $totalPending = $this->memberPayments->sum('total_pending');
        $this->Cell(30, 7, $this->nf($totalPending).' €', 1, 1, 'R', true);

    }
}
