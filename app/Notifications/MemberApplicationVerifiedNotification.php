<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Membership\MemberApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class MemberApplicationVerifiedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly MemberApplication $application) {}

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        if ($notifiable instanceof User) {
            return ['database'];
        }

        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type' => 'application_verified',
            'message' => __('notification.members.application.verified.message', [
                'name' => $this->application->name.', '.$this->application->first_name,
                'date' => $this->application->verified_at->isoFormat('LLLL'),
            ]),
            'name' => $this->application->name.', '.$this->application->first_name,
            'email' => $this->application->email,
            'url' => route('dashboard'),
        ];
    }
}
