<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Enums\Gender;
use App\Models\Event\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class EventReportPdf extends BasePdfTemplate
{
    public function __construct(
        public Event $event,
        public float $total_income,
        public Collection $income_list,
        public float $total_spending,
        public Collection $spending_list,
        public Collection $visitors,
        public string $locale,
        public string $filename
    ) {
        parent::__construct($locale, __('report.event.title')); // Pass locale & title

        // Set document metadata
        $this->SetTitle(__('report.event.title'));
        $this->SetSubject(__('report.event.visitor.name'));
    }

    public function generateContent(): string
    {
        $hH1 = 14;
        $h = 9;
        $this->AddPage();
        $this->SetFont('helvetica', '', $h);
        //        $this->writeHTML($this->content, true, false, true, false, '');

        $this->SetFont('helvetica', '', $hH1);
        $this->Cell(0, 10, 'Finanzen ', 0, 1);
        $this->ln(2);

        $this->SetFont('helvetica', '', $h);
        $this->Cell(60, 8, 'Einnahmen ', 0, 0);
        $this->Cell(80, 8, \App\Helpers\MoneyHelper::formatCents((int) ($this->total_income * 100)), 0, 1, 'R');
        $this->Cell(60, 8, 'Ausgaben ', 0, 0);
        $this->Cell(80, 8, \App\Helpers\MoneyHelper::formatCents((int) ($this->total_spending * 100)), 0, 1, 'R');
        $this->Cell(60, 8, 'Gesamt ', 'T', 0);
        $this->Cell(80, 8, \App\Helpers\MoneyHelper::formatCents((int) (($this->total_income - $this->total_spending) * 100)), 'T', 1, 'R');
        $this->ln(10);
        $this->SetFont('helvetica', '', $hH1);
        $this->Cell(0, 10, 'Besucher ', 0, 1);
        $this->ln(2);

        $this->SetFont('helvetica', '', $h);
        $this->Cell(30, 8, 'Gesamt : ', 0, 0);
        $this->Cell(50, 8, ''.$this->visitors->count(), 0, 1, 'R');
        $this->Cell(30, 8, 'Männlich ', 0, 0);
        $this->Cell(50, 8, '30', 0, 1, 'R');
        $this->Cell(30, 8, 'Weiblich ', 0, 0);
        $this->Cell(50, 8, '30', 0, 1, 'R');

        $this->ln(5);
        $this->Cell(30, 8, 'Mitglieder ', 0, 0);
        $this->Cell(50, 8, '30', 0, 1, 'R');
        $this->Cell(30, 8, 'Über die Webseite angemeldet ', 0, 0);
        $this->Cell(50, 8, '30', 0, 1, 'R');

        $this->setY(-50);

        $this->Cell(30, 8, 'Datum ', 'T', 0);
        $this->Cell(50, 8, 'Kassenwart', 'T', 1, 'C');

        $this->AddPage();

        $this->SetFont('helvetica', '', $hH1);
        $this->Cell(0, 10, 'Details ', 0, 1);
        $this->ln(2);

        $this->SetFont('helvetica', '', $hH1);
        $this->Cell(0, 10, 'Einnahmen ', 0, 1);
        $this->ln(2);

        $w = 30;

        $wText = 66;
        $wReferenz = 45;
        $wStatus = 20;
        $wKonto = 25;

        $this->SetFont('helvetica', '', 8);
        $this->Cell($wText, 8, 'Text', 'B', 0);
        $this->Cell($wReferenz, 8, 'Referenz', 'B', 0);
        $this->Cell($wStatus, 8, 'Status', 'B', 0);
        $this->Cell($wKonto, 8, 'Konto', 'B', 0);
        $this->Cell(0, 8, 'Betrag', 'B', 1, 'R');

        $this->SetFont('helvetica', '', $h);
        foreach ($this->income_list as $item) {
            $this->Cell($wText, 8, Str::limit($item->transaction->label, 60), 'B', 0);
            $this->Cell($wReferenz, 8, $item->transaction->reference, 'B', 0);
            $this->Cell($wStatus, 8, $item->transaction->status, 'B', 0);
            $this->Cell($wKonto, 8, $item->transaction->account->name, 'B', 0);
            $this->Cell(0, 8, \App\Helpers\MoneyHelper::formatCents($item->transaction->amount_gross, withSymbol: false), 'B', 1, 'R');
        }
        $this->ln(10);
        $this->SetFont('helvetica', '', $hH1);
        $this->Cell(0, 10, 'Ausgaben ', 0, 1);
        $this->ln(2);

        $this->SetFont('helvetica', '', 8);
        $this->Cell($wText, 8, 'Text', 'B', 0);
        $this->Cell($wReferenz, 8, 'Referenz', 'B', 0);
        $this->Cell($wStatus, 8, 'Status', 'B', 0);
        $this->Cell($wKonto, 8, 'Konto', 'B', 0);
        $this->Cell(0, 8, 'Betrag', 'B', 1, 'R');

        $this->SetFont('helvetica', '', $h);
        foreach ($this->spending_list as $item) {
            $this->Cell($wText, 8, Str::limit($item->transaction->label, 60), 'B', 0);
            $this->Cell($wReferenz, 8, $item->transaction->reference, 'B', 0);
            $this->Cell($wStatus, 8, $item->transaction->status, 'B', 0);
            $this->Cell($wKonto, 8, $item->transaction->account->name, 'B', 0);
            $this->Cell(0, 8, \App\Helpers\MoneyHelper::formatCents($item->transaction->amount_gross, withSymbol: false), 'B', 1, 'R');
        }

        $this->ln(10);
        $this->SetFont('helvetica', '', $hH1);
        $this->Cell(0, 10, 'Besucher ', 0, 1);
        $this->ln(2);

        $ws = 10;
        $this->SetFont('helvetica', '', 8);
        $this->Cell(50, 8, 'Name', 'B', 0);
        $this->Cell(60, 8, 'E-Mail', 'B', 0);
        $this->Cell($ws, 8, 'MI', 'B', 0);
        $this->Cell($ws, 8, 'AN', 'B', 0);
        $this->Cell($ws, 8, 'MA', 'B', 0);
        $this->Cell($ws, 8, 'FE', 'B', 1, 'R');

        $this->SetFont('helvetica', '', $h);
        foreach ($this->visitors as $visitor) {
            $this->Cell(50, 8, $visitor->name, 'B', 0);
            $this->Cell(60, 8, $visitor->email, 'B', 0);
            $this->Cell($ws, 8, $visitor->member ? 'x' : '', 'B', 0, 'C');
            $this->Cell($ws, 8, $visitor->subscription ? 'x' : '', 'B', 0, 'C');
            $this->Cell($ws, 8, $visitor->gender === Gender::ma ? 'x' : '', 'B', 0, 'C');
            $this->Cell($ws, 8, $visitor->gender === Gender::fe ? 'x' : '', 'B', 1, 'C');
        }

        return $this->Output($this->filename); // 'D' = Download, 'I' = Inline
    }
}
