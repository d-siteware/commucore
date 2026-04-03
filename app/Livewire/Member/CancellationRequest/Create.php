<?php

namespace App\Livewire\Member\CancellationRequest;

use App\Models\MemberCancellationRequest;
use App\Models\Membership\Member;
use App\Notifications\MemberCancellationRequestNotification;
use Flux\Flux;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Create extends Component
{
    public Member $member;

    public string $reason = '';

    public string $requested_leave_date = '';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
            'requested_leave_date' => ['nullable', 'date', 'after:today'],
        ];
    }

    public function submit(): void
    {
        $this->authorize('create', MemberCancellationRequest::class);
        $this->validate();

        $hasOpen = MemberCancellationRequest::query()
            ->where('member_id', $this->member->id)
            ->whereNull('confirmed_at')
            ->whereNull('rejected_at')
            ->exists();

        if ($hasOpen) {
            Flux::toast(
                text: __('cancellation_request.toast.duplicate.text'),
                heading: __('cancellation_request.toast.duplicate.heading'),
                variant: 'warning',
            );

            return;
        }

        $cancellationRequest = MemberCancellationRequest::create([
            'member_id' => $this->member->id,
            'reason' => $this->reason,
            'requested_leave_date' => $this->requested_leave_date ?: null,
        ]);

        $this->notifyReviewers($cancellationRequest);

        $this->reset('reason', 'requested_leave_date');
        Flux::modal('cancellation-request-create')->close();

        Flux::toast(
            text: __('cancellation_request.toast.created.text'),
            heading: __('cancellation_request.toast.created.heading'),
            variant: 'success',
        );

        $this->dispatch('cancellation-request-created');
    }

    private function notifyReviewers(MemberCancellationRequest $cancellationRequest): void
    {
        $reviewers = \App\Models\User::query()
            ->where('is_admin', true)
            ->orWhereHas('member', fn ($q) => $q->whereIn('type', \App\Enums\MemberType::boardTypes()))
            ->get();

        Notification::send($reviewers, new MemberCancellationRequestNotification($cancellationRequest));
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.cancellation-request.create');
    }
}
