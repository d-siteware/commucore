<?php

declare(strict_types=1);

use App\Http\Resources\Api\V1\EventDetailResource;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use App\Models\User;

test('event location ist leer statt Crash ohne Venue', function (): void {
    $event = Event::factory()->create(['venue_id' => null]);

    expect($event->location())->toBe('');
});

test('öffentliche Event-Übersicht rendert mit Event ohne Venue', function (): void {
    Event::factory()->create(['venue_id' => null]);

    $this->get('/events')->assertOk();
});

test('ICS-Export funktioniert ohne Venue', function (): void {
    $event = Event::factory()->create(['venue_id' => null]);

    $this->get('/events/ics/'.$event->slug['de'])->assertOk();
});

test('API Event-Detail liefert venue null ohne Crash', function (): void {
    $event = Event::factory()->create(['venue_id' => null]);
    $event->load(['venue', 'timelines']);

    $data = (new EventDetailResource($event))->resolve();

    expect($data['venue'])->toBeNull();
});

test('Event-Notification-Mail rendert mit gelöschtem Mitglied und null-Feldern', function (): void {
    $user = User::factory()->create();
    $member = Member::factory()->create(['user_id' => $user->id]);
    $event = Event::factory()->create([
        'venue_id' => null,
        'start_time' => null,
        'end_time' => null,
    ]);
    $memberId = $member->id;
    $member->delete(); // Mitglied zwischen Listenaufbau und Versand gelöscht

    $html = view('emails.new_event_notification', [
        'notifiable' => $event,
        'recipient' => ['type' => 'member', 'id' => $memberId, 'email' => $user->email, 'locale' => 'de'],
        'notificationType' => 'events',
    ])->render();

    expect($html)->toContain('—');
});

test('Poster-PDF-View rendert ohne Venue', function (): void {
    $event = Event::factory()->create(['venue_id' => null]);

    $html = view('event_posters.main_pdf', [
        'event' => $event,
        'locale' => 'de',
        'qrcode' => '',
    ])->render();

    expect($html)->toBeString();
});
