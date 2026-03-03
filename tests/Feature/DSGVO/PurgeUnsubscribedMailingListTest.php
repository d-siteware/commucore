<?php

declare(strict_types=1);

use App\Livewire\App\Global\Mailinglist\Unsubscribe;
use App\Models\MailingList;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Helpers ───────────────────────────────────────────────────────────────────

function makeSubscriber(array $overrides = []): MailingList
{
    $subscriber = MailingList::factory()->create(array_merge([
        'verified_at' => now(),
        'unsubscribed_at' => null,
        'terms_accepted' => true,
        'terms_accepted_at' => now(),
        'locale' => 'de',
    ], $overrides));

    // Token direkt aus DB holen – boot() überschreibt den Factory-Wert
    return $subscriber->fresh();
}

// ── Unsubscribe Component ─────────────────────────────────────────────────────

it('sets unsubscribed_at instead of hard-deleting', function (): void {
    $subscriber = makeSubscriber();
    $token = $subscriber->verification_token; // echten Token nehmen

    Livewire::test(Unsubscribe::class, ['token' => $token]);

    $updated = MailingList::find($subscriber->id);

    expect($updated)->not->toBeNull()
        ->and($updated->unsubscribed_at)->not->toBeNull()
        ->and($updated->verification_token)->toBeNull();
});

it('invalidates token on unsubscribe', function (): void {
    $subscriber = makeSubscriber();
    $token = $subscriber->verification_token;

    Livewire::test(Unsubscribe::class, ['token' => $token]);

    expect(MailingList::find($subscriber->id)?->verification_token)->toBeNull();
});

it('redirects home when token is invalid', function (): void {
    Livewire::test(Unsubscribe::class, ['token' => 'invalid-token'])
        ->assertRedirect(route('home'));
});

// ── Subscribed scope ──────────────────────────────────────────────────────────

it('excludes unsubscribed entries from subscribed scope', function (): void {
    makeSubscriber(['email' => 'active@example.com']);
    makeSubscriber(['email' => 'gone@example.com', 'unsubscribed_at' => now()]);

    $subscribed = MailingList::subscribed()->pluck('email');

    expect($subscribed)->toContain('active@example.com')
        ->and($subscribed)->not->toContain('gone@example.com');
});

it('excludes unverified entries from subscribed scope', function (): void {
    makeSubscriber(['email' => 'unverified@example.com', 'verified_at' => null]);

    $subscribed = MailingList::subscribed()->pluck('email');

    expect($subscribed)->not->toContain('unverified@example.com');
});

// ── PurgeUnsubscribedMailingListCommand ───────────────────────────────────────

it('hard-deletes entries unsubscribed longer than retention period', function (): void {
    $old = makeSubscriber(['email' => 'old@example.com',   'unsubscribed_at' => now()->subDays(31)]);
    $fresh = makeSubscriber(['email' => 'fresh@example.com', 'unsubscribed_at' => now()->subDays(5)]);

    $this->artisan('gdpr:purge-unsubscribed-mailing-list --days=30')
        ->assertSuccessful();

    expect(MailingList::find($old->id))->toBeNull()
        ->and(MailingList::find($fresh->id))->not->toBeNull();
});

it('does not delete active subscribers', function (): void {
    $active = makeSubscriber(['email' => 'active@example.com', 'unsubscribed_at' => null]);

    $this->artisan('gdpr:purge-unsubscribed-mailing-list --days=30')
        ->assertSuccessful();

    expect(MailingList::find($active->id))->not->toBeNull();
});

it('does not modify anything in dry-run mode', function (): void {
    $old = makeSubscriber(['unsubscribed_at' => now()->subDays(31)]);

    $this->artisan('gdpr:purge-unsubscribed-mailing-list --dry-run')
        ->assertSuccessful();

    expect(MailingList::find($old->id))->not->toBeNull();
});

it('reports nothing to do when no entries qualify', function (): void {
    makeSubscriber(['unsubscribed_at' => now()->subDays(5)]);

    $this->artisan('gdpr:purge-unsubscribed-mailing-list --days=30')
        ->expectsOutput('No unsubscribed entries to purge.')
        ->assertSuccessful();
});

it('fails when --days is less than 1', function (): void {
    $this->artisan('gdpr:purge-unsubscribed-mailing-list --days=0')
        ->assertFailed();
});

it('writes a log entry per chunk when purging', function (): void {
    Log::spy();

    makeSubscriber(['unsubscribed_at' => now()->subDays(31)]);

    $this->artisan('gdpr:purge-unsubscribed-mailing-list --days=30');

    Log::shouldHaveReceived('info')
        ->withArgs(static fn (string $channel): bool => $channel === 'gdpr.purged_mailing_list_entries'
        );
});
