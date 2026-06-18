<?php

namespace App\Livewire\Member\Fees;

use App\Enums\MemberType;
use App\Enums\TransactionStatus;
use App\Livewire\Traits\Sortable;
use App\Models\Membership\MemberTransaction;
use App\Services\CsvExportService;
use App\Services\PdfGeneratorService;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use Sortable;
    use WithPagination;

    public string $search = '';

    public bool $showInactive = true;

    public int $selectedYear;

    public array $filteredBy = [
        MemberType::AP->value,
        MemberType::MD->value,
        MemberType::ST->value,
        MemberType::AD->value,
    ];

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedYear(): void
    {
        $this->resetPage();
    }

    public function updatedFilteredBy(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function member_payments(): LengthAwarePaginator
    {
        $query = MemberTransaction::query()
            ->where('is_membership_fee', true)
            ->where('fee_year', $this->selectedYear)
            ->with(['member', 'transaction']);

        // Suche in Member-Feldern
        if ($this->search !== '' && $this->search !== '0') {
            $query->whereHas('member', function ($q): void {
                $q->where('name', 'LIKE', '%'.$this->search.'%')
                    ->orWhere('first_name', 'LIKE', '%'.$this->search.'%')
                    ->orWhere('email', 'LIKE', '%'.$this->search.'%');
            });
        }

        // Filter nach Member Type
        if (! empty($this->filteredBy)) {
            $query->whereHas('member', function ($q): void {
                $q->whereIn('type', $this->filteredBy);
            });
        }

        // Nur aktive Mitglieder
        if (! $this->showInactive) {
            $query->whereHas('member', function ($q): void {
                $q->whereNull('left_at');
            });
        }

        // Hole alle Transaktionen und gruppiere nach Member
        $allTransactions = $query->get();

        $grouped = $allTransactions
            ->groupBy('member_id')
            ->map(function ($memberTransactions, $memberId) {
                $member = $memberTransactions->first()->member;
                $transactions = $memberTransactions->sortByDesc('transaction.date');

                // Summen berechnen
                $totalPaid = $transactions
                    ->filter(fn ($mt): bool => $mt->transaction->status === TransactionStatus::booked)
                    ->sum(fn ($mt) => $mt->transaction->amount_net ?? 0);

                $totalPending = $transactions
                    ->filter(fn ($mt): bool => $mt->transaction?->status === TransactionStatus::submitted)
                    ->sum(fn ($mt) => $mt->transaction->amount_net ?? 0);

                return (object) [
                    'member_id' => $memberId,
                    'member' => $member,
                    'transactions' => $transactions,
                    'total_paid' => $totalPaid,
                    'total_pending' => $totalPending,
                    'total_amount' => $totalPaid + $totalPending,
                    'transaction_count' => $transactions->count(),
                    'latest_transaction' => $transactions->first(),
                    'has_paid' => $totalPaid > 0,
                ];
            })
            ->values(); // Collection zurücksetzen

        // Sortierung
        if ($this->sortBy) {
            $grouped = match ($this->sortBy) {
                'member.name' => $this->sortDirection === 'asc'
                    ? $grouped->sortBy(fn ($g) => $g->member->name)
                    : $grouped->sortByDesc(fn ($g) => $g->member->name),
                'total_paid' => $this->sortDirection === 'asc'
                    ? $grouped->sortBy('total_paid')
                    : $grouped->sortByDesc('total_paid'),
                'total_amount' => $this->sortDirection === 'asc'
                    ? $grouped->sortBy('total_amount')
                    : $grouped->sortByDesc('total_amount'),
                default => $grouped->sortBy(fn ($g) => $g->member->name)
            };
        } else {
            // Standard: Nach Name sortieren
            $grouped = $grouped->sortBy(fn ($g) => $g->member->name);
        }

        // Manuelle Paginierung
        $perPage = 15;
        $currentPage = Paginator::resolveCurrentPage();
        $items = $grouped->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $grouped->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    #[Computed]
    public function summary(): array
    {
        $transactions = MemberTransaction::query()
            ->where('is_membership_fee', true)
            ->where('fee_year', $this->selectedYear)
            ->with('transaction')
            ->when(! empty($this->filteredBy), function ($query): void {
                $query->whereHas('member', function ($q): void {
                    $q->whereIn('type', $this->filteredBy);
                });
            })
            ->when(! $this->showInactive, function ($query): void {
                $query->whereHas('member', function ($q): void {
                    $q->whereNull('left_at');
                });
            })
            ->get();

        $paid = $transactions->filter(fn ($mt): bool => $mt->transaction?->status === TransactionStatus::booked
        );

        $pending = $transactions->filter(fn ($mt): bool => $mt->transaction?->status === TransactionStatus::submitted
        );

        return [
            'total_members' => $transactions->pluck('member_id')->unique()->count(),
            'total_transactions' => $transactions->count(),
            'total_paid' => $paid->sum(fn ($mt) => $mt->transaction->amount_net ?? 0),
            'total_pending' => $pending->sum(fn ($mt) => $mt->transaction->amount_net ?? 0),
            'paid_count' => $paid->count(),
            'pending_count' => $pending->count(),
        ];
    }

    #[Computed]
    public function availableYears(): array
    {
        return MemberTransaction::query()
            ->where('is_membership_fee', true)
            ->select('fee_year')
            ->distinct()
            ->whereNotNull('fee_year')
            ->orderBy('fee_year', 'desc')
            ->pluck('fee_year')
            ->toArray();
    }

    public function exportPdf()
    {
        // Hole ALLE Daten (ohne Paginierung)
        $allPayments = $this->getAllMemberPayments();

        $pdfContent = PdfGeneratorService::generatePdf('membership-fees', [
            'payments' => $allPayments,
            'summary' => $this->summary(),
            'year' => $this->selectedYear,
        ]);

        $filename = "Mitgliedsbeitraege-{$this->selectedYear}-".now()->format('Ymd').'.pdf';

        return response()->streamDownload(
            fn () => print ($pdfContent),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function exportCsv()
    {
        $allPayments = $this->getAllMemberPayments();

        $csv = CsvExportService::exportMembershipFees($allPayments, $this->selectedYear);

        $filename = "Mitgliedsbeitraege-{$this->selectedYear}-".now()->format('Ymd').'.csv';

        return response()->streamDownload(
            fn () => print ($csv),
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }

    /**
     * Hole alle Member Payments ohne Paginierung für Export
     */
    private function getAllMemberPayments(): Collection
    {
        $query = MemberTransaction::query()
            ->where('is_membership_fee', true)
            ->where('fee_year', $this->selectedYear)
            ->with(['member', 'transaction']);

        if ($this->search !== '' && $this->search !== '0') {
            $query->whereHas('member', function ($q): void {
                $q->where('name', 'LIKE', '%'.$this->search.'%')
                    ->orWhere('first_name', 'LIKE', '%'.$this->search.'%')
                    ->orWhere('email', 'LIKE', '%'.$this->search.'%');
            });
        }

        if (! empty($this->filteredBy)) {
            $query->whereHas('member', function ($q): void {
                $q->whereIn('type', $this->filteredBy);
            });
        }

        if (! $this->showInactive) {
            $query->whereHas('member', function ($q): void {
                $q->whereNull('left_at');
            });
        }

        $allTransactions = $query->get();

        return $allTransactions
            ->groupBy('member_id')
            ->map(function ($memberTransactions, $memberId) {
                $member = $memberTransactions->first()->member;
                $transactions = $memberTransactions->sortByDesc('transaction.date');

                $totalPaid = $transactions
                    ->filter(fn ($mt): bool => $mt->transaction->status === TransactionStatus::booked)
                    ->sum(fn ($mt) => $mt->transaction->amount_net ?? 0);

                $totalPending = $transactions
                    ->filter(fn ($mt): bool => $mt->transaction->status === TransactionStatus::submitted)
                    ->sum(fn ($mt) => $mt->transaction->amount_net ?? 0);

                return (object) [
                    'member_id' => $memberId,
                    'member' => $member,
                    'transactions' => $transactions,
                    'total_paid' => $totalPaid,
                    'total_pending' => $totalPending,
                    'total_amount' => $totalPaid + $totalPending,
                    'transaction_count' => $transactions->count(),
                    'latest_transaction' => $transactions->first(),
                    'has_paid' => $totalPaid > 0,
                ];
            })
            ->sortBy(fn ($g) => $g->member->name)
            ->values();
    }

    public function render()
    {
        return view('livewire.member.fees.index')->title(__('members.fees.overview_title'));
    }
}
