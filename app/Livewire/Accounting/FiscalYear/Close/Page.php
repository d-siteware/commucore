<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\FiscalYear\Close;

use App\Enums\TransactionType;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\FiscalYearService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Page extends Component
{
    public int $year;

    public ?FiscalYear $fiscalYear = null;

    public array $selectedTransactions = [];

    public bool $selectAll = false;

    public bool $confirmClose = false;

    public bool $showConfirmation = false;

    public ?int $nextYear = null;

    public ?int $lastSelectedIndex = null;

    public function mount(int $year): void
    {
        $this->year = $year;
        $this->nextYear = $year + 1;
        $this->fiscalYear = FiscalYear::getOrCreate($year);

        if ($this->fiscalYear->isClosed()) {
            session()->flash('error', __('fiscal_year.already_closed', ['year' => $year]));
            $this->redirect(route('fiscal-years.index'), navigate: true);
        }
    }

    #[Computed]
    public function unlockedTransactions(): Collection
    {
        return Transaction::whereYear('date', $this->year)
            ->unlocked($this->year)
            ->with(['account', 'member_transaction.member'])
            ->orderBy('date', 'asc')
            ->get();
    }

    #[Computed]
    public function transactionCount(): int
    {
        return $this->unlockedTransactions()->count();
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
            ->where('type', TransactionType::Deposit->value)
            ->sum('amount_net') / 100;
    }

    #[Computed]
    public function totalExpense(): float
    {
        return $this->getSelectedTransactions()
            ->where('type', TransactionType::Withdrawal->value)
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
            $this->selectedTransactions = $this->unlockedTransactions()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedTransactions = [];
        }
    }

    public function updatedSelectedTransactions(): void
    {
        $this->selectAll = count($this->selectedTransactions) === $this->transactionCount();
    }

    /**
     * Toggle einzelne Transaction (wird von Alpine.js aufgerufen)
     */
    public function toggleTransaction(int $transactionId, int $index, bool $shiftKey = false): void
    {
        if ($shiftKey && $this->lastSelectedIndex !== null) {
            // Shift-Select: Wähle alle Transaktionen zwischen lastSelectedIndex und index
            $this->selectRange($this->lastSelectedIndex, $index);
        } else {
            // Normales Toggle
            if (in_array($transactionId, $this->selectedTransactions)) {
                $this->selectedTransactions = array_values(
                    array_diff($this->selectedTransactions, [$transactionId])
                );
            } else {
                $this->selectedTransactions[] = $transactionId;
            }
            $this->lastSelectedIndex = $index;
        }

        $this->updatedSelectedTransactions();
    }

    /**
     * Wähle einen Bereich von Transaktionen aus
     */
    private function selectRange(int $startIndex, int $endIndex): void
    {
        $start = min($startIndex, $endIndex);
        $end = max($startIndex, $endIndex);

        $transactions = $this->unlockedTransactions()->values();

        for ($i = $start; $i <= $end; $i++) {
            if (isset($transactions[$i])) {
                $transactionId = $transactions[$i]->id;
                if (! in_array($transactionId, $this->selectedTransactions)) {
                    $this->selectedTransactions[] = $transactionId;
                }
            }
        }

        $this->lastSelectedIndex = $endIndex;
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

            session()->flash('success', __('fiscal_year.closed_successfully', [
                'year' => $this->year,
                'count' => count($this->selectedTransactions),
                'next_year' => $this->nextYear,
            ]));

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
        return $this->unlockedTransactions()
            ->whereIn('id', $this->selectedTransactions);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.fiscal-year.close.page', [
            'transactions' => $this->unlockedTransactions(),
        ]);
    }
}
