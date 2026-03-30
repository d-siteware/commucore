<?php

declare(strict_types=1);

namespace App\Livewire\Member\Index;

use App\Enums\MemberType;
use App\Livewire\Traits\Sortable;
use App\Models\Membership\Member;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use Sortable;
    use WithPagination;

    public string|null $search = '';

    public string|null $confirm_deletion_text = '';
    public Member|null $selectedMember = null;
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

    public function makePayment(int $memberId): void
    {
        $this->selectedMember = Member::findOrFail($memberId);
        $this->authorize('make-payment', $this->selectedMember);
        Flux::modal('add-new-payment')->show();
    }
    public function deleteMember(int $memberId): void
    {
        $this->selectedMember = Member::findOrFail($memberId);
        $this->authorize('delete', $this->selectedMember);
        Flux::modal('delete-membership')->show();

    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.index.page')->title(__('members.title'));
    }
}
