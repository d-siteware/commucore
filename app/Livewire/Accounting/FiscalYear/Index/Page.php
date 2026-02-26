<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\FiscalYear\Index;

use App\Models\Accounting\FiscalYear;
use App\Services\Accounting\FiscalYearService;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

final class Page extends Component
{
    public ?int $selectedYear = null;

    public bool $showDetailsModal = false;

    public ?array $snapshotData = null;

    #[On('fiscal-year-created')]
    public function refreshList(): void
    {
        unset($this->fiscalYears);

        Flux::modal('make-new-fiscal-year-modal')->close();
    }

    #[Computed]
    public function fiscalYears(): Collection
    {
        return FiscalYear::query()
            ->with(['openedBy', 'closedBy'])
            ->withCount('transactions')
            ->orderBy('year', 'desc')
            ->get();
    }

    public function showDetails(int $year): void
    {
        $this->selectedYear = $year;

        $service = app(FiscalYearService::class);
        $fiscalYear = FiscalYear::where('year', $year)->first();

        if ($fiscalYear && $fiscalYear->isClosed()) {
            $this->snapshotData = $service->getSnapshot($year);
        } else {
            $this->snapshotData = $this->getOpenYearData($year);
        }

        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedYear = null;
        $this->snapshotData = null;
    }

    public function navigateToClose(int $year): void
    {
        $this->redirect(route('fiscal-years.close', ['year' => $year]), navigate: true);
    }

    public function reopenFiscalYear(int $year): void
    {
        $this->authorize('reopen', FiscalYear::class);

        try {
            $service = app(FiscalYearService::class);
            $service->reopenFiscalYear($year);

            session()->flash('success', __('fiscal_year.reopened_successfully', ['year' => $year]));

            $this->closeDetailsModal();
            unset($this->fiscalYears);
        } catch (\Exception $e) {
            $this->addError('reopen', $e->getMessage());
        }
    }

    public function exportSnapshot(int $year)
    {
        $this->authorize('view', FiscalYear::class);

        try {
            $service = app(FiscalYearService::class);
            $snapshot = $service->getSnapshot($year);

            // Hier könntest du ein PDF generieren oder als JSON exportieren
            // Für jetzt: Download als JSON
            $filename = "fiscal_year_{$year}_snapshot.json";

            return response()->streamDownload(function () use ($snapshot) {
                echo json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }, $filename, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            $this->addError('export', $e->getMessage());
        }
    }

    private function getOpenYearData(int $year): array
    {
        $fiscalYear = FiscalYear::where('year', $year)->first();

        if (! $fiscalYear) {
            $fiscalYear = FiscalYear::getOrCreate($year);
        }

        $transactions = \App\Models\Accounting\Transaction::whereYear('date', $year)
            ->with(['account', 'member_transaction', 'event_transaction'])
            ->get();

        return [
            'fiscal_year' => $fiscalYear,
            'metadata' => [
                'year' => $year,
                'opened_at' => $fiscalYear->opened_at,
                'closed_at' => null,
                'opened_by' => $fiscalYear->openedBy?->name,
                'closed_by' => null,
                'is_closed' => false,
            ],
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->date,
                    'label' => $transaction->label,
                    'amount' => $transaction->amount_gross,
                    'type' => $transaction->type,
                    'status' => $transaction->status,
                    'locked_at' => null,
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

    public function openCreateFiscalYearModal()
    {
        Flux::modal('make-new-fiscal-year-modal')->show();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.fiscal-year.index.page')
            ->title(__('fiscal_year.index.title'));
    }
}
