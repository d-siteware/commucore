<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Project\Index;

use App\Enums\ProjectStatus;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\Sortable;
use App\Models\Project\Project;
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

    /** @var array<string> */
    public array $filteredBy = [
        ProjectStatus::Planned->value,
        ProjectStatus::Active->value,
    ];

    public function mount(): void
    {
        $this->sortBy = 'start_date';
        $this->sortDirection = 'desc';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function projects(): LengthAwarePaginator
    {
        return Project::query()
            ->withCount(['projectTransactions', 'fundings'])
            ->tap(fn ($q) => $this->sortBy
                ? $q->orderBy($this->sortBy, $this->sortDirection)
                : $q
            )
            ->tap(fn ($q) => $this->search
                ? $q->where('title', 'LIKE', '%'.$this->search.'%')
                : $q
            )
            ->tap(fn ($q) => $this->filteredBy
                ? $q->whereIn('status', $this->filteredBy)
                : $q
            )
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.activity.project.index.page')
            ->title(__('projects.page.title'));
    }
}
