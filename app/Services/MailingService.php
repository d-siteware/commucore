<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\CustomNotificationMail;
use App\Models\MailingList;
use App\Models\Membership\Member;
use App\Models\User;
use App\Notifications\EventPublishedNotification;
use App\Notifications\PostPublishedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class MailingService
{
    /**
     * Send notifications to subscribers for a given notification type.
     *
     * @param  string  $notificationType  'events' or 'posts'
     * @param  mixed  $notifiable  The notifiable entity (e.g., Event or BlogPost model)
     * @param  string  $subject  The email subject
     * @param  string  $view  The Blade view for the email
     * @param  array  $notificationData  Data to pass to the email view
     */
    public function sendNotificationsToSubscribers(string $notificationType, mixed $notifiable, string $subject, string $view, array $notificationData = []): void
    {
        // Notify backend users via Laravel Notification system
        $this->notifyBackendUsers($notificationType, $notifiable);

        // Fetch unique email recipients (members + mailing list subscribers, deduplicated)
        $emailRecipients = $this->getUniqueEmailRecipients($notificationType);

        // Send emails to members and mailing list subscribers
        foreach ($emailRecipients as $recipient) {

            // In case of an events type nofification
            // check if a poster of the event is
            // present and add it as attachmennt to the email
            if ($notificationType === 'events' && $notifiable->hasPoster($recipient['locale'], 'pdf')) {
                $notificationData['event_poster'] = $notifiable->getFilename($recipient['locale']).'.pdf';
            }

            //            Log::info('Sending email to ', ['data' => $recipient]);
            $recipientData = array_merge($notificationData, [
                'notificationType' => $notificationType,
                'notifiable' => $notifiable,
                'recipient' => $recipient, // Pass the entire recipient array
            ]);

            $subject = $notifiable->getEmailSubject($recipient['locale']);

            Mail::to($recipient['email'])->locale($recipient['locale'])
                ->send(new CustomNotificationMail(
                    $subject,
                    $view,
                    $recipientData
                ));
        }
    }

    /**
     * Notify backend users via Laravel's Notification system.
     *
     * @param  mixed  $notifiable
     */
    protected function notifyBackendUsers(string $notificationType, $notifiable): void
    {
        $users = User::whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($notificationType === 'event') {
            Notification::send($users, new EventPublishedNotification($notifiable));
        } elseif ($notificationType === 'post') {
            Notification::send($users, new PostPublishedNotification($notifiable));
        }
    }

    /**
     * Get a collection of unique email recipients (members and mailing list subscribers).
     */
    protected function getUniqueEmailRecipients(string $notificationType): Collection
    {
        // Fetch members with email addresses (they get all notifications)
        $members = Member::whereNotNull('email')
            ->where('email', '!=', '')
            ->select('id', 'email', 'locale')
            ->get()
            ->map(function ($member): array {
                return ['id' => $member->id, 'email' => $member->email, 'type' => 'member', 'locale' => $member->locale];
            });

        // Fetch mailing list subscribers based on notification type
        $subscribersQuery = MailingList::whereNotNull('email')
            ->where('email', '!=', '');

        if ($notificationType === 'event') {
            $subscribersQuery->where('update_on_events', true);
        } elseif ($notificationType === 'post') {
            $subscribersQuery->where('update_on_articles', true);
        }

        $subscribers = $subscribersQuery->select('id', 'email', 'verification_token', 'verified_at', 'locale')
            ->get()
            ->map(function (MailingList $subscriber): array {
                return [
                    'id' => $subscriber->id,
                    'email' => $subscriber->email,
                    'type' => 'subscriber',
                    'verification_token' => $subscriber->verification_token,
                    'verified_at' => $subscriber->verified_at,
                    'locale' => $subscriber->locale,
                ];
            });

        // Merge and deduplicate based on email
        return $members->merge($subscribers)
            ->unique('email')
            ->values();
    }
}
