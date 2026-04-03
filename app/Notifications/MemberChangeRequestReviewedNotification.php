<?php

namespace App\Notifications;

use App\Models\MemberChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberChangeRequestReviewedNotification extends Notification
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
        $approved = $this->changeRequest->completed_at !== null;

        $mail = (new MailMessage)
            ->subject(__('change_request.reviewed_notification.subject'))
            ->line(__('change_request.reviewed_notification.intro', [
                'field' => $this->changeRequest->field->label(),
            ]));

        if ($approved) {
            $mail->line(__('change_request.reviewed_notification.approved'));
        } else {
            $mail->line(__('change_request.reviewed_notification.rejected'))
                ->line(__('change_request.reviewed_notification.rejection_reason', [
                    'reason' => $this->changeRequest->rejection_reason ?? '–',
                ]));
        }

        return $mail;
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'member_change_request_id' => $this->changeRequest->id,
            'field' => $this->changeRequest->field->value,
            'approved' => $this->changeRequest->completed_at !== null,
            'rejection_reason' => $this->changeRequest->rejection_reason,
        ];
    }
}
