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

    public function __construct(
        Member $member,
        SepaMandate $mandate,
        string $creditorName,
        string $creditorId,
        string $locale = 'de',
    ) {
        $this->member = $member;
        $this->mandate = $mandate;
        $this->creditorName = $creditorName;
        $this->creditorId = $creditorId;

        parent::__construct($locale, 'SEPA-Lastschriftmandat');
    }

    public function generateContent(): void
    {
        $this->AddPage();

        $this->SetFont($this->font, 'B', 14);
        $this->Cell(0, 8, 'SEPA-Lastschriftmandat', 0, 1, 'L');
        $this->ln(4);

        $this->SetFont($this->font, '', 10);
        $this->Cell(0, 6, 'Gläubiger-ID: '.$this->creditorId, 0, 1, 'L');
        $this->Cell(0, 6, 'Mandatsreferenz: '.$this->mandate->mandate_reference, 0, 1, 'L');
        $this->ln(8);

        $this->SetFont($this->font, 'B', 11);
        $this->Cell(0, 7, 'Zahlungspflichtiger:', 0, 1, 'L');
        $this->ln(2);

        $this->SetFont($this->font, '', 10);
        $this->Cell(0, 6, 'Name: '.$this->mandate->account_holder, 0, 1, 'L');
        $this->Cell(0, 6, 'Straße: '.($this->member->address ?? ''), 0, 1, 'L');
        $this->Cell(0, 6, 'PLZ/Ort: '.($this->member->zip ?? '').' '.($this->member->city ?? ''), 0, 1, 'L');
        $this->Cell(0, 6, 'Land: '.($this->member->country ?? 'Deutschland'), 0, 1, 'L');
        $this->ln(4);

        $this->Cell(0, 6, 'IBAN: '.$this->mandate->iban, 0, 1, 'L');
        if ($this->mandate->bic) {
            $this->Cell(0, 6, 'BIC: '.$this->mandate->bic, 0, 1, 'L');
        }
        $this->ln(8);

        $this->SetFont($this->font, 'B', 11);
        $this->Cell(0, 7, 'Gläubiger:', 0, 1, 'L');
        $this->ln(2);

        $this->SetFont($this->font, '', 10);
        $this->Cell(0, 6, $this->creditorName, 0, 1, 'L');
        $this->ln(10);

        $this->SetFont($this->font, 'B', 10);
        $this->MultiCell(0, 5, 'Ich ermächtige den Gläubiger, Zahlungen von meinem Konto mittels Lastschrift einzuziehen. Zugleich weise ich mein Kreditinstitut an, die vom Gläubiger auf mein Konto gezogenen Lastschriften einzulösen.', 0, 'L');
        $this->ln(4);

        $this->SetFont($this->font, '', 9);
        $mandateType = $this->mandate->mandate_type->isCore() ? 'Basis-Lastschrift (CORE)' : 'Firmen-Lastschrift (B2B)';
        $this->MultiCell(0, 5, 'Art der Lastschrift: '.$mandateType, 0, 'L');
        $this->ln(2);

        $this->MultiCell(0, 5, 'Hinweis: Ich kann innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem Kreditinstitut vereinbarten Bedingungen.', 0, 'L');
        $this->ln(12);

        $this->Cell(0, 6, 'Ort, Datum: _______________________', 0, 1, 'L');
        $this->ln(10);
        $this->Cell(0, 6, 'Unterschrift: _______________________', 0, 1, 'L');
    }
}
