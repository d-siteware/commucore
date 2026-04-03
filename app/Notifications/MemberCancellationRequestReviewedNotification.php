<?php

namespace App\Notifications;

use App\Models\MemberCancellationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberCancellationRequestReviewedNotification extends Notification
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
        $confirmed = $this->cancellationRequest->confirmed_at !== null;

        $mail = (new MailMessage)
            ->subject(__('cancellation_request.reviewed_notification.subject'));

        if ($confirmed) {
            $mail->line(__('cancellation_request.reviewed_notification.confirmed'));

            if ($this->cancellationRequest->requested_leave_date !== null) {
                $mail->line(__('cancellation_request.reviewed_notification.leave_date', [
                    'date' => $this->cancellationRequest->requested_leave_date->format('d.m.Y'),
                ]));
            }
        } else {
            $mail->line(__('cancellation_request.reviewed_notification.rejected'))
                ->line(__('cancellation_request.reviewed_notification.rejection_reason', [
                    'reason' => $this->cancellationRequest->rejection_reason ?? '–',
                ]));
        }

        return $mail;
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'member_cancellation_request_id' => $this->cancellationRequest->id,
            'confirmed' => $this->cancellationRequest->confirmed_at !== null,
            'requested_leave_date' => $this->cancellationRequest->requested_leave_date?->format('Y-m-d'),
            'rejection_reason' => $this->cancellationRequest->rejection_reason,
        ];
    }
}
