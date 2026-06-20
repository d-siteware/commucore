<?php

declare(strict_types=1);

use App\Livewire\App\Tool\Venue\Index;
use App\Models\User;
use App\Models\Venue;

beforeEach(function (): void {
    $this->admin = User::factory()->create(['is_admin' => true]);
});

it('renders the venue index page for authorized users', function (): void {
    $this->actingAs($this->admin)
        ->get(route('backend.venues.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('denies access for unauthorized users', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('backend.venues.index'))
        ->assertForbidden();
});

it('lists venues with pagination', function (): void {
    Venue::factory()->count(20)->create();

    $this->actingAs($this->admin)
        ->get(route('backend.venues.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('dispatches open-venue-create event when create button is clicked', function (): void {
    $this->actingAs($this->admin);

    Livewire::test(Index::class)
        ->call('create')
        ->assertDispatched('open-venue-create');
});

it('dispatches open-venue-edit event when edit button is clicked', function (): void {
    $venue = Venue::factory()->create();
    $this->actingAs($this->admin);

    Livewire::test(Index::class)
        ->call('edit', $venue->id)
        ->assertDispatched('open-venue-edit');
});

it('sets pending delete id and opens modal', function (): void {
    $venue = Venue::factory()->create();
    $this->actingAs($this->admin);

    $component = Livewire::test(Index::class);

    $component->call('confirmDelete', $venue->id);

    expect($component->get('pendingDeleteId'))->toBe($venue->id);
});

it('deletes a venue', function (): void {
    $venue = Venue::factory()->create();
    $this->actingAs($this->admin);

    Livewire::test(Index::class)
        ->set('pendingDeleteId', $venue->id)
        ->call('delete')
        ->assertSet('pendingDeleteId', null);

    expect(Venue::find($venue->id))->toBeNull();
});

it('shows events count for venues with events', function (): void {
    $venue = Venue::factory()->create();
    \App\Models\Event\Event::factory()->create(['venue_id' => $venue->id]);
    $this->actingAs($this->admin);

    $component = Livewire::test(Index::class);

    $found = $component->get('venues')->firstWhere('id', $venue->id);
    expect($found->events_count)->toBe(1);
});
it('shows correct events count in delete confirmation', function (): void {
    $venue = Venue::factory()->create();
    \App\Models\Event\Event::factory()->count(3)->create(['venue_id' => $venue->id]);
    $this->actingAs($this->admin);

    $component = Livewire::test(Index::class)
        ->call('confirmDelete', $venue->id);

    expect($component->get('pendingDeleteEventsCount'))->toBe(3);
    expect($component->get('pendingDeleteVenueName'))->toBe($venue->name);
});