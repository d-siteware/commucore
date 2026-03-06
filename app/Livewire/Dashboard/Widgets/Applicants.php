<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Widgets;

use App\Models\Membership\MemberApplication;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Applicants extends Component
{
    use WithPagination;

    /** @var array<int, string> */
    public array $selectedApplicants = [];

    /** @var array<int, string> */
    public array $applicantsOnPage = [];

    /** @var array<int, string> */
    public array $allApplicants = [];

    public string $sortBy = 'applied_at';

    public string $sortDirection = 'desc';

    public string $search = '';

    public int $numApplicants = 0;

    public function mount(): void
    {
        $this->numApplicants = MemberApplication::query()
            ->whereNotNull('verified_at')
            ->count();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteSelectedApplicants(): void
    {
        if (count($this->selectedApplicants) === 0) {
            return;
        }

        if (! auth()->user()?->is_admin) {
            return;
        }

        MemberApplication::query()
            ->whereIn('id', $this->selectedApplicants)
            ->delete();

        Flux::toast(
            text: __('members.widgets.applicants.confirm.deletion.text'),
            heading: __('members.widgets.applicants.confirm.deletion.title'),
            variant: 'success',
        );

        $this->selectedApplicants = [];

        $this->numApplicants = MemberApplication::query()
            ->whereNotNull('verified_at')
            ->count();
    }

    #[Computed]
    public function applicants(): LengthAwarePaginator
    {
        $query = MemberApplication::query()
            ->whereNotNull('verified_at')
            ->when(
                $this->search !== '',
                fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')
            )
            ->orderBy($this->sortBy, $this->sortDirection);

        $paginated = $query->paginate(5);

        $this->allApplicants = MemberApplication::query()
            ->whereNotNull('verified_at')
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->toArray();

        $this->applicantsOnPage = $paginated
            ->map(fn ($application): string => (string) $application->id)
            ->toArray();

        return $paginated;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.dashboard.widgets.applicants');
    }
}
