<?php

declare(strict_types=1);

// -----------------------------------------------------------------------
// Füge dies in deinen bestehenden FiscalYearObserver ein (oder passe ihn an)
// -----------------------------------------------------------------------
// In der Methode, die nach dem DATEV-Export aufgerufen wird, z.B.:
//
//   public function closed(FiscalYear $fiscalYear): void
//   {
//       $path = app(DatevExportService::class)->export($fiscalYear);
//       $this->notifyAccountants($fiscalYear, $path);
//   }
// -----------------------------------------------------------------------

namespace App\Observers;

use App\Models\Accounting\FiscalYear;
use App\Models\User;
use App\Notifications\FiscalYearClosedNotification;
use App\Services\Accounting\Datev\DatevExportService;
use Illuminate\Support\Facades\Log;

// Beispiel-Observer – passe die Klasse an deinen vorhandenen Observer an
final class FiscalYearObserver
{
    public function __construct(private readonly DatevExportService $datevExportService) {}

    /**
     * Wird aufgerufen, wenn ein FiscalYear als geschlossen markiert wird.
     * Passe den Event-Hook (updated/saved/etc.) an deinen bestehenden Observer an.
     */
    public function updated(FiscalYear $fiscalYear): void
    {
        // Nur reagieren, wenn gerade auf "closed" gewechselt wurde
        if (! $fiscalYear->wasChanged('closed_at') || ! $fiscalYear->closed_at) {
            return;
        }

        try {
            $path = $this->datevExportService->export($fiscalYear);
            $this->notifyAccountants($fiscalYear, $path);
        } catch (\Throwable $e) {
            Log::error('FiscalYearObserver: DATEV-Export fehlgeschlagen', [
                'year' => $fiscalYear->year,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyAccountants(FiscalYear $fiscalYear, string $path): void
    {
        // Alle User mit der Berechtigung can_manage_accounting benachrichtigen

        //            ->each(function (User $user) use ($fiscalYear, $path) {
        //                $user->notify(new FiscalYearClosedNotification($fiscalYear, $path));
        //            });
    }
}
