<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Membership\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class MemberAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Member $member) {}

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('members.accepted.mail.subject'))
            ->greeting(__('members.accepted.mail.greeting', [
                'name' => $this->member->first_name ?? $this->member->name,
            ]))
            ->line(__('members.accepted.mail.line1'))
            ->line(__('members.accepted.mail.line2'));
    }
}
