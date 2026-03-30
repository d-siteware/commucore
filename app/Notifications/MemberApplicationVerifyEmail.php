<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Membership\MemberApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class MemberApplicationVerifyEmail extends Notification
{
    use Queueable;

    public function __construct(private readonly MemberApplication $application) {}

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $url = route('members.application.verify', ['token' => $this->application->token]);

        return (new MailMessage)
            ->subject(__('members.apply.verify.mail.subject', ['organization' => setting('organization.name')]))
            ->view('emails.member-application-reply', ['url' => $url, 'applicant' => $this->application]);
    }
}
