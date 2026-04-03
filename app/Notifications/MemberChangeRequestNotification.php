<?php

namespace App\Notifications;

use App\Models\MemberChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberChangeRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly MemberChangeRequest $changeRequest
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('change_request.notification.subject'))
            ->line(__('change_request.notification.intro', [
                'member' => $this->changeRequest->member->fullName(),
                'field' => $this->changeRequest->field->label(),
            ]))
            ->line(__('change_request.notification.old_value', [
                'value' => $this->changeRequest->old_value ?: '–',
            ]))
            ->line(__('change_request.notification.requested_value', [
                'value' => $this->changeRequest->requested_value,
            ]))
            ->line(__('change_request.notification.reason', [
                'reason' => $this->changeRequest->reason,
            ]));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'member_change_request',
            'member_change_request_id' => $this->changeRequest->id,
            'member_id' => $this->changeRequest->member_id,
            'member_name' => $this->changeRequest->member->fullName(),
            'field' => $this->changeRequest->field->value,
            'field_label' => $this->changeRequest->field->label(),
            'requested_value' => $this->changeRequest->requested_value,
            'requested_value_label' => $this->resolveValueLabel(),
            'message' => __('change_request.notification.message', [
                'member' => $this->changeRequest->member->fullName(),
                'field' => $this->changeRequest->field->label(),
                'value' => $this->resolveValueLabel(),
            ]),
            'url' => route('backend.members.show', $this->changeRequest->member_id),
        ];
    }

    private function resolveValueLabel(): string
    {
        return match ($this->changeRequest->field) {
            \App\Enums\MemberChangeField::TYPE => \App\Enums\MemberType::from($this->changeRequest->requested_value)->label(),
            \App\Enums\MemberChangeField::FEE_TYPE => \App\Enums\MemberFeeType::from($this->changeRequest->requested_value)->label(),
        };
    }
}
