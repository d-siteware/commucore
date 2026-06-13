<?php

declare(strict_types=1);

use App\Livewire\Activity\Event\Create\Page;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use App\Models\User;
use App\Models\Venue;
use Livewire\Livewire;

beforeEach(function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
});

test('the create event page renders', function (): void {
    Livewire::test(Page::class)
        ->assertStatus(200);
});

test('a draft event can be created with minimal data', function (): void {
    $venue = Venue::factory()->create();

    Livewire::test(Page::class)
        ->set('form.name', 'Test Event')
        ->set('form.event_date', now()->addMonth()->format('Y-m-d'))
        ->set('form.start_time', '14:00')
        ->set('form.end_time', '18:00')
        ->set('form.venue_id', $venue->id)
        ->set('form.title', ['de' => 'Test Titel', 'hu' => 'Teszt cím'])
        ->set('form.slug', ['de' => 'test-titel', 'hu' => 'teszt-cim'])
        ->set('form.status', 'draft')
        ->call('createEventData')
        ->assertHasNoErrors();

    $event = Event::where('name', 'Test Event')->first();
    expect($event)->not->toBeNull()
        ->and($event->status->value)->toBe('draft')
        ->and($event->venue_id)->toBe($venue->id);
});

test('event dates are parsed as Carbon instances after creation', function (): void {
    Livewire::test(Page::class)
        ->set('form.name', 'Dated Event')
        ->set('form.event_date', now()->addMonth()->format('Y-m-d'))
        ->set('form.start_time', '10:00')
        ->set('form.end_time', '12:00')
        ->set('form.title', ['de' => 'Dated', 'hu' => 'Dátumos'])
        ->set('form.slug', ['de' => 'dated', 'hu' => 'datumos'])
        ->set('form.status', 'draft')
        ->call('createEventData')
        ->assertHasNoErrors();

    $event = Event::where('name', 'Dated Event')->first();
    expect($event->event_date)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($event->start_time->format('H:i'))->toBe('10:00')
        ->and($event->end_time->format('H:i'))->toBe('12:00');
});

test('event creation requires a name', function (): void {
    Livewire::test(Page::class)
        ->set('form.event_date', now()->addMonth()->format('Y-m-d'))
        ->set('form.start_time', '14:00')
        ->set('form.end_time', '18:00')
        ->set('form.title', ['de' => 'Titel', 'hu' => 'Cím'])
        ->set('form.slug', ['de' => 'titel', 'hu' => 'cim'])
        ->call('createEventData')
        ->assertHasErrors(['form.name']);
});

test('event creation requires title for each active locale', function (): void {
    Livewire::test(Page::class)
        ->set('form.name', 'No Title Event')
        ->set('form.event_date', now()->addMonth()->format('Y-m-d'))
        ->set('form.start_time', '14:00')
        ->set('form.end_time', '18:00')
        ->set('form.title', ['de' => '', 'hu' => ''])
        ->set('form.slug', ['de' => 'no-title', 'hu' => 'nincs-cim'])
        ->call('createEventData')
        ->assertHasErrors(['form.title.de', 'form.title.hu']);
});
