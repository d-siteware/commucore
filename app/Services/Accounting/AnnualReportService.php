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
 *   $data = AnnualReportService::build(year: 2024);
 *   // $data['transactions'] und $data['snapshot'] an AnnualReportPdf übergeben
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
        $user = auth()->user();

        return [
            'year' => $year,
            'generated_at' => now(),
            'generated_by' => $user !== null ? $user->name : 'System',
        ];
    }

    // -------------------------------------------------------------------------

    private function buildSummary(Collection $transactions): array
    {
        $income = $transactions->where('type', TransactionType::Deposit)->sum('amount_gross');
        $expense = $transactions->where('type', TransactionType::Withdrawal)->sum('amount_gross');

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'balance' => $income - $expense,
            'transaction_count' => $transactions->count(),
        ];
    }

    // -------------------------------------------------------------------------

    /**
     * EÜR-Daten: aufgeteilt nach USt-Satz, steuerlicher Sphäre und Buchungskonto.
     */
    private function buildEur(Collection $transactions): array
    {
        // --- Nach USt-Satz ---
        $byVat = $transactions
            ->groupBy('vat')
            ->map(fn (Collection $group, int $vat) => [
                'vat' => $vat,
                'income' => $group->where('type', TransactionType::Deposit)->sum('amount_gross'),
                'expense' => $group->where('type', TransactionType::Withdrawal)->sum('amount_gross'),
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
                'income' => $group->where('type', TransactionType::Deposit)->sum('amount_gross'),
                'expense' => $group->where('type', TransactionType::Withdrawal)->sum('amount_gross'),
            ];
        }

        // --- Nach Buchungskonto (SKR49) ---
        $byBookingAccount = $transactions
            ->filter(fn (Transaction $tx) => $tx->bookingAccount !== null)
            ->groupBy('booking_account_id')
            ->map(function (Collection $group): array {
                /** @var Transaction $first */
                $first = $group->first();
                $account = $first->bookingAccount;

                return [
                    'number' => $account->number,
                    'label' => $account->label,
                    'area' => $account->area->value,
                    'income' => $group->where('type', TransactionType::Deposit)->sum('amount_gross'),
                    'expense' => $group->where('type', TransactionType::Withdrawal)->sum('amount_gross'),
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

        return $events->map(function (Event $event) use ($txByEvent): array {
            $group = $txByEvent->get($event->id, collect());
            $income = $group->where('type', TransactionType::Deposit)->sum('amount_gross');
            $expense = $group->where('type', TransactionType::Withdrawal)->sum('amount_gross');

            // Titel: translatable array – Locale-Fallback auf ersten Eintrag
            $title = $event->title[app()->getLocale()]
                ?? (reset($event->title) ?: '-');

            return [
                'id' => $event->id,
                'title' => $title,
                'date' => $event->event_date?->format('d.m.Y') ?? '-',
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
                'visitor_count' => $event->visitors->count(),
            ];
        })->toArray();
    }

    // -------------------------------------------------------------------------

    /**
     * Pro Projekt: Ausgaben, zugewiesene Fördermittel, Deckungsgrad.
     */
    private function buildProjects(int $year, Collection $transactions): array
    {
        $projects = Project::query()
            ->inYear($year)
            ->with(['fundings'])
            ->orderBy('title')
            ->get();

        $txByProject = $transactions
            ->filter(fn (Transaction $tx) => $this->getProjectTransaction($tx) !== null)
            ->groupBy(fn (Transaction $tx) => $this->getProjectTransaction($tx)->project_id);

        return $projects->map(function (Project $project) use ($txByProject): array {
            $group = $txByProject->get($project->id, collect());
            $income = $group->where('type', TransactionType::Deposit)->sum('amount_gross');
            $expense = $group->where('type', TransactionType::Withdrawal)->sum('amount_gross');

            $fundingAllocated = (int) $project->fundings
                ->sum(fn (\App\Models\Funding\Funding $f): int => (int) ($f->pivot->allocated_amount ?? 0)
                );

            return [
                'id' => $project->id,
                'title' => $project->title,
                'status' => $project->status->label(),
                'start_date' => $project->start_date?->format('d.m.Y') ?? '-',
                'end_date' => $project->end_date?->format('d.m.Y') ?? '-',
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
                'funding_allocated' => $fundingAllocated,
                'coverage_rate' => $expense > 0
                    ? round(($fundingAllocated / $expense) * 100, 1)
                    : 0.0,
            ];
        })->toArray();
    }

    // -------------------------------------------------------------------------

    /**
     * Pro Förderung: bewilligter Betrag, erhaltene Einnahmen, verplante Mittel,
     * zugeordnete Projekte.
     */
    private function buildFundings(int $year, Collection $transactions): array
    {
        $fundings = Funding::query()
            ->inYear($year)
            ->with(['projects'])
            ->orderBy('funder')
            ->get();

        $txByFunding = $transactions
            ->filter(fn (Transaction $tx) => $this->getFundingTransaction($tx) !== null)
            ->groupBy(fn (Transaction $tx) => $this->getFundingTransaction($tx)->funding_id);

        return $fundings->map(function (Funding $funding) use ($txByFunding): array {
            $group = $txByFunding->get($funding->id, collect());
            $received = $group->where('type', TransactionType::Deposit)->sum('amount_gross');

            $allocatedToProjects = (int) $funding->projects
                ->sum(fn (\App\Models\Project\Project $p): int => (int) ($p->pivot->allocated_amount ?? 0)
                );

            $usageReport = $funding->usageReport();

            return [
                'id' => $funding->id,
                'title' => $funding->title,
                'funder' => $funding->funder,
                'reference' => $funding->reference ?? '-',
                'status' => $funding->status->label(),
                'approved_amount' => $funding->approved_amount ?? 0,
                'received' => $received,
                'allocated_to_projects' => $allocatedToProjects,
                'remaining' => $usageReport['remaining'],
                'period_start' => $funding->funding_period_start?->format('d.m.Y') ?? '-',
                'period_end' => $funding->funding_period_end?->format('d.m.Y') ?? '-',
                'projects' => $funding->projects->map(
                    fn (\App\Models\Project\Project $p): array => [
                        'title' => $p->title,
                        'allocated_amount' => (int) ($p->pivot->allocated_amount ?? 0),
                    ]
                )->toArray(),
            ];
        })->toArray();
    }

    // =========================================================================
    // PHPStan Level 6 helpers – typed relation accessors
    // =========================================================================

    /**
     * Typisierter Zugriff auf project_transaction-Relation.
     * Ersetzt Magic-Property-Zugriff ($tx->project_transaction) solange
     * die @property-read noch nicht im Transaction-PHPDoc eingetragen ist.
     */
    private function getProjectTransaction(Transaction $tx): ?\App\Models\Project\ProjectTransaction
    {
        $relation = $tx->getRelation('project_transaction');

        return $relation instanceof \App\Models\Project\ProjectTransaction ? $relation : null;
    }

    /**
     * Typisierter Zugriff auf funding_transaction-Relation.
     */
    private function getFundingTransaction(Transaction $tx): ?\App\Models\Funding\FundingTransaction
    {
        $relation = $tx->getRelation('funding_transaction');

        return $relation instanceof \App\Models\Funding\FundingTransaction ? $relation : null;
    }
}
