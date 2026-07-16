<?php

namespace App\Livewire\Member\CancellationRequest;

use App\Enums\MemberType;
use App\Livewire\Traits\HandlesErrors;
use App\Models\MemberCancellationRequest;
use App\Models\Membership\Member;
use App\Notifications\MemberCancellationRequestReviewedNotification;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Review extends Component
{
    use HandlesErrors;
    public Member $member;

    public ?int $reviewingId = null;

    public string $rejection_reason = '';

    public function getListeners(): array
    {
        return [
            'cancellation-request-created' => '$refresh',
        ];
    }

    public function startReview(int $id): void
    {
        $this->reviewingId = $id;
        $this->rejection_reason = '';
        Flux::modal('cancellation-request-review')->show();
    }

    public function approve(): void
    {
        try {
            $request = $this->getReviewingRequest();

            $this->authorize('review', $request);

            $leaveDate = $request->requested_leave_date ?? now()->toDateString();

            $request->member->update([
                'left_at' => $leaveDate,
                'type' => MemberType::EX,
            ]);

            $request->update([
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'confirmed_at' => now(),
            ]);

            $request->member->user?->notify(
                new MemberCancellationRequestReviewedNotification($request)
            );

            Flux::modal('cancellation-request-review')->close();
            $this->reviewingId = null;

            Flux::toast(
                text: __('cancellation_request.toast.approved.text'),
                heading: __('cancellation_request.toast.approved.heading'),
                variant: 'success',
            );

            $this->dispatch('cancellation-request-reviewed');
        } catch (\Throwable $e) {
            $this->handleError('Kündigungsantrag genehmigen fehlgeschlagen', $e);
        }
    }

    public function reject(): void
    {
        try {
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
                new MemberCancellationRequestReviewedNotification($request)
            );

            Flux::modal('cancellation-request-review')->close();
            $this->reviewingId = null;
            $this->rejection_reason = '';

            Flux::toast(
                text: __('cancellation_request.toast.rejected.text'),
                heading: __('cancellation_request.toast.rejected.heading'),
                variant: 'success',
            );

            $this->dispatch('cancellation-request-reviewed');
        } catch (\Throwable $e) {
            $this->handleError('Kündigungsantrag ablehnen fehlgeschlagen', $e);
        }
    }

    private function getReviewingRequest(): MemberCancellationRequest
    {
        if ($this->reviewingId === null) {
            abort(404);
        }

        return MemberCancellationRequest::findOrFail($this->reviewingId);
    }

    /** @return Collection<int, MemberCancellationRequest> */
    private function pendingRequests(): Collection
    {
        return MemberCancellationRequest::query()
            ->where('member_id', $this->member->id)
            ->whereNull('confirmed_at')
            ->whereNull('rejected_at')
            ->orderBy('created_at')
            ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.cancellation-request.review', [
            'pendingRequests' => $this->pendingRequests(),
            'reviewingRequest' => $this->reviewingId
                ? MemberCancellationRequest::find($this->reviewingId)
                : null,
        ]);
    }
}
