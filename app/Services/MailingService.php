<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\CustomNotificationMail;
use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
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
        $isEventNotification = in_array($notificationType, ['event', 'events'], true);
        $isPostNotification = in_array($notificationType, ['post', 'posts'], true);

        $this->notifyBackendUsers($isEventNotification ? 'event' : ($isPostNotification ? 'post' : $notificationType), $notifiable);

        // Fetch unique email recipients (members + mailing list subscribers, deduplicated)
        $emailRecipients = $this->getUniqueEmailRecipients($notificationType, $notifiable);

        // Send emails to members and mailing list subscribers
        foreach ($emailRecipients as $recipient) {

            // In case of an events type nofification
            // check if a poster of the event is
            // present and add it as attachmennt to the email
            if ($isEventNotification && $notifiable->hasPoster($recipient['locale'], 'pdf')) {
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
    protected function getUniqueEmailRecipients(string $notificationType, mixed $notifiable): Collection
    {
        $isEventNotification = in_array($notificationType, ['event', 'events'], true);
        $isPostNotification = in_array($notificationType, ['post', 'posts'], true);

        // Fetch members with email addresses (they get all notifications)
        $members = Member::whereNotNull('email')
            ->where('email', '!=', '')
            ->select('id', 'email', 'locale')
            ->get()
            ->map(function ($member): array {
                return ['id' => $member->id, 'email' => $member->email, 'type' => 'member', 'locale' => $member->locale];
            });

        // Fetch mailing list subscribers based on notification type
        $subscribersQuery = MailingList::subscribed()
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if ($isEventNotification) {
            $subscribersQuery->where('update_on_events', true);
        } elseif ($isPostNotification) {
            $subscribersQuery->where('update_on_articles', true);
        }

        $subscribersQuery
            ->where('terms_accepted', true);

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

        $eventSubscriptions = collect();

        if ($isEventNotification && $notifiable instanceof Event) {
            $eventSubscriptions = EventSubscription::query()
                ->where('event_id', $notifiable->id)
                ->whereNotNull('confirmed_at')
                ->where('consentNotification', true)
                ->whereNotNull('data_purge_after')
                ->whereDate('data_purge_after', '>', now()->toDateString())
                ->get(['id', 'email'])
                ->map(fn (EventSubscription $subscription): array => [
                    'id' => $subscription->id,
                    'email' => $subscription->email,
                    'type' => 'subscriber',
                    'locale' => app()->getLocale(),
                ])
                ->all();
        }

        // Merge and deduplicate based on email
        return collect($members->all())
            ->merge($subscribers->all())
            ->merge($eventSubscriptions)
            ->unique('email')
            ->values();
    }
}
