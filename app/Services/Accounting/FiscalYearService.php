<?php

declare(strict_types=1);

namespace App\Services\Accounting;

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use Illuminate\Support\Facades\DB;

final class FiscalYearService
{
    /**
     * Schließe ein Geschäftsjahr
     */
    public function closeFiscalYear(int $year, int $closedByUserId): FiscalYear
    {
        return DB::transaction(function () use ($year, $closedByUserId) {
            $fiscalYear = FiscalYear::where('year', $year)->firstOrFail();

            if ($fiscalYear->isClosed()) {
                throw new \Exception("Fiscal year {$year} is already closed.");
            }

            // Hole alle Transaktionen des Jahres, die noch nicht gesperrt sind
            $transactions = Transaction::whereYear('date', $year)
                ->unlocked($year)
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
     * Exportiere einen Snapshot des Geschäftsjahres
     *
     * @return array{fiscal_year: FiscalYear, metadata: array, transactions: \Illuminate\Support\Collection, summary: array}
     */
    public function getSnapshot(int $year): array
    {
        $fiscalYear = FiscalYear::where('year', $year)
            ->with(['closedBy', 'openedBy'])
            ->firstOrFail();

        /**
         * @var \Illuminate\Database\Eloquent\Collection<int, Transaction> $transactions
         */
        $transactions = $fiscalYear->transactions()
            ->with(['account', 'member_transaction', 'event_transaction'])
            ->get();

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
                    'status' => $transaction->status,
                    'locked_at' => $transaction->pivot->locked_at ?? null,
                ];
            }),
            'summary' => [
                'total_income' => $transactions->where('type', 'income')->sum('amount_gross'),
                'total_expense' => $transactions->where('type', 'expense')->sum('amount_gross'),
                'balance' => $transactions->where('type', 'income')->sum('amount_gross') -
                    $transactions->where('type', 'expense')->sum('amount_gross'),
                'transaction_count' => $transactions->count(),
            ],
        ];
    }
}
