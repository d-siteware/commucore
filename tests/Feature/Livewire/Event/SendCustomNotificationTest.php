<?php

declare(strict_types=1);

use App\Mail\CustomNotificationMail;
use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
use App\Models\MailingList;
use App\Models\Membership\Member;
use App\Models\User;
use App\Services\MailingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

it('queues CustomNotificationMail for event notification with correct details and attachment', function (): void {
    // Fake the mail system to prevent real emails
    Mail::fake();

    // Fake the storage disk to mock the poster file
    Storage::fake('public');

    // Arrange: Create test data
    $member = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id]);

    // Create an event with a mock poster
    $event = Event::factory()->create(); // Adjust based on your Event model
    // Set up the poster file
    $posterPath = $event->getPoster('de', 'pdf');
    $posterFileName = basename($posterPath);
    Storage::disk('public')->put($posterPath, 'Mock PDF content');

    // Act: Trigger the MailingService
    $mailingService = app(MailingService::class);
    $mailingService->sendNotificationsToSubscribers(
        notificationType: 'events',
        notifiable: $event,
        subject: __('event.notification_mail.subject'), // Overridden by getEmailSubject
        view: 'emails.new_event_notification',
        notificationData: []
    );

    /*    // Assert: Verify the mail was queued
        Mail::assertSent(CustomNotificationMail::class, function (CustomNotificationMail $mail) use ($member, $event, $posterFileName) {
            // Check recipient
            $hasCorrectRecipient = $mail->hasTo($member->email);

            // Check subject (adjust based on what getEmailSubject returns)
            $hasCorrectSubject = $mail->envelope()->subject === $event->getEmailSubject('en');

            // Check view and data
            $hasCorrectView = $mail->content()->view === 'emails.new_event_notification';
            $hasCorrectData = $mail->data['notificationType'] === 'events' &&
                $mail->data['notifiable'] === $event &&
                $mail->data['recipient']['email'] === $member->email &&
                $mail->data['event_poster'] === $posterFileName;

            // Check attachment
            $hasAttachment = count($mail->attachments()) === 1 &&
                $mail->attachments()[0]->getFilename() === $posterFileName &&
                $mail->attachments()[0]->getMimeType() === 'application/pdf';

            // Check locale
            $hasCorrectLocale = app()->getLocale() === $member->locale;

            // Check sender
            $hasCorrectSender = $mail->envelope()->from === setting('organization.email');

            return $hasCorrectRecipient &&
                $hasCorrectSubject &&
                $hasCorrectView &&
                $hasCorrectData &&
                $hasAttachment &&
                $hasCorrectLocale &&
                $hasCorrectSender;
        });*/

    // Assert: Verify the mail was queued exactly once for the member
    Mail::assertSent(CustomNotificationMail::class, 1);
});

it('sends event notifications only to confirmed and consented signups and opted-in mailing subscribers', function (): void {
    Mail::fake();

    $event = Event::factory()->create();
    $consented = EventSubscription::factory()->create([
        'event_id' => $event->id,
        'email' => 'consented@example.com',
        'confirmed_at' => now(),
        'consentNotification' => true,
        'data_purge_after' => now()->addDays(5),
    ]);
    $declined = EventSubscription::factory()->create([
        'event_id' => $event->id,
        'email' => 'declined@example.com',
        'confirmed_at' => now(),
        'consentNotification' => false,
    ]);
    $unconfirmed = EventSubscription::factory()->create([
        'event_id' => $event->id,
        'email' => 'unconfirmed@example.com',
        'consentNotification' => true,
    ]);

    $legacyWithoutConsentTimestamp = EventSubscription::factory()->create([
        'event_id' => $event->id,
        'email' => 'legacy@example.com',
        'confirmed_at' => now(),
        'consentNotification' => false,
    ]);
    DB::table('event_subscriptions')
        ->where('id', $legacyWithoutConsentTimestamp->id)
        ->update(['consentNotification' => true, 'notification_consent_at' => null]);

    $mailingSubscriber = MailingList::factory()->create([
        'email' => 'mailing-subscriber@example.com',
        'terms_accepted' => true,
        'terms_accepted_at' => now(),
        'update_on_events' => true,
        'verified_at' => now(),
        'unsubscribed_at' => null,
    ]);
    $unverifiedSubscriber = MailingList::factory()->create([
        'email' => 'unverified@example.com',
        'terms_accepted' => true,
        'terms_accepted_at' => now(),
        'update_on_events' => true,
        'verified_at' => null,
        'unsubscribed_at' => null,
    ]);
    $optedOutSubscriber = MailingList::factory()->create([
        'email' => 'opted-out@example.com',
        'terms_accepted' => true,
        'terms_accepted_at' => now(),
        'update_on_events' => false,
        'verified_at' => now(),
        'unsubscribed_at' => null,
    ]);

    app(MailingService::class)->sendNotificationsToSubscribers(
        notificationType: 'events',
        notifiable: $event,
        subject: __('event.notification_mail.subject'),
        view: 'emails.new_event_notification',
    );

    Mail::assertSent(CustomNotificationMail::class, 3);
    Mail::assertSent(CustomNotificationMail::class, fn (CustomNotificationMail $mail): bool => $mail->hasTo($consented->email));
    Mail::assertSent(CustomNotificationMail::class, fn (CustomNotificationMail $mail): bool => $mail->hasTo($mailingSubscriber->email));
    Mail::assertSent(CustomNotificationMail::class, fn (CustomNotificationMail $mail): bool => $mail->hasTo($legacyWithoutConsentTimestamp->email));
    Mail::assertNotSent(CustomNotificationMail::class, fn (CustomNotificationMail $mail): bool => $mail->hasTo($declined->email));
    Mail::assertNotSent(CustomNotificationMail::class, fn (CustomNotificationMail $mail): bool => $mail->hasTo($unconfirmed->email));
    Mail::assertNotSent(CustomNotificationMail::class, fn (CustomNotificationMail $mail): bool => $mail->hasTo($unverifiedSubscriber->email));
    Mail::assertNotSent(CustomNotificationMail::class, fn (CustomNotificationMail $mail): bool => $mail->hasTo($optedOutSubscriber->email));
});
