<?php

declare(strict_types=1);

namespace App\Services\Accounting;

use App\Enums\TransactionType;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\User;
use App\Notifications\FiscalYearClosedNotification;
use App\Pdfs\AnnualReportPdf;
use App\Services\Accounting\AnnualReportService;
use App\Services\Accounting\Datev\DatevExportService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class FiscalYearService
{
    /**
     * Schließe ein Geschäftsjahr mit ausgewählten Transaktionen.
     *
     * @param  array<int>  $transactionIds
     *
     * @throws \RuntimeException wenn ein älteres FY noch offen ist (§ 4a EStG), Altdaten ohne FY existieren oder das FY bereits geschlossen ist
     */
    public function closeFiscalYearWithSelection(int $year, array $transactionIds, int $closedByUserId): FiscalYear
    {
        return DB::transaction(function () use ($year, $transactionIds, $closedByUserId) {
            $fiscalYear = FiscalYear::where('year', $year)->firstOrFail();

            if ($fiscalYear->isClosed()) {
                throw new \RuntimeException("Fiscal year {$year} is already closed.");
            }

            // Guard: Keine Lücken in der Abschlusskette
            $oldestOpen = FiscalYear::whereNull('closed_at')->orderBy('year')->first();
            if ($oldestOpen && $oldestOpen->id !== $fiscalYear->id) {
                throw new \RuntimeException(
                    "Fiscal year {$year} cannot be closed. The oldest open fiscal year ({$oldestOpen->year}) must be closed first."
                );
            }

            // Altdaten-Sicherheitsnetz: keine Buchungen ohne fiscal_year_id
            $orphanedCount = Transaction::whereNull('fiscal_year_id')
                ->whereYear('date', $year)
                ->count();
            if ($orphanedCount > 0) {
                throw new \RuntimeException(
                    "{$orphanedCount} transaction(s) in year {$year} have no fiscal year assigned. Please run the backfill migration first."
                );
            }

            if (empty($transactionIds)) {
                throw new \RuntimeException("No transactions selected for fiscal year {$year}.");
            }

            // Validiere dass alle Transaktionen zum Jahr gehören
            $validTransactions = Transaction::whereIn('id', $transactionIds)
                ->whereHas('fiscalYear', fn ($q) => $q->where('year', $year))
                ->unlocked()
                ->financialReportable()
                ->pluck('id');

            if ($validTransactions->count() !== count($transactionIds)) {
                throw new \RuntimeException('Some selected transactions are invalid or already locked.');
            }

            // Erstelle Pivot-Einträge mit Zeitstempel
            $pivotData = [];
            $now = now();

            foreach ($transactionIds as $transactionId) {
                $pivotData[$transactionId] = [
                    'locked_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $fiscalYear->transactions()->attach($pivotData);

            // Schließe das Geschäftsjahr
            $fiscalYear->update([
                'closed_at' => $now,
                'closed_by' => $closedByUserId,
            ]);

            return $fiscalYear->fresh();
        });
    }

    /**
     * Schließe ein Geschäftsjahr mit allen darin enthaltenen, noch nicht
     * gesperrten Buchungen.
     *
     * @throws \RuntimeException wenn ein älteres FY noch offen ist, Altdaten ohne FY existieren oder das FY bereits geschlossen ist
     */
    public function closeFiscalYear(int $year, int $closedByUserId): FiscalYear
    {
        return DB::transaction(function () use ($year, $closedByUserId) {
            $fiscalYear = FiscalYear::where('year', $year)->firstOrFail();

            if ($fiscalYear->isClosed()) {
                throw new \RuntimeException("Fiscal year {$year} is already closed.");
            }

            // Guard: Keine Lücken in der Abschlusskette
            $oldestOpen = FiscalYear::whereNull('closed_at')->orderBy('year')->first();
            if ($oldestOpen && $oldestOpen->id !== $fiscalYear->id) {
                throw new \RuntimeException(
                    "Fiscal year {$year} cannot be closed. The oldest open fiscal year ({$oldestOpen->year}) must be closed first."
                );
            }

            // Altdaten-Sicherheitsnetz: keine Buchungen ohne fiscal_year_id
            $orphanedCount = Transaction::whereNull('fiscal_year_id')
                ->whereYear('date', $year)
                ->count();
            if ($orphanedCount > 0) {
                throw new \RuntimeException(
                    "{$orphanedCount} transaction(s) in year {$year} have no fiscal year assigned. Please run the backfill migration first."
                );
            }

            // Hole alle Transaktionen des Jahres, die noch nicht gesperrt sind
            $transactions = Transaction::whereHas('fiscalYear', fn ($q) => $q->where('year', $year))
                ->unlocked()
                ->financialReportable()
                ->get();

            if ($transactions->isEmpty()) {
                throw new \Exception("No transactions found for fiscal year {$year}.");
            }

            // Erstelle Pivot-Einträge mit Zeitstempel
            $pivotData = [];
            $now = now();

            foreach ($transactions as $transaction) {
                $pivotData[$transaction->id] = [
                    'locked_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $fiscalYear->transactions()->attach($pivotData);

            // Schließe das Geschäftsjahr
            $fiscalYear->update([
                'closed_at' => $now,
                'closed_by' => $closedByUserId,
            ]);

            return $fiscalYear->fresh();
        });
    }

    /**
     * Öffne ein geschlossenes Geschäftsjahr wieder
     */
    public function reopenFiscalYear(int $year): FiscalYear
    {
        return DB::transaction(function () use ($year) {
            $fiscalYear = FiscalYear::where('year', $year)->firstOrFail();

            if ($fiscalYear->isOpen()) {
                throw new \Exception("Fiscal year {$year} is already open.");
            }

            // Entferne die Sperrungen
            $fiscalYear->transactions()->detach();

            // Öffne das Geschäftsjahr wieder
            $fiscalYear->update([
                'closed_at' => null,
                'closed_by' => null,
            ]);

            return $fiscalYear->fresh();
        });
    }

    /**
     * Erzeugt DATEV-Export + Jahresbericht-PDF nach dem Schließen eines Geschäftsjahres.
     * Läuft synchron im HTTP-Request, außerhalb der DB-Transaction.
     *
     * @return array{datev_success: bool, pdf_success: bool, datev_path: string|null, annual_report_path: string|null}
     */
    public function generatePostCloseReports(FiscalYear $fiscalYear): array
    {
        $fiscalYear->load('bookingAccountType');

        $datevSuccess = false;
        $datevPath = null;
        $pdfSuccess = false;
        $annualReportPath = null;

        try {
            $datevPath = app(DatevExportService::class)->export($fiscalYear);
            $datevSuccess = true;
        } catch (\Throwable $e) {
            Log::error('FiscalYearService: DATEV-Export fehlgeschlagen', [
                'year' => $fiscalYear->year,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $annualReportPath = $this->generateAnnualReport($fiscalYear->year);

            $fiscalYear->withoutEvents(function () use ($fiscalYear, $annualReportPath): void {
                $fiscalYear->update(['annual_report_path' => $annualReportPath]);
            });

            $pdfSuccess = true;
        } catch (\Throwable $e) {
            Log::error('FiscalYearService: Jahresbericht-Generierung fehlgeschlagen', [
                'year' => $fiscalYear->year,
                'error' => $e->getMessage(),
            ]);
        }

        $this->notifyAccountants($fiscalYear, $datevPath, $annualReportPath);

        return [
            'datev_success' => $datevSuccess,
            'pdf_success' => $pdfSuccess,
            'datev_path' => $datevPath,
            'annual_report_path' => $annualReportPath,
        ];
    }

    private function generateAnnualReport(int $year): string
    {
        $data = app(AnnualReportService::class)->build($year);
        $filename = 'Jahresbericht-'.$year.'-'.now()->format('Ymd').'.pdf';

        $pdf = new AnnualReportPdf(
            year: $data['year'],
            snapshot: $data['snapshot'],
            transactions: $data['transactions'],
        );
        $pdf->generateContent();

        $pdfContent = $pdf->Output($filename, 'S');

        $path = "reports/annual/{$year}/{$filename}";
        Storage::disk('local')->put($path, $pdfContent);

        return $path;
    }

    private function notifyAccountants(FiscalYear $fiscalYear, ?string $datevPath, ?string $annualReportPath): void
    {
        $exportPath = $datevPath ?? $annualReportPath ?? '';

        $notification = new FiscalYearClosedNotification($fiscalYear, $exportPath, $annualReportPath);

        $recipients = $this->resolveNotificationRecipients();

        foreach ($recipients as $notifiable) {
            $notifiable->notify($notification);
        }
    }

    private function resolveNotificationRecipients(): \Illuminate\Support\Collection
    {
        return Member::getAccountants();
    }

    /**
     * @return array{fiscal_year: FiscalYear, metadata: array, transactions: \Illuminate\Support\Collection, summary: array}
     */
    public function getSnapshot(int $year): array
    {
        $fiscalYear = FiscalYear::where('year', $year)
            ->with(['closedBy', 'openedBy'])
            ->firstOrFail();

        $transactions = $fiscalYear->transactions()
            ->with(['account', 'member_transaction', 'event_transaction'])
            ->financialReportable()
            ->get();

        // Cache gefilterte Collections
        $incomeTransactions = $transactions->where('type', TransactionType::Deposit);
        $expenseTransactions = $transactions->where('type', TransactionType::Withdrawal);

        return [
            'fiscal_year' => $fiscalYear,
            'metadata' => [
                'year' => $fiscalYear->year,
                'opened_at' => $fiscalYear->opened_at,
                'closed_at' => $fiscalYear->closed_at,
                'opened_by' => $fiscalYear->openedBy?->name,
                'closed_by' => $fiscalYear->closedBy?->name,
                'is_closed' => $fiscalYear->isClosed(),
            ],
            'transactions' => $transactions->map(function (Transaction $transaction): array {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->date,
                    'label' => $transaction->label,
                    'amount' => $transaction->amount_gross,
                    'type' => $transaction->type,
                    'booking_account' => $transaction->bookingAccount?->number,
                    'status' => $transaction->status,
                    'locked_at' => $transaction->pivot->locked_at ?? null,
                ];
            }),
            'summary' => [
                'total_income' => $incomeTransactions->sum('amount_gross'),
                'total_expense' => $expenseTransactions->sum('amount_gross'),
                'balance' => $incomeTransactions->sum('amount_gross') -
                    $expenseTransactions->sum('amount_gross'),
                'transaction_count' => $transactions->count(),
            ],
        ];
    }
}
