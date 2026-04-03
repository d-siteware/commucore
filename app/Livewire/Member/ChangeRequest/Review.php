<?php

namespace App\Livewire\Member\ChangeRequest;

use App\Enums\MemberChangeField;
use App\Models\MemberChangeRequest;
use App\Models\Membership\Member;
use App\Notifications\MemberChangeRequestReviewedNotification;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Review extends Component
{
    public Member $member;

    public ?int $reviewingId = null;

    public string $rejection_reason = '';

    public string $deduction_reason = '';

    public function getListeners(): array
    {
        return [
            'change-request-created' => '$refresh',
        ];
    }

    public function startReview(int $id): void
    {
        $this->reviewingId = $id;
        $this->rejection_reason = '';
        Flux::modal('change-request-review')->show();
    }

    public function approve(): void
    {
        $request = $this->getReviewingRequest();

        $this->authorize('review', $request);

        $member = $request->member;
        $member->{$request->field->value} = $request->requested_value;

        if ($request->field === MemberChangeField::FEE_TYPE) {
            $member->deduction_reason = $this->deduction_reason;
        }

        $member->save();

        $request->update([
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'completed_at' => now(),
        ]);

        $request->member->user?->notify(
            new MemberChangeRequestReviewedNotification($request)
        );

        Flux::modal('change-request-review')->close();
        $this->reviewingId = null;

        Flux::toast(
            text: __('change_request.toast.approved.text'),
            heading: __('change_request.toast.approved.heading'),
            variant: 'success',
        );

        $this->dispatch('change-request-reviewed');
    }

    public function reject(): void
    {
        $this->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $request = $this->getReviewingRequest();

        $this->authorize('review', $request);

        $request->update([
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejected_at' => now(),
            'rejection_reason' => $this->rejection_reason,
        ]);

        $request->member->user?->notify(
            new MemberChangeRequestReviewedNotification($request)
        );

        Flux::modal('change-request-review')->close();
        $this->reviewingId = null;
        $this->rejection_reason = '';

        Flux::toast(
            text: __('change_request.toast.rejected.text'),
            heading: __('change_request.toast.rejected.heading'),
            variant: 'success',
        );

        $this->dispatch('change-request-reviewed');
    }

    private function getReviewingRequest(): MemberChangeRequest
    {
        if ($this->reviewingId === null) {
            abort(404);
        }

        return MemberChangeRequest::findOrFail($this->reviewingId);
    }

    /** @return Collection<int, MemberChangeRequest> */
    private function pendingRequests(): Collection
    {
        return MemberChangeRequest::query()
            ->where('member_id', $this->member->id)
            ->whereNull('completed_at')
            ->whereNull('rejected_at')
            ->orderBy('created_at')
            ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.change-request.review', [
            'pendingRequests' => $this->pendingRequests(),
            'reviewingRequest' => $this->reviewingId
                ? MemberChangeRequest::find($this->reviewingId)
                : null,
        ]);
    }
}
