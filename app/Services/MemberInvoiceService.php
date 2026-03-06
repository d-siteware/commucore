<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Pdfs\TransactionInvoicePdf;

final class MemberInvoiceService
{
    public function generate(Transaction $transaction, ?Member $member = null, string $locale = 'de'): string
    {
        $filename = 'Rechnung-'.$transaction->id.'.pdf';

        $pdf = new TransactionInvoicePdf($transaction, $member, $locale);
        $pdf->generateContent(); // 'S' to get as string

        return $pdf->Output($filename, 'S');
    }
}
