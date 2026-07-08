<?php

declare(strict_types=1);

use App\Livewire\Accounting\Transaction\Boxoffice\Form;
use App\Models\Accounting\BookingAccount;
use App\Models\Event\Event;
use App\Models\User;
use Livewire\Livewire;

describe('Boxoffice form default booking account', function (): void {

    it('preselects the SKR42 box office income account by number', function (): void {
        $expected = BookingAccount::factory()->create(['number' => '51500', 'label' => 'Eintrittsgelder kulturelle Veranstaltungen']);
        BookingAccount::factory()->create(['number' => '01100', 'label' => 'Konzessionen']);

        $event = Event::factory()->create();
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Form::class, ['event' => $event])
            ->assertSet('form.booking_account_id', $expected->id);
    });

    it('falls back to the next configured account number', function (): void {
        // 51500 fehlt – 51900 ist der nächste Kandidat
        $fallback = BookingAccount::factory()->create(['number' => '51900', 'label' => 'Sonstige Einnahmen Zweckbetriebe']);

        $event = Event::factory()->create();
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Form::class, ['event' => $event])
            ->assertSet('form.booking_account_id', $fallback->id);
    });

    it('leaves the booking account empty when no candidate exists', function (): void {
        BookingAccount::factory()->create(['number' => '01100', 'label' => 'Konzessionen']);

        $event = Event::factory()->create();
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Form::class, ['event' => $event])
            ->assertSet('form.booking_account_id', null);
    });

});
