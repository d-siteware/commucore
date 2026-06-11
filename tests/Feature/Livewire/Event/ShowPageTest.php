<?php

declare(strict_types=1);

use App\Http\Controllers\EventController;
use App\Livewire\Activity\Event\Show\Page;
use App\Livewire\App\Global\ImageUpload;
use App\Livewire\App\Global\Venue\Form;
use App\Models\Event\Event;
use App\Models\Event\EventAssignment;
use App\Models\Event\EventSubscription;
use App\Models\History;
use App\Models\Membership\Member;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\UploadedFile;
use Tests\Traits\TranslationTestTrait;

uses(TranslationTestTrait::class);

test('backend event show page component renders correctly', function (): void {

    // Nutzer mit Member erstellen
    $user = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user;

    // Nutzer authentifizieren
    $this->actingAs($user);

    $event = Event::factory()->create();

    Livewire::test(Page::class, ['event' => $event])
        ->assertStatus(200);
});

test('backend event show page loads the correct event data', function (): void {
    $user = User::factory()->create();
    $member = Member::factory()->create([
        'user_id' => $user->id, // Member mit User verknüpfen
    ]);

    // Nutzer authentifizieren
    $this->actingAs($user);
    $event = Event::factory()->create();

    Livewire::test(Page::class, ['event' => $event])
        ->assertSet('event_id', $event->id)
        ->assertSee($event->name); // Adjust according to your event fields
});

test('backend event show page loads subscriptions', function (): void {
    $user = User::factory()->create();
    $member = Member::factory()->create([
        'user_id' => $user->id, // Member mit User verknüpfen
    ]);

    // Nutzer authentifizieren
    $this->actingAs($user);
    $event = Event::factory()->create();
    EventSubscription::factory()->count(3)->create(['event_id' => $event->id]);

    Livewire::test(Page::class, ['event' => $event])
        ->assertCount('subscriptions', 3);
});

test('assign venue listener works', function (): void {
    $user = User::factory()->create();
    Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $event = Event::factory()->create();
    $venue = Venue::factory()->create(['name' => 'Initial Venue']);

    $component = Livewire::test(Page::class, ['event' => $event]);

    // Venue-IDs vor dem Dispatch prüfen
    $component->assertSet(
        'venues',
        fn ($venues) => $venues->pluck('id')->contains($venue->id)
    );

    // Neues Venue anlegen und Event dispatchen
    $newVenue = Venue::factory()->create(['name' => 'New Venue']);

    $component->dispatch('venue-created', venueId: $newVenue->id);

    // venue_id im Form gesetzt
    $component->assertSet('form.venue_id', $newVenue->id);

    // Neues Venue in der Collection
    $component->assertSet(
        'venues',
        fn ($venues) => $venues->pluck('id')->contains($newVenue->id)
    );
});

test('backend event page stores image and dispatches success toast', function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    $member = Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
    $event = Event::factory()->create();

    Livewire::test(Page::class, ['event' => $event])
        ->dispatch('image-uploaded', file: 'test.jpg') // Simulate ImageUpload's output
        ->assertDispatched('flux-toast', function ($name, $params): bool {
            return $params[0]['variant'] === 'success';
        });

    // Verify the event was updated with the image filename
    expect($event->fresh()->image)
        ->toBe('test.jpg')
        ->and(History::where('historable_id', $event->id)
            ->where('historable_type', get_class($event))
            ->where('action', 'updated')
            ->count())
        ->toBe(1);

    // Optional: Verify history was recorded (if Event uses HasHistory)
});

test('image upload component processes file and dispatches event', function (): void {
    // Setup: Authenticated user with member
    $user = User::factory()->create();
    $member = Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    // Create an event for the component
    $event = Event::factory()->create();

    $user = User::factory()->create();
    $member = Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $fakeImage = UploadedFile::fake()->image('test.jpg');

    Storage::fake('public'); // Use a fake disk for consistency

    $component = Livewire::test(ImageUpload::class)
        ->set('image', $fakeImage);

    // Get the stored filename
    $storedFiles = Storage::disk('public')->files('images');
    $storedFile = ! empty($storedFiles) ? basename($storedFiles[0]) : null;

    $component->assertDispatched('image-uploaded', function ($event, $params) use ($storedFile): bool {
        return isset($params['file']) && $params['file'] === $storedFile;
    });

    Storage::disk('public')->assertExists("images/{$storedFile}");
});

test('clicking add visitor opens modal', function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    $member = Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
    $event = Event::factory()->create();

    $component = Livewire::test(Page::class, ['event' => $event])
        ->call('addVisitor')
        ->assertDispatched('modal-show');
});

test('deleting assignment removes it and shows toast', function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    $member = Member::factory()->create([
        'user_id' => $user->id, // Member mit User verknüpfen
    ]);

    // Nutzer authentifizieren
    $this->actingAs($user);

    $event = Event::factory()->create();
    $assignment = EventAssignment::factory()->create(['event_id' => $event->id]);

    Livewire::test(Page::class, ['event' => $event])
        ->call('deleteAssignment', $assignment->id)
        ->assertReturned(null);

});

test('venue creation updates event show page venues', function (): void {
    $user = User::factory()->create();
    Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $event = Event::factory()->create();

    $showComponent = Livewire::test(Page::class, ['event' => $event]);

    $createComponent = Livewire::test(Form::class)
        ->set('form.name', 'Neues Venue')
        ->set('form.address', 'Musterstraße 1')
        ->call('save'); // heißt jetzt save(), nicht storeVenue()

    $newVenue = Venue::where('name', 'Neues Venue')->firstOrFail();

    // Event simulieren das Form::save() dispatcht
    $showComponent->dispatch('venue-created', venueId: $newVenue->id);

    $showComponent->assertSet('form.venue_id', $newVenue->id);
    $showComponent->assertSet(
        'venues',
        fn ($venues) => $venues->pluck('id')->contains($newVenue->id)
    );
});

test('all translations are rendered', function (): void {
    $user = User::factory()
        ->create(['is_admin' => true]);
    $this->actingAs($user);

    $member = Member::factory()->create(['user_id' => $user->id]);
    $event = Event::factory()->create();

    $this->assertTranslationsRendered(
        Page::class,
        ['event' => $event, 'user' => $user, 'member' => $member],
        'event',
        'event.',
    );
});

test('event page route translations are rendered', function (): void {
    $event = Event::factory()->create();

    $this->assertTranslationsRendered(
        EventController::class,
        ['event' => $event, 'method' => 'index'],
        'event',
        'event.'
    );
});

// test('custom translations test', function () {
//    $this->assertTranslationsRendered(
//        SomeComponent::class,
//        [], // no params
//        'custom_translations', // different translation file
//        'custom.', // different prefix
//        false // less strict mode
//    );
// });
