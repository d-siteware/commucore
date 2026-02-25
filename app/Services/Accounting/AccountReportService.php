<?php

declare(strict_types=1);

namespace App\Services\Accounting;

use App\Models\Accounting\AccountReport;
use App\Pdfs\AccountReportPdf;
use Carbon\Carbon;

final class AccountReportService
{
    public function generate(AccountReport $accountReport): string
    {

        $dateString = Carbon::createFromTimeString($accountReport->period_start->format('Y-m-d'))->format('Y-m');

        $filename = 'Kassenbericht-'.$dateString.'.pdf';

        $pdf = new AccountReportPdf(
            $accountReport,
            app()->getLocale(),
            $filename
        );
        $pdf->generateContent();

        return $pdf->generatePdf($filename);

    }
}
