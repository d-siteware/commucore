<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Membership\MemberApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ApplianceReceivedNotification extends Notification
{
    use Queueable;

    protected MemberApplication $applicant;

    /**
     * Create a new notification instance.
     */
    public function __construct(MemberApplication $applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        app()->setLocale($this->applicant->locale ?? 'de');

        return (new MailMessage)
            ->subject(__('members.appliance_received.mail.subject'))
            ->from(setting('organization.email'), setting('organization.name', 'Helpdesk'))
            ->view(
                'emails.member-application-reply', ['applicant' => $this->applicant]
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
