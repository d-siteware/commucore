<?php

declare(strict_types=1);

use App\Enums\EventStatus;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('Event-Seite öffnet sich für Admins ohne Member-Record', function (): void {
    $event = Event::factory()->create(['status' => EventStatus::PUBLISHED->value]);
    $admin = User::factory()->create(['is_admin' => true]); // kein Member-Record

    $this->actingAs($admin)
        ->get(route('backend.events.show', $event))
        ->assertOk();
});

test('Mitglied-Löschung hinterlässt member_transaction mit null statt Crash', function (): void {
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create();

    $linkId = DB::table('member_transactions')->insertGetId([
        'member_id' => $member->id,
        'transaction_id' => $transaction->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $member->delete();

    expect(DB::table('member_transactions')->find($linkId)->member_id)->toBeNull();
});

test('Registrierung mit ungültigem Token leitet mit Fehler um statt 404', function (): void {
    $response = $this->post(route('members.register'), [
        'token' => 'ungueltig',
        'password' => 'geheim12345',
        'password_confirmation' => 'geheim12345',
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHas('error');
});
