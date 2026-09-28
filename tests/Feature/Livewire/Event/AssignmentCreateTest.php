<?php

declare(strict_types=1);

use App\Enums\AssignmentStatus;
use App\Livewire\Activity\Event\Create\Page as CreatePage;
use App\Livewire\Activity\Event\Show\Page as ShowPage;
use App\Models\Event\Event;
use App\Models\Event\EventAssignment;
use App\Models\Membership\Member;
use App\Models\User;
use Livewire\Livewire;

/*
 * Regression: $this->reset('assignmentForm') in startNewAssigment() leerte
 * auch die in mount() gesetzten Vorgaben. Das Modal öffnete sich mit leerem
 * Status-Feld, storeAssignment() scheiterte dann an 'status required'.
 */
test('neue aufgabe öffnet das modal mit den vorgabewerten', function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $event = Event::factory()->create();

    Livewire::test(ShowPage::class, ['event' => $event])
        ->call('startNewAssigment')
        ->assertSet('assignmentForm.status', AssignmentStatus::draft->value)
        ->assertSet('assignmentForm.due_at', now()->format('Y-m-d'))
        ->assertSet('assignmentForm.member_id', Member::first()->id);
});

test('aufgabe lässt sich anlegen ohne den status erneut zu wählen', function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    $member = Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $event = Event::factory()->create();

    Livewire::test(ShowPage::class, ['event' => $event])
        ->call('startNewAssigment')
        // Der Nutzer tippt nur die Aufgabe – der Status bleibt auf der Vorgabe.
        ->set('assignmentForm.task', 'Tische aufstellen')
        ->call('storeAssignment')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('event_assignments', [
        'event_id' => $event->id,
        'task' => 'Tische aufstellen',
        'status' => AssignmentStatus::draft->value,
        'member_id' => $member->id,
    ]);
});

/*
 * Regression: Schritt 2 verlangte Titel + Slug für JEDE aktive Sprache.
 * Die UI zeigt aber nur einen Sprach-Tab – eine zweite aktivierte Sprache
 * blockierte das Anlegen, obwohl ihr Feld nie sichtbar ausgefüllt werden konnte.
 */
test('create-seite verlangt nur die fallback-sprache', function (): void {
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    Livewire::test(CreatePage::class)
        ->set('form.name', 'Sommerfest')
        ->set('form.event_date', now()->addMonth()->format('Y-m-d'))
        ->set('form.start_time', '14:00')
        ->set('form.end_time', '20:00')
        ->call('nextStep')
        ->assertHasNoErrors()
        ->set('form.title.de', 'Sommerfest am See')
        ->set('form.slug.de', 'sommerfest-am-see')
        ->call('validateStep')
        ->assertHasNoErrors();
});

test('create-seite verlangt beide felder wenn eine weitere sprache angefangen wurde', function (): void {
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    Livewire::test(CreatePage::class)
        ->set('form.name', 'Sommerfest')
        ->set('form.event_date', now()->addMonth()->format('Y-m-d'))
        ->set('form.start_time', '14:00')
        ->set('form.end_time', '20:00')
        ->call('nextStep')
        ->set('form.title.de', 'Sommerfest am See')
        ->set('form.slug.de', 'sommerfest-am-see')
        // Ungarischer Titel getippt, Slug vergessen -> muss auffallen.
        ->set('form.title.hu', 'Nyári rendezvény a tónál')
        ->call('validateStep')
        ->assertHasErrors(['form.slug.hu']);
});

test('aufgabenliste zeigt die angelegte aufgabe', function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $event = Event::factory()->create();
    EventAssignment::factory()->create([
        'event_id' => $event->id,
        'task' => 'Bargeld auslegen',
    ]);

    Livewire::test(ShowPage::class, ['event' => $event])
        ->assertSee('Bargeld auslegen');
});
