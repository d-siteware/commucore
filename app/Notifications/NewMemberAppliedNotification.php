<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Membership\Member;
use App\Models\Membership\MemberApplication;
use App\Notifications\Concerns\HasDatabaseChannelForLinkedUsers;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

final class NewMemberAppliedNotification extends Notification // implements ShouldQueue
{
    // use Queueable;
    use HasDatabaseChannelForLinkedUsers;

    /**
     * Create a new notification instance.
     */
    public function __construct(public MemberApplication $application) {}

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
        return (new MailMessage)
            ->from(Auth::user()->email, Auth::user()->name)
            ->subject(__('members.notifications.new_applicant.subject'))
            ->view(
                'emails.member-application', ['application' => $this->application]
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
            'applied_at' => $this->application->applied_at,
            'fullName' => $this->application->name.', '.$this->application->first_name,
        ];
    }
}
