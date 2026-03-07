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
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'fiscal_year_closed',
            'year' => $this->fiscalYear->year,
            'export_path' => $this->exportPath,
            'message' => "Geschäftsjahr {$this->fiscalYear->year} wurde abgeschlossen. DATEV-Export wurde erstellt.",
            'closed_at' => now()->toIso8601String(),
        ];
    }
}
