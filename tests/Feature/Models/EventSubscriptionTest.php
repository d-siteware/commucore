<?php

declare(strict_types=1);

use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
use Carbon\Carbon;

describe('EventSubscription model', function (): void {
    it('can be created with factory', function (): void {
        $subscription = EventSubscription::factory()->create();

        expect($subscription)->toBeInstanceOf(EventSubscription::class)
            ->and($subscription->event)->toBeInstanceOf(Event::class);
    });

    it('has fillable attributes', function (): void {
        $event = Event::factory()->create();

        $subscription = EventSubscription::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+123456789',
            'remarks' => 'Looking forward!',
            'brings_guests' => true,
            'amount_guests' => 2,
            'event_id' => $event->id,
            'confirmed_at' => now(),
            'consentNotification' => true,
        ]);

        expect($subscription->name)->toBe('John Doe')
            ->and($subscription->email)->toBe('john@example.com')
            ->and($subscription->phone)->toBe('+123456789')
            ->and($subscription->remarks)->toBe('Looking forward!')
            ->and($subscription->event_id)->toBe($event->id);
    });

    it('casts booleans correctly', function (): void {
        $subscription = EventSubscription::factory()->create([
            'consentNotification' => 1,
            'brings_guests' => 0,
        ]);

        expect($subscription->consentNotification)->toBeTrue()
            ->and($subscription->brings_guests)->toBeFalse();
    });

    it('casts datetime fields', function (): void {
        $subscription = EventSubscription::factory()->create([
            'confirmed_at' => '2024-06-15 14:30:00',
        ]);

        expect($subscription->confirmed_at)->toBeInstanceOf(Carbon::class)
            ->and($subscription->confirmed_at->format('Y-m-d H:i:s'))->toBe('2024-06-15 14:30:00');
    });

    it('belongs to an event', function (): void {
        $event = Event::factory()->create();
        $subscription = EventSubscription::factory()->create(['event_id' => $event->id]);

        expect($subscription->event)->toBeInstanceOf(Event::class)
            ->and($subscription->event->id)->toBe($event->id);
    });

    it('auto-sets data_purge_after on creation based on event date', function (): void {
        $event = Event::factory()->create(['event_date' => '2024-07-01']);
        $subscription = EventSubscription::factory()->create(['event_id' => $event->id]);

        expect($subscription->data_purge_after)->toBeInstanceOf(Carbon::class)
            ->and($subscription->data_purge_after->format('Y-m-d'))->toBe('2024-07-31');
    });

    it('casts data_purge_after and notification_consent_at as datetime', function (): void {
        $subscription = EventSubscription::factory()->create([
            'data_purge_after' => '2024-08-01 12:00:00',
            'notification_consent_at' => '2024-06-01 10:00:00',
        ]);

        expect($subscription->data_purge_after)->toBeInstanceOf(Carbon::class)
            ->and($subscription->notification_consent_at)->toBeInstanceOf(Carbon::class);
    });
});
