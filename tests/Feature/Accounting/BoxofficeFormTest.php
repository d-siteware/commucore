<?php

declare(strict_types=1);

use App\Livewire\Accounting\Transaction\Boxoffice\Form;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\BookingAccountType;
use App\Models\Accounting\BoxofficePreset;
use App\Models\Accounting\FiscalYear;
use App\Models\Event\Event;
use App\Models\User;
use Livewire\Livewire;

describe('Boxoffice form default booking account', function (): void {

    beforeEach(function (): void {
        $type = BookingAccountType::firstOrCreate(
            ['slug' => 'skr42'],
            ['name' => 'SKR42', 'datev_skr_code' => '42', 'account_length' => 5],
        );

        $year = now()->year;
        FiscalYear::firstOrCreate(
            ['year' => $year],
            [
                'opened_at' => now(),
                'booking_account_type_id' => $type->id,
            ],
        );

        $this->skr42Type = $type;
    });

    it('preselects the first box office preset by priority', function (): void {
        $expected = BookingAccount::factory()->create(['number' => '51500', 'booking_account_type_id' => $this->skr42Type->id]);
        BookingAccount::factory()->create(['number' => '01100', 'booking_account_type_id' => $this->skr42Type->id]);

        BoxofficePreset::factory()->create([
            'booking_account_type_id' => $this->skr42Type->id,
            'booking_account_id' => $expected->id,
            'priority' => 1,
        ]);

        $event = Event::factory()->create();
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Form::class, ['event' => $event])
            ->assertSet('form.booking_account_id', $expected->id);
    });

    it('selects the higher priority preset', function (): void {
        $low = BookingAccount::factory()->create(['number' => '51900', 'booking_account_type_id' => $this->skr42Type->id]);
        $high = BookingAccount::factory()->create(['number' => '51500', 'booking_account_type_id' => $this->skr42Type->id]);

        BoxofficePreset::factory()->create([
            'booking_account_type_id' => $this->skr42Type->id,
            'booking_account_id' => $low->id,
            'priority' => 2,
        ]);
        BoxofficePreset::factory()->create([
            'booking_account_type_id' => $this->skr42Type->id,
            'booking_account_id' => $high->id,
            'priority' => 1,
        ]);

        $event = Event::factory()->create();
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Form::class, ['event' => $event])
            ->assertSet('form.booking_account_id', $high->id);
    });

    it('leaves the booking account empty when no preset exists', function (): void {
        $event = Event::factory()->create();
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Form::class, ['event' => $event])
            ->assertSet('form.booking_account_id', null);
    });

});
