<?php

declare(strict_types=1);

test('an event can be created with all fillable fields', function (): void {
    $attributes = [
        'name' => 'Summer Festival',
        'event_date' => \Carbon\Carbon::today()->format('Y-m-d'),
        'start_time' => '14:00:00',
        'end_time' => '18:00:00',
        'title' => ['en' => 'Summer Fest', 'de' => 'Sommerfest'],
        'slug' => ['en' => 'summer-fest', 'de' => 'sommerfest'],
        'excerpt' => ['en' => 'A fun event', 'de' => 'Ein tolles Event'],
        'description' => ['en' => 'Join us!', 'de' => 'Komm mit!'],
        'image' => 'summer.jpg',
        'status' => \App\Enums\EventStatus::DRAFT,
        'entry_fee' => 1000, // Assuming cents, e.g., 10.00
        'entry_fee_discounted' => 800,
        'venue_id' => \App\Models\Venue::factory()->create()->id,
    ];

    $event = new \App\Models\Event\Event($attributes);
    $event->save();

    expect($event->exists)->toBeTrue();

    $freshEvent = $event->fresh();
    expect($freshEvent->name)->toBe($attributes['name']);
    expect($freshEvent->event_date->toDateString())->toBe($attributes['event_date']);
    expect($freshEvent->start_time->toTimeString())->toBe($attributes['start_time']);
    expect($freshEvent->end_time->toTimeString())->toBe($attributes['end_time']);
    expect($freshEvent->title)->toBe($attributes['title']);
    expect($freshEvent->slug)->toBe($attributes['slug']);
    expect($freshEvent->excerpt)->toBe($attributes['excerpt']);
    expect($freshEvent->description)->toBe($attributes['description']);
    expect($freshEvent->image)->toBe($attributes['image']);
    expect($freshEvent->status)->toBe($attributes['status']);
    expect($freshEvent->entry_fee)->toBe($attributes['entry_fee']);
    expect($freshEvent->entry_fee_discounted)->toBe($attributes['entry_fee_discounted']);
    expect($freshEvent->venue_id)->toBe($attributes['venue_id']);

    expect($event->event_date)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($event->start_time->format('H:i'))->toBe('14:00');
    expect($event->end_time->format('H:i'))->toBe('18:00');

    expect($event->event_date)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($event->start_time->format('H:i'))->toBe('14:00');
    expect($event->end_time->format('H:i'))->toBe('18:00');
});

test('an event can be updated', function (): void {
    $event = \App\Models\Event\Event::factory()->create([
        'name' => 'Old Name',
        'event_date' => '2025-01-01',
    ]);

    $event->update([
        'name' => 'New Name',
        'event_date' => '2025-02-01',
    ]);

    expect($event->fresh()->name)->toBe('New Name');
    expect($event->fresh()->event_date->toDateString())->toBe('2025-02-01');
});

test('an event can save with a venue relationship', function (): void {
    $venue = \App\Models\Venue::factory()->create();
    $event = \App\Models\Event\Event::factory()->create(['venue_id' => $venue->id]);

    expect($event->venue)->toBeInstanceOf(\App\Models\Venue::class);
    expect($event->venue->id)->toBe($venue->id);
});

test('an event can save with subscriptions', function (): void {
    $event = \App\Models\Event\Event::factory()->create();
    $subscriptions = \App\Models\Event\EventSubscription::factory()->count(3)->create(['event_id' => $event->id]);

    expect($event->subscriptions)->toHaveCount(3);
    expect(collect($event->subscriptions->pluck('id')->all())->sort()->values()->all())
        ->toBe(collect($subscriptions->pluck('id')->all())->sort()->values()->all());
});
