<?php

declare(strict_types=1);

use App\Enums\Locale;
use App\Livewire\Activity\Event\PosterGenerator\Create;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\Traits\TranslationTestTrait;

uses(TranslationTestTrait::class);

// -------------------------------------------------------------------------
// Helpers
// -------------------------------------------------------------------------

function makeAuthUser(): User
{
    $member = Member::factory()->withUser()->create([
        'user_id' => User::factory()->create(['email_verified_at' => now()])->id,
    ]);

    return $member->user;
}

// -------------------------------------------------------------------------
// Rendering
// -------------------------------------------------------------------------

test('poster generator renders for authenticated user', function (): void {
    $user = makeAuthUser();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->assertOk();
});

test('poster generator shows generate buttons', function (): void {
    $user = makeAuthUser();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->assertSee('generatePoster', false);
});

// -------------------------------------------------------------------------
// Default option values
// -------------------------------------------------------------------------

test('default options are set correctly on mount', function (): void {
    $user = makeAuthUser();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->assertSet('withImage', true)
        ->assertSet('textMode', 'excerpt');
});

// -------------------------------------------------------------------------
// PDF generation
// -------------------------------------------------------------------------

test('can generate pdf poster for all locales', function (): void {
    Storage::fake('public');

    $user = makeAuthUser();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->call('generatePosters');

    foreach (Locale::toArray() as $locale) {
        $path = 'images/posters/'.$event->getFilename($locale).'.pdf';
        Storage::disk('public')->assertExists($path);
    }
});

test('can generate pdf without cover image', function (): void {
    Storage::fake('public');

    $user = makeAuthUser();
    $event = Event::factory()->create(['image' => null]);

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->set('withImage', false)
        ->call('generatePosters');

    foreach (Locale::toArray() as $locale) {
        $path = 'images/posters/'.$event->getFilename($locale).'.pdf';
        Storage::disk('public')->assertExists($path);
    }
});

test('can generate pdf with full text mode', function (): void {
    Storage::fake('public');

    $user = makeAuthUser();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->set('textMode', 'full')
        ->call('generatePosters');

    foreach (Locale::toArray() as $locale) {
        $path = 'images/posters/'.$event->getFilename($locale).'.pdf';
        Storage::disk('public')->assertExists($path);
    }
});

// -------------------------------------------------------------------------
// Delete poster
// -------------------------------------------------------------------------

test('can delete an existing poster', function (): void {
    Storage::fake('public');

    $user = makeAuthUser();
    $event = Event::factory()->create();

    $locale = 'de';
    $path = 'images/posters/'.$event->getFilename($locale).'.pdf';
    Storage::disk('public')->put($path, 'dummy');
    Storage::disk('public')->assertExists($path);

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->call('deletePoster', $locale, 'pdf');

    Storage::disk('public')->assertMissing($path);
});

test('deleting a non-existent poster does not throw', function (): void {
    Storage::fake('public');

    $user = makeAuthUser();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->call('deletePoster', 'de', 'pdf')
        ->assertOk();
});

// -------------------------------------------------------------------------
// Option toggling
// -------------------------------------------------------------------------

test('withImage property can be toggled', function (): void {
    $user = makeAuthUser();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->set('withImage', false)
        ->assertSet('withImage', false)
        ->set('withImage', true)
        ->assertSet('withImage', true);
});

test('textMode can be set to full', function (): void {
    $user = makeAuthUser();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->set('textMode', 'full')
        ->assertSet('textMode', 'full');
});

test('previewLocale can be changed', function (): void {
    $user = makeAuthUser();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class, ['event' => $event])
        ->set('previewLocale', 'hu')
        ->assertSet('previewLocale', 'hu');
});

// -------------------------------------------------------------------------
// Translations
// -------------------------------------------------------------------------

test('all translations are present for poster generator', function (): void {
    $event = Event::factory()->create();

    $this->assertTranslationsRendered(
        Create::class,
        ['event' => $event],
        'event',
        'event.poster.',
    );
});
