<?php

namespace App\Notifications;

use App\Models\MemberCancellationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberCancellationRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly MemberCancellationRequest $cancellationRequest
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('cancellation_request.notification.subject'))
            ->line(__('cancellation_request.notification.intro', [
                'member' => $this->cancellationRequest->member->fullName(),
            ]))
            ->line(__('cancellation_request.notification.reason', [
                'reason' => $this->cancellationRequest->reason,
            ]));

        if ($this->cancellationRequest->requested_leave_date !== null) {
            $mail->line(__('cancellation_request.notification.leave_date', [
                'date' => \App\Helpers\DateHelper::formatDate($this->cancellationRequest->requested_leave_date),
            ]));
        }

        return $mail;
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'member_cancellation_request',
            'member_cancellation_request_id' => $this->cancellationRequest->id,
            'member_id' => $this->cancellationRequest->member_id,
            'member_name' => $this->cancellationRequest->member->fullName(),
            'requested_leave_date' => $this->cancellationRequest->requested_leave_date?->format('Y-m-d'),
            'message' => __('cancellation_request.notification.message', [
                'member' => $this->cancellationRequest->member->fullName(),
            ]),
            'url' => route('backend.members.show', $this->cancellationRequest->member_id),
        ];
    }
}
