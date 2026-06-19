<?php

declare(strict_types=1);

namespace App\Services\Accounting;

use App\Enums\BookingAccountArea;
use App\Enums\TransactionType;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Funding\Funding;
use App\Models\Project\Project;
use Illuminate\Support\Collection;

/**
 * Bereitet alle Daten für den AnnualReportPdf auf.
 *
 * Aufruf:
 *   $data = (new AnnualReportService)->build(year: 2024);
 *   $pdf  = new AnnualReportPdf(
 *               year:         $data['year'],
 *               snapshot:     $data['snapshot'],
 *               transactions: $data['transactions'],
 *           );
 */
final class AnnualReportService
{
    public function build(int $year): array
    {
        $transactions = $this->loadTransactions($year);

        return [
            'year' => $year,
            'snapshot' => $this->buildSnapshot($year, $transactions),
            'transactions' => $transactions,
        ];
    }

    // =========================================================================
    // Data loading
    // =========================================================================

    private function loadTransactions(int $year): Collection
    {
        return Transaction::query()
            ->with([
                'bookingAccount',
                'event_transaction.event',
                'project_transaction.project',
                'funding_transaction.funding',
            ])
            ->whereYearEquals($year)
            ->orderBy('date')
            ->get();
    }

    // =========================================================================
    // Snapshot builder
    // =========================================================================

    private function buildSnapshot(int $year, Collection $transactions): array
    {
        return [
            'metadata' => $this->buildMetadata($year),
            'summary' => $this->buildSummary($transactions),
            'eur' => $this->buildEur($transactions),
            'events' => $this->buildEvents($year, $transactions),
            'projects' => $this->buildProjects($year, $transactions),
            'fundings' => $this->buildFundings($year, $transactions),
        ];
    }

    // -------------------------------------------------------------------------

    private function buildMetadata(int $year): array
    {
        return [
            'year' => $year,
            'generated_at' => now(),
            'generated_by' => auth()->user() !== null ? auth()->user()->name : 'System',
        ];
    }

    // -------------------------------------------------------------------------

    private function buildSummary(Collection $transactions): array
    {
        $income = $transactions->where('type', TransactionType::Deposit)->sum('amount_gross');
        $expense = $transactions->where('type', TransactionType::Withdrawal)->sum('amount_gross');

        return [
            'total_income' => (int) $income,
            'total_expense' => (int) $expense,
            'balance' => (int) ($income - $expense),
            'transaction_count' => $transactions->count(),
        ];
    }

    // -------------------------------------------------------------------------

    /**
     * EÜR: nach USt-Satz, nach steuerlicher Sphäre und nach Buchungskonto (SKR42).
     */
    private function buildEur(Collection $transactions): array
    {
        // --- Nach USt-Satz ---
        $byVat = $transactions
            ->groupBy('vat')
            ->map(fn (Collection $group) => [
                'vat' => $group->first()->vat,
                'income' => (int) $group->where('type', TransactionType::Deposit)->sum('amount_gross'),
                'expense' => (int) $group->where('type', TransactionType::Withdrawal)->sum('amount_gross'),
            ])
            ->sortKeys()
            ->values()
            ->toArray();

        // --- Nach Sphäre ---
        $bySphere = [];
        foreach (BookingAccountArea::cases() as $area) {
            $group = $transactions->filter(
                fn (Transaction $tx) => $tx->bookingAccount?->area === $area
            );
            $bySphere[$area->value] = [
                'label' => $area->label(),
                'income' => (int) $group->where('type', TransactionType::Deposit)->sum('amount_gross'),
                'expense' => (int) $group->where('type', TransactionType::Withdrawal)->sum('amount_gross'),
            ];
        }

        // --- Nach Buchungskonto (SKR42) ---
        $byBookingAccount = $transactions
            ->filter(fn (Transaction $tx) => $tx->bookingAccount !== null)
            ->groupBy('booking_account_id')
            ->map(function (Collection $group) {
                $account = $group->first()->bookingAccount;

                return [
                    'number' => $account->number,
                    'label' => $account->label,
                    'area' => $account->area->value,
                    'income' => (int) $group->where('type', TransactionType::Deposit)->sum('amount_gross'),
                    'expense' => (int) $group->where('type', TransactionType::Withdrawal)->sum('amount_gross'),
                ];
            })
            ->sortBy('number')
            ->values()
            ->toArray();

        return [
            'by_vat' => $byVat,
            'by_sphere' => $bySphere,
            'by_booking_account' => $byBookingAccount,
        ];
    }

    // -------------------------------------------------------------------------

    /**
     * Pro Event: Einnahmen, Ausgaben, Ergebnis, Besucherzahl.
     */
    private function buildEvents(int $year, Collection $transactions): array
    {
        $events = Event::query()
            ->whereYear('event_date', $year)
            ->with(['visitors'])
            ->orderBy('event_date')
            ->get();

        $txByEvent = $transactions
            ->filter(fn (Transaction $tx) => $tx->event_transaction !== null)
            ->groupBy(fn (Transaction $tx) => $tx->event_transaction->event_id);

        return $events->map(function (Event $event) use ($txByEvent) {
            $group = $txByEvent->get($event->id, collect());
            $income = (int) $group->where('type', TransactionType::Deposit)->sum('amount_gross');
            $expense = (int) $group->where('type', TransactionType::Withdrawal)->sum('amount_gross');

            $title = $event->title[app()->getLocale()] ?? reset($event->title);

            return [
                'id' => $event->id,
                'title' => $title,
                'date' => \App\Helpers\DateHelper::formatDate($event->event_date) ?: '-',
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
                'visitor_count' => $event->visitors->count(),
            ];
        })->toArray();
    }

