<?php

declare(strict_types=1);

use App\Mail\CustomNotificationMail;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use App\Models\User;
use App\Services\MailingService;
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
        Mail::assertQueued(CustomNotificationMail::class, function (CustomNotificationMail $mail) use ($member, $event, $posterFileName) {
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
    Mail::assertQueued(CustomNotificationMail::class, 1);
});
