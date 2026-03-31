<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Membership\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

final class MembershipCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Member $member, public Carbon $leftAt) {}

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
            ->from(setting('organization.email'), setting('organization.name', 'Helpdesk'))
            ->subject(__('members.notifications.new_applicant.reply_subject', ['name' => setting('organization.name')]))
            ->view('emails.member-acceptance', ['member' => $this->member]);
    }
}
