<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Enums\Gender;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use Carbon\Carbon;
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
        parent::__construct($locale, 'Quittung #'.$this->documentNumber);
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
        $this->Cell(0, 6, 'Berlin, der '.Carbon::today('Europe/Berlin')->locale(app()->getLocale())->isoFormat('DD. MMMM YYYY'), 0, 1, 'R');

        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 6, 'Quittung über den Zahlungseingang', 0, 1);

        $this->ln(20);
        $this->SetFont('helvetica', '', 11);

        if ($this->member->gender === Gender::ma) {
            $this->Cell(0, 6, 'Sehr geehrter Herr'.$this->member->name.',', 0, 1);
        } elseif ($this->member->gender == Gender::fe) {
            $this->Cell(0, 6, 'Sehr geehrterte Frau'.$this->member->name.',', 0, 1);
        } else {
            $this->Cell(0, 6, 'Guten Tag, '.$this->member->first_name.' '.$this->member->name.',', 0, 1);
        }

        $this->ln(4);

        $this->MultiCell(0, 5, 'hiermit bestätigen wir den Erhalt der folgenden Zahlung, für die wir uns herzlich bedanken:', 0, 'L');

        $this->ln(10);

        $this->SetFont('helvetica', '', 8);
        $this->Cell(60, 6, __('Erhalten am'), 'LTR', 0);
        $this->Cell(00, 6, __('Erhaltener Betrag'), 'LTR', 1);
        $this->SetFont('helvetica', '', 11);
        $this->Cell(60, 6, \App\Helpers\DateHelper::formatDate($this->transaction->date), 'LBR', 0);
        $this->Cell(0, 6, 'EUR '.$this->nf($this->transaction->amount_gross), 'LBR', 1);
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
        $this->Cell(0, 6, 'Wir danken Ihnen herzlich für Ihre Unterstützung.', 0, 1);
        $this->Cell(0, 6, 'Mit freundlichen Grüßen', 0, 1);
        $this->ln(2);
        $this->Cell(60, 5, 'Magyar-Kolónia Berlin (Ungarische-Kolonie-Berlin) e.V.', 0, 1);
        $this->ln(20);

        $this->SetFont('helvetica', '', 10);
        $this->Cell(100, 5, ' ', 0, 0);
        $this->Cell(60, 5, 'gez. Daniel Körtvélyessy / Kassenwart', 'B', 1);

    }
}
