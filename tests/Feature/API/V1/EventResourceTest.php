<?php

declare(strict_types=1);

use App\Enums\EventStatus;
use App\Enums\Locale;
use App\Models\Event\Event;
use App\Models\Venue;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns a list of published events', function (): void {
    // Arrange: Create test data
    $venue = Venue::factory()->create([
        'name' => 'Test Venue',
        'address' => '123 Test Street',
        'city' => 'Test City',
        'website' => 'https://testvenue.com',
    ]);
    $date = \Carbon\Carbon::now()->addWeeks(2)->format('Y-m-d');
    $date2 = \Carbon\Carbon::now()->addWeeks(2)->addDays(3)->format('Y-m-d');
    $event1 = Event::factory()->create([
        'status' => EventStatus::PUBLISHED->value,
        'venue_id' => $venue->id,
        'slug' => [
            Locale::HU->value => 'event-one-hu',
            Locale::DE->value => 'event-one-de',
        ],
        'title' => [
            Locale::HU->value => 'Event One HU',
            Locale::DE->value => 'Event One DE',
        ],
        'excerpt' => [
            Locale::HU->value => 'Excerpt HU 1',
            Locale::DE->value => 'Excerpt DE 1',
        ],
        'description' => [
            Locale::HU->value => 'Description HU 1',
            Locale::DE->value => 'Description DE 1',
        ],
        'event_date' => $date,
    ]);
    $event2 = Event::factory()->create([
        'status' => EventStatus::PUBLISHED->value,
        'venue_id' => $venue->id,
        'slug' => [
            Locale::HU->value => 'event-two-hu',
            Locale::DE->value => 'event-two-de',
        ],
        'title' => [
            Locale::HU->value => 'Event Two HU',
            Locale::DE->value => 'Event Two DE',
        ],
        'excerpt' => [
            Locale::HU->value => 'Excerpt HU 2',
            Locale::DE->value => 'Excerpt DE 2',
        ],
        'description' => [
            Locale::HU->value => 'Description HU 2',
            Locale::DE->value => 'Description DE 2',
        ],
        'event_date' => $date2,
    ]);
    Event::factory()->create([
        'status' => EventStatus::DRAFT->value,
    ]);

    // Act: Make a GET request to the API
    $response = $this->getJson('/api/v1/events');

    // Assert: Check response
    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'title' => [Locale::HU->value, Locale::DE->value],
                    'slug' => [Locale::HU->value, Locale::DE->value],
                    'excerpt' => [Locale::HU->value, Locale::DE->value],
                    'description' => [Locale::HU->value, Locale::DE->value],
                    'event_date',
                    'start_time',
                    'end_time',
                    'image',
                    'entry_fee',
                    'venue' => [
                        'name',
                        'address',
                        'city',
                        'website',
                    ],
                    'links' => [Locale::HU->value, Locale::DE->value],
                ],
            ],
            'meta' => ['count', 'locale', 'timestamp'],
        ])
        ->assertJson(fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson => $json->has('data', 2) // Only published events
            ->has('data.0', fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson => $json->where('slug.hu', 'event-one-hu')
                ->where('slug.de', 'event-one-de')
                ->where('title.hu', 'Event One HU')
                ->where('title.de', 'Event One DE')
                ->where('excerpt.hu', 'Excerpt HU 1')
                ->where('excerpt.de', 'Excerpt DE 1')
                ->where('description.hu', 'Description HU 1')
                ->where('description.de', 'Description DE 1')
                ->where('event_date', $date)
                ->where('links.hu', route('events.show', ['slug' => 'event-one-hu']))
                ->where('links.de', route('events.show', ['slug' => 'event-one-de']))
                ->etc()
            )
            ->has('data.1', fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson => $json->where('slug.hu', 'event-two-hu')
                ->where('slug.de', 'event-two-de')
                ->where('title.hu', 'Event Two HU')
                ->where('title.de', 'Event Two DE')
                ->where('excerpt.hu', 'Excerpt HU 2')
                ->where('excerpt.de', 'Excerpt DE 2')
                ->where('description.hu', 'Description HU 2')
                ->where('description.de', 'Description DE 2')
                ->where('event_date', $date2)
                ->etc()
            )
            ->where('meta.count', 2)
            ->where('meta.locale', App::getLocale())
        );
});

