<?php

declare(strict_types=1);

namespace App\Livewire\Member\Index;

use App\Enums\MemberType;
use App\Livewire\Traits\Sortable;
use App\Models\Membership\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use Sortable;
    use WithPagination;

    public $search = '';

    public $showInactive = true;
    public $filteredBy = [
        MemberType::AP->value,
        MemberType::MD->value,
        MemberType::ST->value,
        MemberType::AD->value,
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function members(): LengthAwarePaginator
    {
        return Member::query()
            ->tap(fn ($query) => $this->showInactive ? $query : $query->whereNull('left_at'))
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->tap(fn ($query) => $this->search ? $query->where('name', 'LIKE', '%'.$this->search.'%')
                ->orWhere('first_name', 'LIKE', '%'.$this->search.'%')
                ->orWhere('email', 'LIKE', '%'.$this->search.'%')
                ->orWhere('bith_place', 'LIKE', '%'.$this->search.'%')
                : $query)
            ->tap(fn ($query) => $this->filteredBy ? $query->whereIn('type', $this->filteredBy) : $query)
            ->paginate(10);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.index.page')->title(__('members.title'));
    }
}
