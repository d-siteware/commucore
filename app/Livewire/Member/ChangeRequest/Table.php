<?php

namespace App\Livewire\Member\ChangeRequest;

use App\Models\MemberChangeRequest;
use App\Models\Membership\Member;
use Illuminate\Support\Collection;
use Livewire\Component;

class Table extends Component
{
    public Member $member;

    public function getListeners(): array
    {
        return [
            'change-request-created' => '$refresh',
        ];
    }

    /** @return Collection<int, MemberChangeRequest> */
    private function pendingRequests(): Collection
    {
        return MemberChangeRequest::query()
            ->where('member_id', $this->member->id)
            ->whereNull('completed_at')
            ->whereNull('rejected_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /** @return Collection<int, MemberChangeRequest> */
    private function historyRequests(): Collection
    {
        return MemberChangeRequest::query()
            ->where('member_id', $this->member->id)
            ->where(function ($q): void {
                $q->whereNotNull('completed_at')
                    ->orWhereNotNull('rejected_at');
            })
            ->orderBy('reviewed_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.change-request.table', [
            'pendingRequests' => $this->pendingRequests(),
            'historyRequests' => $this->historyRequests(),
        ]);
    }
}