    // -------------------------------------------------------------------------

    /**
     * Pro Projekt (scopeInYear): Einnahmen, Ausgaben, Saldo, Förderdeckung.
     *
     * Snapshot-Keys:
     *   id, title, status, start_date, end_date,
     *   income, expense, balance,
     *   funding_allocated, coverage_rate,
     *   fundings (für PDF Sub-Zeilen)
     */
    private function buildProjects(int $year, Collection $transactions): array
    {
        $projects = Project::query()
            ->inYear($year)
            ->with([
                'fundings' => fn ($q) => $q->withPivot('allocated_amount'),
            ])
            ->orderBy('title')
            ->get();

        if ($projects->isEmpty()) {
            return [];
        }

        $txByProject = $transactions
            ->filter(fn (Transaction $tx) => $tx->project_transaction !== null)
            ->groupBy(fn (Transaction $tx) => (int) $tx->project_transaction->project_id);

        return $projects->map(function (Project $project) use ($txByProject) {
            /** @var Collection<int, Transaction> $group */
            $group = $txByProject->get($project->id, collect());

            // effectiveAmount: allocated_amount falls gesetzt, sonst amount_gross
            // Expliziter (int)-Cast in der Closure nötig – amount_gross kommt als String aus DB
            $income = $group
                ->filter(fn (Transaction $tx) => $tx->type === TransactionType::Deposit)
                ->sum(fn (Transaction $tx) => (int) ($tx->project_transaction->allocated_amount ?? $tx->amount_gross));

            $expense = $group
                ->filter(fn (Transaction $tx) => $tx->type === TransactionType::Withdrawal)
                ->sum(fn (Transaction $tx) => (int) ($tx->project_transaction->allocated_amount ?? $tx->amount_gross));

            $fundingAllocated = (int) $project->fundings->sum(
                fn (Funding $f) => (int) ($f->pivot->allocated_amount ?? 0)
            );

            $coverageRate = $expense > 0
                ? (float) round($fundingAllocated / $expense * 100, 1)
                : 0.0;

            return [
                'id' => $project->id,
                'title' => $project->title,
                'status' => $project->status->label(),
                'start_date' => \App\Helpers\DateHelper::formatDate($project->start_date) ?: '-',
                'end_date' => \App\Helpers\DateHelper::formatDate($project->end_date) ?: '-',
                'income' => (int) $income,
                'expense' => (int) $expense,
                'balance' => (int) $income - (int) $expense,
                'funding_allocated' => $fundingAllocated,
                'coverage_rate' => $coverageRate,
                // Für das PDF – verknüpfte Förderungen als Sub-Zeilen
                'fundings' => $project->fundings->map(fn (Funding $f) => [
                    'title' => $f->title,
                    'funder' => $f->funder ?? '',
                    'allocated_amount' => (int) ($f->pivot->allocated_amount ?? 0),
                ])->toArray(),
            ];
        })->toArray();
    }

    // -------------------------------------------------------------------------

    /**
     * Pro Förderung (scopeInYear): Verwendungsnachweis-Basis.
     *
     * Snapshot-Keys:
     *   id, title, funder, reference, status,
     *   approved_amount, received, allocated_to_projects,
     *   remaining, period_start, period_end,
     *   projects (für PDF Sub-Zeilen)
     */
    private function buildFundings(int $year, Collection $transactions): array
    {
        $fundings = Funding::query()
            ->inYear($year)
            ->with([
                'projects' => fn ($q) => $q->withPivot('allocated_amount'),
            ])
            ->orderBy('title')
            ->get();

        if ($fundings->isEmpty()) {
            return [];
        }

        $txByFunding = $transactions
            ->filter(fn (Transaction $tx) => $tx->funding_transaction !== null)
            ->groupBy(fn (Transaction $tx) => (int) $tx->funding_transaction->funding_id);

        return $fundings->map(function (Funding $funding) use ($txByFunding) {
            /** @var Collection<int, Transaction> $group */
            $group = $txByFunding->get($funding->id, collect());

            // Im Berichtsjahr erhaltene Zahlungen (via FundingTransaction)
            $received = $group
                ->filter(fn (Transaction $tx) => $tx->type === TransactionType::Deposit)
                ->sum(fn (Transaction $tx) => (int) ($tx->funding_transaction->allocated_amount ?? $tx->amount_gross));

            $allocatedToProjects = (int) $funding->projects->sum(
                fn (Project $p) => (int) ($p->pivot->allocated_amount ?? 0)
            );

            $approvedAmount = (int) ($funding->approved_amount ?? 0);

            return [
                'id' => $funding->id,
                'title' => $funding->title,
                'funder' => $funding->funder ?? '',
                'reference' => $funding->reference ?? '',
                'status' => $funding->status->label(),
                'approved_amount' => $approvedAmount,
                'received' => (int) $received,
                'allocated_to_projects' => $allocatedToProjects,
                'remaining' => $approvedAmount - $allocatedToProjects,
                'period_start' => \App\Helpers\DateHelper::formatDate($funding->funding_period_start),
                'period_end' => \App\Helpers\DateHelper::formatDate($funding->funding_period_end),
                // Für das PDF – verknüpfte Projekte als Sub-Zeilen
                'projects' => $funding->projects->map(fn (Project $p) => [
                    'title' => $p->title,
                    'status' => $p->status->label(),
                    'allocated_amount' => (int) ($p->pivot->allocated_amount ?? 0),
                ])->toArray(),
            ];
        })->toArray();
    }
}
