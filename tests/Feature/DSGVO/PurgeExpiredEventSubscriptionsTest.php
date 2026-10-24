<?php

declare(strict_types=1);

use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
use App\Models\Event\EventVisitor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

// ── Helpers ───────────────────────────────────────────────────────────────────

function makeEvent(string $eventDate): Event
{
    return Event::factory()->create(['event_date' => $eventDate]);
}

function makeSubscription(Event $event, ?string $datePurgeAfter = null): EventSubscription
{
    return EventSubscription::factory()->create([
        'event_id' => $event->id,
        'data_purge_after' => $datePurgeAfter,
    ]);
}

// ── boot() – data_purge_after auto-set ───────────────────────────────────────

it('sets data_purge_after to event_date + 30 days on create', function (): void {
    $event = makeEvent(Carbon::today()->toDateString());
    $subscription = makeSubscription($event);

    expect($subscription->data_purge_after->toDateString())
        ->toBe(Carbon::today()->addDays(30)->toDateString());
});

it('does not overwrite data_purge_after if already set', function (): void {
    $event = makeEvent(Carbon::today()->toDateString());
    $custom = Carbon::today()->addDays(60)->toDateString();
    $subscription = makeSubscription($event, $custom);

    expect($subscription->data_purge_after->toDateString())->toBe($custom);
});

// ── Command: purge ────────────────────────────────────────────────────────────

it('deletes subscriptions whose data_purge_after has passed', function (): void {
    $event = makeEvent(Carbon::today()->subDays(40)->toDateString());

    $expired = makeSubscription($event, Carbon::yesterday()->toDateString());
    $valid = makeSubscription($event, Carbon::tomorrow()->toDateString());

    $this->artisan('gdpr:purge-event-subscriptions')->assertSuccessful();

    expect(EventSubscription::find($expired->id))->toBeNull()
        ->and(EventSubscription::find($valid->id))->not->toBeNull();
});

it('does not delete anything in dry-run mode', function (): void {
    $event = makeEvent(Carbon::today()->subDays(40)->toDateString());
    $expired = makeSubscription($event, Carbon::yesterday()->toDateString());

    $this->artisan('gdpr:purge-event-subscriptions --dry-run')->assertSuccessful();

    expect(EventSubscription::find($expired->id))->not->toBeNull();
});

it('reports nothing to do when no event data is expired', function (): void {
    $event = makeEvent(Carbon::today()->toDateString());
    makeSubscription($event, Carbon::tomorrow()->toDateString());

    $this->artisan('gdpr:purge-event-subscriptions')
        ->expectsOutput('No expired event data to purge.')
        ->assertSuccessful();
});

it('uses the event date for legacy subscriptions without data_purge_after', function (): void {
    $event = makeEvent(Carbon::today()->subDays(40)->toDateString());

    // Direkt via DB um boot() zu umgehen
    $id = DB::table('event_subscriptions')->insertGetId([
        'event_id' => $event->id,
        'name' => 'Test',
        'email' => 'no-purge@example.com',
        'brings_guests' => false,
        'amount_guests' => 0,
        'consentNotification' => false,
        'data_purge_after' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->artisan('gdpr:purge-event-subscriptions')->assertSuccessful();

    expect(DB::table('event_subscriptions')->find($id))->toBeNull();
});

it('anonymizes expired visitors while retaining event and transaction links', function (): void {
    $event = makeEvent(Carbon::today()->subDays(40)->toDateString());
    $subscription = makeSubscription($event, Carbon::yesterday()->toDateString());
    $visitor = EventVisitor::factory()->create([
        'event_id' => $event->id,
        'event_subscription_id' => $subscription->id,
        'name' => 'Test Visitor',
        'email' => 'visitor@example.com',
        'phone' => '123456789',
        'gender' => 'f',
        'member_id' => 42,
        'transaction_id' => 84,
    ]);

    $this->artisan('gdpr:purge-event-subscriptions')->assertSuccessful();

    $visitor->refresh();
    expect($visitor->name)->toBe('Anonymisiert')
        ->and($visitor->email)->toBeNull()
        ->and($visitor->phone)->toBeNull()
        ->and($visitor->gender)->toBeNull()
        ->and($visitor->member_id)->toBeNull()
        ->and($visitor->event_subscription_id)->toBeNull()
        ->and($visitor->event_id)->toBe($event->id)
        ->and($visitor->transaction_id)->toBe(84)
        ->and(EventSubscription::find($subscription->id))->toBeNull();
});

it('does not anonymize visitors in dry-run mode', function (): void {
    $event = makeEvent(Carbon::today()->subDays(40)->toDateString());
    $visitor = EventVisitor::factory()->create([
        'event_id' => $event->id,
        'name' => 'Test Visitor',
        'email' => 'visitor@example.com',
    ]);

    $this->artisan('gdpr:purge-event-subscriptions --dry-run')->assertSuccessful();

    expect($visitor->fresh()->name)->toBe('Test Visitor')
        ->and($visitor->fresh()->email)->toBe('visitor@example.com');
});

// ── Audit log ─────────────────────────────────────────────────────────────────

it('writes a log entry per chunk when deleting', function (): void {
    Log::spy();

    $event = makeEvent(Carbon::today()->subDays(40)->toDateString());
    makeSubscription($event, Carbon::yesterday()->toDateString());

    $this->artisan('gdpr:purge-event-subscriptions');

    Log::shouldHaveReceived('info')
        ->withArgs(static fn (string $channel): bool => $channel === 'gdpr.purged_event_subscriptions'
        );
});
