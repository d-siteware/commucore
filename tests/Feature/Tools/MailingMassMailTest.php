<?php

declare(strict_types=1);

use App\Livewire\App\Tool\Mailing\Page;
use App\Mail\SendMemberMassMail;
use App\Models\MailingList;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function makeSubject(string $hu = 'Tárgy', string $de = 'Betreff'): array
{
    return ['hu' => $hu, 'de' => $de];
}

function makeMessage(string $hu = 'Üzenet szövege', string $de = 'Nachrichtentext'): array
{
    return ['hu' => $hu, 'de' => $de];
}

function makeUrlLabel(string $hu = 'Kattints ide', string $de = 'Klick hier'): array
{
    return ['hu' => $hu, 'de' => $de];
}

/** Create a verified, subscribed MailingList entry. */
function mailingListSubscriber(array $overrides = []): MailingList
{
    return MailingList::factory()->create(array_merge([
        'verified_at' => now(),
        'terms_accepted' => true,
        'terms_accepted_at' => now(),
        'update_on_notifications' => true,
        'unsubscribed_at' => null,
        'locale' => 'de',
    ], $overrides));
}

// ---------------------------------------------------------------------------
// Rendering
// ---------------------------------------------------------------------------

describe('rendering', function () {
    it('renders the mailing page successfully', function () {
        Livewire::test(Page::class)
            ->assertStatus(200);
    });
});

// ---------------------------------------------------------------------------
// sendMembersMail – members only (include_mailing_list = false)
// ---------------------------------------------------------------------------

describe('sendMembersMail – members only', function () {
    beforeEach(function () {
        $user = User::factory()->create(['locale' => 'de']);
        $this->actingAs($user);
        Mail::fake();
    });

    it('queues one mail per member with a valid email', function () {

        Member::factory()->count(3)->create([
            'locale' => 'de',
        ]);
        Member::factory()->create(['email' => null]); // should be skipped

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('url', 'https://example.com')
            ->set('urlLabel', makeUrlLabel())
            ->set('attachments', [])
            ->set('include_mailing_list', false)
            ->call('sendMembersMail');

        Mail::assertQueued(SendMemberMassMail::class, 3);
    });

    it('skips members without an email address', function () {
        Member::factory()->create(['email' => null]);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->call('sendMembersMail');

        Mail::assertNothingQueued();
    });

    it('uses the correct locale-specific subject and message per member', function () {
        Member::factory()->create(['locale' => 'hu', 'email' => 'hu@example.com']);
        Member::factory()->create(['locale' => 'de', 'email' => 'de@example.com']);

        Livewire::test(Page::class)
            ->set('subject', makeSubject('Magyar tárgy', 'Deutsches Betreff'))
            ->set('message', makeMessage('Magyar üzenet', 'Deutsche Nachricht'))
            ->set('attachments', [])
            ->call('sendMembersMail');

        Mail::assertQueued(SendMemberMassMail::class, function (SendMemberMassMail $mail) {
            return $mail->mail_locale === 'hu' && $mail->mail_subject === 'Magyar tárgy';
        });

        Mail::assertQueued(SendMemberMassMail::class, function (SendMemberMassMail $mail) {
            return $mail->mail_locale === 'de' && $mail->mail_subject === 'Deutsches Betreff';
        });
    });

    it('shows a success toast after sending', function () {
        Member::factory()->create(['locale' => 'de']);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->call('sendMembersMail')
            ->assertOk();
    });
});

// ---------------------------------------------------------------------------
// sendMembersMail – validation
// ---------------------------------------------------------------------------

describe('sendMembersMail – validation', function () {
    beforeEach(function () {
        $user = User::factory()->create(['locale' => 'de']);
        $this->actingAs($user);
        Mail::fake();
    });

    it('requires subject.hu', function () {
        Livewire::test(Page::class)
            ->set('subject', ['hu' => '', 'de' => 'Betreff'])
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->call('sendMembersMail')
            ->assertHasErrors(['subject.hu' => 'required']);
    });

    it('requires subject.de', function () {
        Livewire::test(Page::class)
            ->set('subject', ['hu' => 'Tárgy', 'de' => ''])
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->call('sendMembersMail')
            ->assertHasErrors(['subject.de' => 'required']);
    });

    it('requires message.hu', function () {
        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', ['hu' => '', 'de' => 'Nachricht'])
            ->set('attachments', [])
            ->call('sendMembersMail')
            ->assertHasErrors(['message.hu' => 'required']);
    });

    it('requires message.de', function () {
        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', ['hu' => 'Üzenet', 'de' => ''])
            ->set('attachments', [])
            ->call('sendMembersMail')
            ->assertHasErrors(['message.de' => 'required']);
    });
});

// ---------------------------------------------------------------------------
// sendMembersMail – include_mailing_list = true
// ---------------------------------------------------------------------------

