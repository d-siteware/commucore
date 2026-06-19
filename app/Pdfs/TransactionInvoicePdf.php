<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Enums\Gender;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use Carbon\Carbon;
use App\Helpers\MoneyHelper;
use Illuminate\Support\Str;

final class TransactionInvoicePdf extends BasePdfTemplate
{
    protected Transaction $transaction;

    protected ?Member $member;

    protected string $documentNumber;

    public function __construct(Transaction $transaction, ?Member $member = null, string $locale = 'de')
    {
        $this->transaction = $transaction;
        $this->member = $member;
        $this->documentNumber = Str::padLeft(''.$transaction->id, 6, '0');
        parent::__construct($locale, __('pdf.invoice.receipt_title').' #'.$this->documentNumber);
    }

    public function generateContent(): void
    {
        $this->AddPage();

        // Set font for the content

        $this->ln(20);
        $this->setFont('helvetica', '', 9);
        $this->cell(0, 3, setting('organization.name'), 'B', 1);

        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 6, $this->member->fullName(), 0, 1);
        $this->Cell(0, 6, $this->member->address, 0, 1);
        $this->Cell(0, 6, $this->member->zip.' '.$this->member->city, 0, 1);

        $this->ln(20);
        $this->Cell(0, 6, 'Berlin, '.__('pdf.invoice.receipt_location').' '.Carbon::today('Europe/Berlin')->locale(app()->getLocale())->isoFormat('DD. MMMM YYYY'), 0, 1, 'R');

        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 6, __('pdf.invoice.receipt_title'), 0, 1);

        $this->ln(20);
        $this->SetFont('helvetica', '', 11);

        if ($this->member->gender === Gender::ma) {
            $this->Cell(0, 6, __('pdf.invoice.receipt_salutation_male').' '.$this->member->name.',', 0, 1);
        } elseif ($this->member->gender == Gender::fe) {
            $this->Cell(0, 6, __('pdf.invoice.receipt_salutation_female').' '.$this->member->name.',', 0, 1);
        } else {
            $this->Cell(0, 6, __('pdf.invoice.receipt_salutation_neutral').' '.$this->member->first_name.' '.$this->member->name.',', 0, 1);
        }

        $this->ln(4);

        $this->MultiCell(0, 5, __('pdf.invoice.receipt_body'), 0, 'L');

        $this->ln(10);

        $this->SetFont('helvetica', '', 8);
        $this->Cell(60, 6, __('Erhalten am'), 'LTR', 0);
        $this->Cell(0, 6, __('Erhaltener Betrag'), 'LTR', 1);
        $this->SetFont('helvetica', '', 11);
        $this->Cell(60, 6, \App\Helpers\DateHelper::formatDate($this->transaction->date), 'LBR', 0);
        $this->Cell(0, 6, MoneyHelper::getCurrencySymbol().' '.$this->nf($this->transaction->amount_gross), 'LBR', 1);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 6, __('Betreff'), 'LTR', 1);
        $this->SetFont('helvetica', '', 11);
        $this->MultiCell(0, 6, $this->transaction->label, 'LBR', 'L', false, 1);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 6, __('Referenz'), 'LTR', 1);
        $this->SetFont('helvetica', '', 11);
        $this->MultiCell(0, 6, $this->transaction->reference ?? '-', 'LBR', 'L', false, 1);

        $this->ln(10);
        $this->SetFont('helvetica', '', 11);
        $this->Cell(0, 6, __('pdf.invoice.receipt_thanks'), 0, 1);
        $this->Cell(0, 6, __('pdf.invoice.receipt_regards'), 0, 1);
        $this->ln(2);
        $this->Cell(60, 5, 'Magyar-Kolónia Berlin (Ungarische-Kolonie-Berlin) e.V.', 0, 1);
        $this->ln(20);

        $this->SetFont('helvetica', '', 10);
        $this->Cell(100, 5, ' ', 0, 0);
        $this->Cell(60, 5, __('pdf.invoice.receipt_signature').' Daniel Körtvélyessy / '.__('pdf.invoice.receipt_treasurer'), 'B', 1);

    }
}
