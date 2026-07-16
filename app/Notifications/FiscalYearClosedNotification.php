<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Accounting\FiscalYear;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class FiscalYearClosedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly FiscalYear $fiscalYear,
        private readonly string $exportPath,
        private readonly ?string $annualReportPath = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $parts = ["Geschäftsjahr {$this->fiscalYear->year} wurde abgeschlossen."];

        if ($this->exportPath !== '') {
            $parts[] = 'DATEV-Export wurde erstellt.';
        }

        if ($this->annualReportPath !== null) {
            $parts[] = 'Jahresbericht wurde erstellt.';
        }

        return [
            'type' => 'fiscal_year_closed',
            'year' => $this->fiscalYear->year,
            'datev_path' => $this->exportPath,
            'annual_report_path' => $this->annualReportPath,
            'message' => implode(' ', $parts),
            'closed_at' => now()->toIso8601String(),
        ];
    }
}