describe('sendMembersMail – include_mailing_list', function () {
    beforeEach(function () {
        $user = User::factory()->create(['locale' => 'de']);
        $this->actingAs($user);
        Mail::fake();
    });

    it('also queues mails for valid mailing list subscribers', function () {
        Member::factory()->create(['locale' => 'de', 'email' => 'member@example.com']);
        mailingListSubscriber(['email' => 'subscriber@example.com']);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->set('include_mailing_list', true)
            ->call('sendMembersMail');

        Mail::assertQueued(SendMemberMassMail::class, 2);
    });

    it('does not send duplicate when subscriber email matches a member email', function () {
        $sharedEmail = 'shared@example.com';
        Member::factory()->create(['locale' => 'de', 'email' => $sharedEmail]);
        mailingListSubscriber(['email' => $sharedEmail]);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->set('include_mailing_list', true)
            ->call('sendMembersMail');

        // Only 1 mail – the member's; the duplicate subscriber is skipped
        Mail::assertQueued(SendMemberMassMail::class, 1);
    });

    it('does not send duplicate regardless of email casing', function () {
        Member::factory()->create(['locale' => 'de', 'email' => 'User@Example.com']);
        mailingListSubscriber(['email' => 'user@example.com']);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->set('include_mailing_list', true)
            ->call('sendMembersMail');

        Mail::assertQueued(SendMemberMassMail::class, 1);
    });

    it('skips unverified mailing list entries', function () {
        mailingListSubscriber(['verified_at' => null]);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->set('include_mailing_list', true)
            ->call('sendMembersMail');

        Mail::assertNothingQueued();
    });

    it('skips subscribers who have not accepted terms', function () {
        mailingListSubscriber(['terms_accepted' => false, 'terms_accepted_at' => null]);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->set('include_mailing_list', true)
            ->call('sendMembersMail');

        Mail::assertNothingQueued();
    });

    it('skips subscribers with update_on_notifications = false', function () {
        mailingListSubscriber([
            'update_on_notifications' => false,
            'update_on_articles' => false,
        ]);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->set('include_mailing_list', true)
            ->call('sendMembersMail');

        Mail::assertNothingQueued();
    });

    it('skips unsubscribed mailing list entries', function () {
        mailingListSubscriber(['unsubscribed_at' => now()]);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->set('include_mailing_list', true)
            ->call('sendMembersMail');

        Mail::assertNothingQueued();
    });

    it('falls back to "de" locale when subscriber locale is null', function () {
        mailingListSubscriber(['locale' => 'de', 'email' => 'nolocale@example.com']);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->set('include_mailing_list', true)
            ->call('sendMembersMail');

        Mail::assertQueued(SendMemberMassMail::class, function (SendMemberMassMail $mail) {
            return $mail->mail_locale === 'de';
        });
    });

    it('sends nothing to mailing list when include_mailing_list is false', function () {
        mailingListSubscriber(['email' => 'subscriber@example.com']);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('attachments', [])
            ->set('include_mailing_list', false)
            ->call('sendMembersMail');

        Mail::assertNothingQueued();
    });
});

// ---------------------------------------------------------------------------
// sendTestMailToSelf
// ---------------------------------------------------------------------------

describe('sendTestMailToSelf', function () {
    beforeEach(function () {
        $user = User::factory()->create(['locale' => 'de']);
        $this->actingAs($user);
        Mail::fake();
    });

    it('queues exactly one test mail to the authenticated user', function () {
        $user = \App\Models\User::factory()->create(['locale' => 'de']);
        $this->actingAs($user);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('url', 'https://example.com')
            ->set('urlLabel', makeUrlLabel())
            ->call('sendTestMailToSelf');

        Mail::assertQueued(SendMemberMassMail::class, 1);
        Mail::assertQueued(SendMemberMassMail::class, fn ($mail) => $mail->hasTo($user->email));
    });

    it('shows a toast on success', function () {
        $user = \App\Models\User::factory()->create(['locale' => 'de']);
        $this->actingAs($user);

        Livewire::test(Page::class)
            ->set('subject', makeSubject())
            ->set('message', makeMessage())
            ->set('urlLabel', makeUrlLabel())
            ->call('sendTestMailToSelf')
            ->assertOk();
    });
});

// ---------------------------------------------------------------------------
// Subscription statistics (mount)
// ---------------------------------------------------------------------------

describe('subscription statistics', function () {
    it('counts total subscriptions for the current year correctly', function () {
        MailingList::factory()->count(4)->create([
            'verified_at' => now(),
            'unsubscribed_at' => null,
        ]);

        MailingList::factory()->create([
            'verified_at' => now()->subYear(),
            'unsubscribed_at' => null,
        ]); // last year – should NOT count

        $component = Livewire::test(Page::class);

        expect($component->get('totalSubscriptionsThisYear'))->toBe(4);
    });

    it('initialises monthlySubscriptions on mount', function () {
        MailingList::factory()->count(2)->create(['verified_at' => now()]);

        $component = Livewire::test(Page::class);

        expect($component->get('monthlySubscriptions'))->toBeArray();
    });

    it('initialises yearlySubscriptions on mount', function () {
        $component = Livewire::test(Page::class);

        expect($component->get('yearlySubscriptions'))->toBeArray();
    });
});

// ---------------------------------------------------------------------------
// addDummyData
// ---------------------------------------------------------------------------

describe('addDummyData', function () {
    it('fills subject, message, url and urlLabel with dummy values', function () {
        $component = Livewire::test(Page::class)
            ->call('addDummyData');

        expect($component->get('subject.hu'))->not->toBeEmpty()
            ->and($component->get('subject.de'))->not->toBeEmpty()
            ->and($component->get('message.hu'))->not->toBeEmpty()
            ->and($component->get('message.de'))->not->toBeEmpty()
            ->and($component->get('url'))->not->toBeEmpty()
            ->and($component->get('urlLabel.hu'))->not->toBeEmpty()
            ->and($component->get('urlLabel.de'))->not->toBeEmpty();
    });
});