it('returns a single published event by slug', function (): void {
    // Arrange: Create test data
    $venue = Venue::factory()->create([
        'name' => 'Test Venue',
        'address' => '123 Test Street',
        'city' => 'Test City',
        'website' => 'https://testvenue.com',
    ]);
    $event = Event::factory()->create([
        'status' => EventStatus::PUBLISHED->value,
        'venue_id' => $venue->id,
        'slug' => [
            Locale::HU->value => 'test-event-hu',
            Locale::DE->value => 'test-event-de',
        ],
        'title' => [
            Locale::HU->value => 'Test Event HU',
            Locale::DE->value => 'Test Event DE',
        ],
        'excerpt' => [
            Locale::HU->value => 'Excerpt HU',
            Locale::DE->value => 'Excerpt DE',
        ],
        'description' => [
            Locale::HU->value => 'Description HU',
            Locale::DE->value => 'Description DE',
        ],
        'event_date' => '2025-06-20',
        'start_time' => '18:00:00',
        'end_time' => '22:00:00',
        'image' => 'events/test.jpg',
        'entry_fee' => 1000,
    ]);

    // Act: Test with HU slug
    $response = $this->getJson('/api/v1/event/test-event-hu');

    // Assert: Check response
    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'title' => [Locale::HU->value, Locale::DE->value],
                'slug' => [Locale::HU->value, Locale::DE->value],
                'excerpt' => [Locale::HU->value, Locale::DE->value],
                'description' => [Locale::HU->value, Locale::DE->value],
                'event_date',
                'start_time',
                'end_time',
                'image',
                'entry_fee',
                'venue' => [
                    'name',
                    'address',
                    'city',
                    'website',
                ],
                'links' => [Locale::HU->value, Locale::DE->value],
            ],
            'meta' => ['locale', 'timestamp'], // Added meta
        ])
        ->assertJson(fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson => $json->has('data', fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson => $json->where('slug', ['hu' => 'test-event-hu', 'de' => 'test-event-de'])
            ->where('title', ['hu' => 'Test Event HU', 'de' => 'Test Event DE'])
            ->where('excerpt', ['hu' => 'Excerpt HU', 'de' => 'Excerpt DE'])
            ->where('description', ['hu' => 'Description HU', 'de' => 'Description DE'])
            ->where('event_date', '2025-06-20')
            ->where('start_time', '18:00')
            ->where('end_time', '22:00')
            ->where('image', asset('storage/events/test.jpg'))
            ->where('entry_fee', 1000)
            ->where('venue', [
                'name' => 'Test Venue',
                'address' => '123 Test Street',
                'city' => 'Test City',
                'website' => 'https://testvenue.com',
            ])
            ->where('links', [
                'hu' => route('events.show', ['slug' => 'test-event-hu']),
                'de' => route('events.show', ['slug' => 'test-event-de']),
            ])
            ->etc()
        )
            ->where('meta.locale', App::getLocale())
        );

    // Act: Test with DE slug
    $response = $this->getJson('/api/v1/event/test-event-de');

    // Assert: Same event, different slug
    $response
        ->assertStatus(200)
        ->assertJson(fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson => $json->has('data', fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson => $json->where('slug', ['hu' => 'test-event-hu', 'de' => 'test-event-de'])
            ->where('title', ['hu' => 'Test Event HU', 'de' => 'Test Event DE'])
            ->etc()
        )
            ->where('meta.locale', App::getLocale())
        );
});

it('returns 404 for unpublished event slug', function (): void {
    // Arrange: Create an unpublished event
    $event = Event::factory()->create([
        'status' => EventStatus::DRAFT->value,
        'slug' => [
            Locale::HU->value => 'draft-event-hu',
            Locale::DE->value => 'draft-event-de',
        ],
    ]);

    // Act & Assert: Test both slugs
    $this->getJson('/api/v1/event/draft-event-hu')->assertStatus(404);
    $this->getJson('/api/v1/event/draft-event-de')->assertStatus(404);
});

it('returns 404 for non-existent slug', function (): void {
    // Act & Assert: Test a non-existent slug
    $this->getJson('/api/v1/event/non-existent-slug')->assertStatus(404);
});

it('returns empty list when no published events exist', function (): void {
    // Arrange: Ensure no published events
    Event::factory()->create([
        'status' => EventStatus::DRAFT->value,
    ]);

    // Act & Assert
    $this->getJson('/api/v1/events')
        ->assertStatus(200)
        ->assertJson(fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson => $json->has('data', 0)
            ->where('meta.count', 0)
            ->where('meta.locale', App::getLocale())
        );
});
