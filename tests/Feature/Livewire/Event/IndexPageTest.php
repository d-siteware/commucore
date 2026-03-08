<?php

declare(strict_types=1);

use App\Livewire\Activity\Event\Index\Page;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use App\Models\User;
use Tests\Traits\TranslationTestTrait;

uses(TranslationTestTrait::class);

test('if backend event index page component renders correctly', function (): void {

    // Nutzer erstellen aus Mitglied authentifizieren
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    // 30 Veranstaltungen erstellen

    $event = \App\Models\Event\Event::factory(30)->create();

    Livewire::test(Page::class)
        ->assertSeeLivewire(Page::class) // Ensures Livewire renders
        ->assertStatus(200)
//        ->assertSee('pagination') // Assuming pagination is visible
        ->assertSee(Event::first()->name); // Check if first event is listed
});

test('if backend event pagination works correctly', function (): void {
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);
    Event::factory(30)->create();

    Livewire::test(Page::class)
        ->call('gotoPage', 2)
        ->assertSee(Event::skip(10)->first()->name) // Check second page content
        ->assertDontSee(Event::first()->name); // First page event should not be here
});

test('if backend event index page events can be searched', function (): void {
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);
    Event::factory()->create(['name' => 'Laravel Conference']);
    Event::factory()->create(['name' => 'VueJS Meetup']);

    Livewire::test(Page::class)
        ->set('search', 'Laravel')
        ->assertSee('Laravel Conference')
        ->assertDontSee('VueJS Meetup');
});

test('if all translations are rendered on backend event index page', function (): void {
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    $event = \App\Models\Event\Event::factory()->create();

    $this->assertTranslationsRendered(
        Page::class, [],
        'event',
        'event.',
    );
});
