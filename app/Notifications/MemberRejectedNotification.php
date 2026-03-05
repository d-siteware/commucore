<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Membership\MemberApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class MemberRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly MemberApplication $application) {}

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('members.rejected.mail.subject'))
            ->greeting(__('members.rejected.mail.greeting', [
                'name' => $this->application->first_name ?? $this->application->name,
            ]))
            ->line(__('members.rejected.mail.line1'))
            ->line(__('members.rejected.mail.line2'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type' => 'application_rejected',
            'message' => __('members.rejected.mail.line1'),
            'url' => route('dashboard'),
        ];
    }
}
