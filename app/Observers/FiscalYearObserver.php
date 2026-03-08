<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Accounting\FiscalYear;
use App\Models\User;
use App\Notifications\FiscalYearClosedNotification;
use App\Services\Accounting\AnnualReportService;
use App\Services\Accounting\Datev\DatevExportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class FiscalYearObserver
{
    public function __construct(
        private readonly DatevExportService $datevExportService,
        private readonly AnnualReportService $annualReportService,
    ) {}

    public function updated(FiscalYear $fiscalYear): void
    {
        if (! $fiscalYear->wasChanged('closed_at') || $fiscalYear->closed_at === null) {
            return;
        }

        $datevPath = null;
        $annualReportPath = null;

        // --- DATEV-Export ---
        try {
            $datevPath = $this->datevExportService->export($fiscalYear);
        } catch (\Throwable $e) {
            Log::error('FiscalYearObserver: DATEV-Export fehlgeschlagen', [
                'year' => $fiscalYear->year,
                'error' => $e->getMessage(),
            ]);
        }

        // --- Jahresbericht generieren & speichern ---
        try {
            $annualReportPath = $this->generateAndStoreAnnualReport($fiscalYear);

            $fiscalYear->withoutEvents(function () use ($fiscalYear, $annualReportPath): void {
                $fiscalYear->update(['annual_report_path' => $annualReportPath]);
            });
        } catch (\Throwable $e) {
            Log::error('FiscalYearObserver: Jahresbericht-Generierung fehlgeschlagen', [
                'year' => $fiscalYear->year,
                'error' => $e->getMessage(),
            ]);
        }

        // --- Benachrichtigung ---
        $this->notifyAccountants($fiscalYear, $datevPath, $annualReportPath);
    }

    /**
     * Generiert den Jahresbericht als PDF und speichert ihn im Storage.
     * Gibt den Storage-Pfad zurück.
     */
    private function generateAndStoreAnnualReport(FiscalYear $fiscalYear): string
    {
        $data = $this->annualReportService->build($fiscalYear->year);
        $filename = "Jahresbericht-{$fiscalYear->year}-".now()->format('Ymd').'.pdf';

        $pdf = new \App\Pdfs\AnnualReportPdf(
            year: $data['year'],
            snapshot: $data['snapshot'],
            transactions: $data['transactions'],
        );
        $pdf->generateContent();

        $pdfContent = $pdf->Output($filename, 'S');

        $path = "reports/annual/{$fiscalYear->year}/{$filename}";
        Storage::disk('local')->put($path, $pdfContent);

        return $path;
    }

    private function notifyAccountants(FiscalYear $fiscalYear, ?string $datevPath, ?string $annualReportPath): void
    {
        // Alle User mit der Berechtigung can_manage_accounting benachrichtigen
        // User::permission('can_manage_accounting')
        //     ->each(function (User $user) use ($fiscalYear, $datevPath, $annualReportPath): void {
        //         $user->notify(new FiscalYearClosedNotification($fiscalYear, $datevPath, $annualReportPath));
        //     });
    }
}
