<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;

final class SepaMandatePdf extends BasePdfTemplate
{
    private Member $member;

    private SepaMandate $mandate;

    private string $creditorName;

    private string $creditorId;

    private int $periodAmount;

    private string $intervalLabel;

    private int $periodsPerYear;

    public function __construct(
        Member $member,
        SepaMandate $mandate,
        string $creditorName,
        string $creditorId,
        int $periodAmount,
        string $intervalLabel,
        int $periodsPerYear,
        string $locale = 'de',
    ) {
        $this->member = $member;
        $this->mandate = $mandate;
        $this->creditorName = $creditorName;
        $this->creditorId = $creditorId;
        $this->periodAmount = $periodAmount;
        $this->intervalLabel = $intervalLabel;
        $this->periodsPerYear = $periodsPerYear;

        parent::__construct($locale, __('pdf.mandate.title'));
    }

    public function generateContent(): void
    {
        $this->AddPage();

        $this->SetFont($this->font, 'B', 14);
        $this->Cell(0, 8, __('pdf.mandate.title'), 0, 1, 'L');
        $this->ln(4);

        $this->SetFont($this->font, '', 10);
        $this->Cell(0, 6, 'Gläubiger-ID: '.$this->creditorId, 0, 1, 'L');
        $this->Cell(0, 6, 'Mandatsreferenz: '.$this->mandate->mandate_reference, 0, 1, 'L');
        $this->ln(8);

        $this->SetFont($this->font, 'B', 11);
        $this->Cell(0, 7, __('pdf.mandate.debtor').':', 0, 1, 'L');
        $this->ln(2);

        $this->SetFont($this->font, '', 10);
        $this->Cell(0, 6, __('pdf.mandate.name').': '.$this->mandate->account_holder, 0, 1, 'L');
        $this->Cell(0, 6, __('pdf.mandate.street').': '.($this->member->address ?? ''), 0, 1, 'L');
        $this->Cell(0, 6, __('pdf.mandate.zip_city').': '.($this->member->zip ?? '').' '.($this->member->city ?? ''), 0, 1, 'L');
        $this->Cell(0, 6, __('pdf.mandate.country').': '.($this->member->country ?? 'Deutschland'), 0, 1, 'L');
        $this->ln(4);

        $this->Cell(0, 6, 'IBAN: '.$this->mandate->iban, 0, 1, 'L');
        if ($this->mandate->bic) {
            $this->Cell(0, 6, 'BIC: '.$this->mandate->bic, 0, 1, 'L');
        }
        $this->ln(8);

        $this->SetFont($this->font, 'B', 11);
        $this->Cell(0, 7, __('pdf.mandate.creditor').':', 0, 1, 'L');
        $this->ln(2);

        $this->SetFont($this->font, '', 10);
        $this->Cell(0, 6, $this->creditorName, 0, 1, 'L');
        $this->ln(10);

        $this->SetFont($this->font, 'B', 10);
        $this->MultiCell(0, 5, __('pdf.mandate.mandate_text'), 0, 'L');
        $this->ln(4);

        $this->SetFont($this->font, '', 9);
        $mandateType = $this->mandate->mandate_type->isCore() ? __('pdf.mandate.mandate_type_core') : __('pdf.mandate.mandate_type_b2b');
        $this->MultiCell(0, 5, __('pdf.mandate.mandate_type').': '.$mandateType, 0, 'L');
        $this->ln(2);

        if ($this->mandate->mandate_type->isCore()) {
            $this->MultiCell(0, 5, __('pdf.mandate.hint').': '.__('pdf.mandate.hint_core'), 0, 'L');
        } else {
            $this->MultiCell(0, 5, __('pdf.mandate.hint').': '.__('pdf.mandate.hint_b2b'), 0, 'L');
        }
        $this->ln(4);

        // ─── Beitragsinformation (informativ) ──────────────────────────────────────
        $this->SetFont($this->font, 'B', 9);
        $this->Cell(0, 6, __('pdf.mandate.fee_info'), 0, 1, 'L');
        $this->ln(1);

        $this->SetFont($this->font, '', 9);
        $amountStr = \App\Helpers\MoneyHelper::formatCents($this->periodAmount);
        $this->Cell(0, 5, __('pdf.mandate.fee_amount').': '.$amountStr, 0, 1, 'L');
        $this->Cell(0, 5, __('pdf.mandate.fee_interval').': '.$this->intervalLabel, 0, 1, 'L');
        $this->Cell(0, 5, __('pdf.mandate.fee_per_year').': '.$this->periodsPerYear, 0, 1, 'L');
        $this->ln(2);

        $this->SetFont($this->font, 'I', 8);
        $this->MultiCell(0, 4, __('pdf.mandate.fee_hint'), 0, 'L');
        $this->ln(8);

        $this->Cell(0, 6, __('pdf.mandate.location_date').': _______________________', 0, 1, 'L');
        $this->ln(10);
        $this->Cell(0, 6, __('pdf.mandate.signature').': _______________________', 0, 1, 'L');
    }
}
