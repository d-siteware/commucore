<?php

namespace App\Livewire\Member\ChangeRequest;

use App\Enums\MemberChangeField;
use App\Models\MemberChangeRequest;
use App\Models\Membership\Member;
use App\Notifications\MemberChangeRequestNotification;
use Flux\Flux;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Create extends Component
{
    public Member $member;

    public string $field = '';

    public string $requested_value = '';

    public string $reason = '';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'field' => ['required', 'string', 'in:'.implode(',', array_column(MemberChangeField::cases(), 'value'))],
            'requested_value' => ['required', 'string', 'max:255'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function submit(): void
    {
        $this->authorize('create', MemberChangeRequest::class);
        $this->validate();

        $fieldEnum = MemberChangeField::from($this->field);

        if ($fieldEnum->hasOpenRequest($this->member->id)) {
            Flux::toast(
                text: __('change_request.toast.duplicate.text'),
                heading: __('change_request.toast.duplicate.heading'),
                variant: 'warning',
            );

            return;
        }

        $oldValue = match ($fieldEnum) {
            MemberChangeField::TYPE => $this->member->type->value,
            MemberChangeField::FEE_TYPE => $this->member->fee_type->value,
        };

        $changeRequest = MemberChangeRequest::create([
            'member_id' => $this->member->id,
            'field' => $fieldEnum->value,
            'old_value' => $oldValue,
            'requested_value' => $this->requested_value,
            'reason' => $this->reason,
        ]);

        $this->notifyReviewers($changeRequest);

        $this->reset('field', 'requested_value', 'reason');
        Flux::modal('change-request-create')->close();

        Flux::toast(
            text: __('change_request.toast.created.text'),
            heading: __('change_request.toast.created.heading'),
            variant: 'success',
        );

        $this->dispatch('change-request-created');
    }

    private function notifyReviewers(MemberChangeRequest $changeRequest): void
    {
        $reviewers = \App\Models\User::query()
            ->where('is_admin', true)
            ->orWhereHas('member', fn ($q) => $q->whereIn('type', \App\Enums\MemberType::boardTypes()))
            ->get();

        Notification::send($reviewers, new MemberChangeRequestNotification($changeRequest));
    }

    public function render(): \Illuminate\View\View
    {
        $availableFields = collect(MemberChangeField::cases())
            ->reject(fn (MemberChangeField $f) => $f->hasOpenRequest($this->member->id))
            ->mapWithKeys(fn (MemberChangeField $f): array => [$f->value => $f->label()])
            ->all();

        return view('livewire.member.change-request.create', [
            'availableFields' => $availableFields,
        ]);
    }
}
