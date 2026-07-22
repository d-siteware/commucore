<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Funding\Index;

use App\Enums\FundingStatus;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\Sortable;
use App\Models\Funding\Funding;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use HasPrivileges;
    use Sortable;
    use WithPagination;

    public string $search = '';

    public bool $showArchived = false;

    /** @var array<string> */
    public array $filteredBy = [
        FundingStatus::Applied->value,
        FundingStatus::Approved->value,
        FundingStatus::Active->value,
    ];

    public function mount(): void
    {
        $this->sortBy = 'funding_period_start';
        $this->sortDirection = 'desc';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function fundings(): LengthAwarePaginator
    {
        return Funding::query()
            ->withCount('projects')
            ->withCount('fundingTransactions')
            ->tap(fn ($q) => $this->sortBy
                ? $q->orderBy($this->sortBy, $this->sortDirection)
                : $q
            )
            ->tap(fn ($q) => $this->search
                ? $q->where('title', 'LIKE', '%'.$this->search.'%')
                    ->orWhere('funder', 'LIKE', '%'.$this->search.'%')
                : $q
            )
            ->tap(fn ($q) => $this->filteredBy
                ? $q->whereIn('status', $this->filteredBy)
                : $q
            )
            ->tap(fn ($q) => $this->showArchived
                ? $q->archived()
                : $q->notArchived()
            )
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.accounting.funding.index.page')
            ->title(__('fundings.page.title'));
    }
}
