<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\FiscalYear\Close;

use App\Livewire\Traits\Sortable;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\FiscalYearService;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use Sortable;
    use WithPagination;

    public int $year;

    public ?FiscalYear $fiscalYear = null;

    public array $selectedTransactions = [];

    public bool $selectAll = false;

    public bool $confirmClose = false;

    public bool $showConfirmation = false;

    public ?int $nextYear = null;

    public ?int $lastSelectedIndex = null;

    #[Url]
    public string $search = '';

    public function mount(int $year): void
    {
        $this->year = $year;
        $this->nextYear = $year + 1;
        $this->fiscalYear = FiscalYear::getOrCreate($year);
        $this->sortBy = 'date';
        $this->sortDirection = 'asc';

        if ($this->fiscalYear->isClosed()) {
            session()->flash('error', __('fiscal_year.already_closed', ['year' => $year]));
            $this->redirect(route('fiscal-years.index'), navigate: true);
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function unlockedTransactions(): LengthAwarePaginator
    {
        $query = Transaction::query()
            ->unlocked($this->year)
            ->with(['account', 'member_transaction.member'])
            ->whereBetween('date', [
                "{$this->year}-01-01 00:00:00",
                "{$this->year}-12-31 23:59:59",
            ]);

        // Suche
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('label', 'like', '%'.$this->search.'%')
                    ->orWhere('reference', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhereHas('member_transaction.member', function ($memberQuery) {
                        $memberQuery->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('first_name', 'like', '%'.$this->search.'%');
                    })
                    ->orWhereHas('account', function ($accountQuery) {
                        $accountQuery->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        // Sortierung
        if ($this->sortBy === 'account') {
            $query->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
                ->orderBy('accounts.name', $this->sortDirection)
                ->select('transactions.*');
        } elseif ($this->sortBy === 'member') {
            $query->leftJoin('member_transactions', 'transactions.id', '=', 'member_transactions.transaction_id')
                ->leftJoin('members', 'member_transactions.member_id', '=', 'members.id')
                ->orderBy('members.name', $this->sortDirection)
                ->orderBy('members.first_name', $this->sortDirection)
                ->select('transactions.*');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate($this->showRows);
    }

    #[Computed]
    public function allFilteredTransactionIds(): array
    {
        $query = Transaction::query()
            ->unlocked($this->year)
            ->whereBetween('date', [
                "{$this->year}-01-01 00:00:00",
                "{$this->year}-12-31 23:59:59",
            ])
            ->select('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('label', 'like', '%'.$this->search.'%')
                    ->orWhere('reference', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhereHas('member_transaction.member', function ($memberQuery) {
                        $memberQuery->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('first_name', 'like', '%'.$this->search.'%');
                    })
                    ->orWhereHas('account', function ($accountQuery) {
                        $accountQuery->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        return $query->pluck('id')->toArray();
    }

    #[Computed]
    public function transactionCount(): int
    {
        return count($this->allFilteredTransactionIds());
    }

    #[Computed]
    public function selectedCount(): int
    {
        return count($this->selectedTransactions);
    }

    #[Computed]
    public function totalIncome(): float
    {
        return $this->getSelectedTransactions()
            ->filter(fn ($t) => $t->type->isIncome())
            ->sum('amount_net') / 100;
    }

    #[Computed]
    public function totalExpense(): float
    {
        return $this->getSelectedTransactions()
            ->filter(fn ($t) => $t->type->isExpense())
            ->sum('amount_net') / 100;
    }

    #[Computed]
    public function balance(): float
    {
        return $this->totalIncome() - $this->totalExpense();
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedTransactions = $this->allFilteredTransactionIds();
        } else {
            $this->selectedTransactions = [];
        }
    }

    public function updatedSelectedTransactions(): void
    {
        $allIds = $this->allFilteredTransactionIds();
        $this->selectAll = count($this->selectedTransactions) === count($allIds)
            && count($allIds) > 0;
    }

    public function toggleTransaction(int $transactionId, int $pageIndex, bool $shiftKey = false): void
    {
        if ($shiftKey && $this->lastSelectedIndex !== null) {
            $this->selectRange($this->lastSelectedIndex, $pageIndex);
        } else {
            if (in_array($transactionId, $this->selectedTransactions)) {
                $this->selectedTransactions = array_values(
                    array_diff($this->selectedTransactions, [$transactionId])
                );
            } else {
                $this->selectedTransactions[] = $transactionId;
            }
            $this->lastSelectedIndex = $pageIndex;
        }

        $this->updatedSelectedTransactions();
    }

    private function selectRange(int $startIndex, int $endIndex): void
    {
        $start = min($startIndex, $endIndex);
        $end = max($startIndex, $endIndex);

        $items = $this->unlockedTransactions()->items();

        for ($i = $start; $i <= $end; $i++) {
            if (isset($items[$i])) {
                $transactionId = $items[$i]->id;
                if (! in_array($transactionId, $this->selectedTransactions)) {
                    $this->selectedTransactions[] = $transactionId;
                }
            }
        }

        $this->lastSelectedIndex = $endIndex;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function showConfirmationModal(): void
    {
        if (empty($this->selectedTransactions)) {
            $this->addError('selection', __('fiscal_year.select_at_least_one'));

            return;
        }

        $this->showConfirmation = true;
    }

    public function close(): void
    {
        $this->authorize('close', $this->fiscalYear);

        if (! $this->confirmClose) {
            $this->addError('confirm', __('fiscal_year.must_confirm'));

            return;
        }

        if (empty($this->selectedTransactions)) {
            $this->addError('selection', __('fiscal_year.select_at_least_one'));

            return;
        }

        try {
            $service = app(FiscalYearService::class);

            $service->closeFiscalYearWithSelection(
                $this->year,
                $this->selectedTransactions,
                auth()->id() ?? 0
            );

            FiscalYear::getOrCreate($this->nextYear ?? 0, auth()->id());

            Flux::toast(
                text: __('fiscal_year.closed_successfully', ['year' => $this->year,
                    'count' => count($this->selectedTransactions),
                    'next_year' => $this->nextYear, ]),
                heading: __('fiscal_year.closed_successfully_heading', ['year' => $this->year]),
                variant: 'success'
            );

            $this->redirect(route('fiscal-years.index'), navigate: true);
        } catch (\Exception $e) {
            $this->addError('close', $e->getMessage());
        }
    }

    public function cancel(): void
    {
        $this->redirect(route('fiscal-years.index'), navigate: true);
    }

    private function getSelectedTransactions(): Collection
    {
        return Transaction::query()
            ->whereIn('id', $this->selectedTransactions)
            ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.fiscal-year.close.page')->title(__('fiscal_year.close.title'));
    }
}
