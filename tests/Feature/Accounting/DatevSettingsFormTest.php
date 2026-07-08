<?php

declare(strict_types=1);

use App\Livewire\App\Branding\Page;
use App\Models\User;
use App\Services\Accounting\DatevSettingsService;
use Livewire\Livewire;

describe('DATEV settings form (Branding page)', function (): void {

    it('loads defaults with empty placeholder numbers', function (): void {
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->assertSet('datevForm.berater_nr', '')
            ->assertSet('datevForm.mandant_nr', '')
            ->assertSet('datevForm.konto_laenge', 5)
            ->assertSet('datevForm.skr', '42');
    });

    it('saves valid DATEV settings and marks the export as configured', function (): void {
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->set('datevForm.berater_nr', '12345')
            ->set('datevForm.mandant_nr', '67890')
            ->set('datevForm.skr', '42')
            ->set('datevForm.application_info', 'CommuCore')
            ->call('saveDatev')
            ->assertHasNoErrors();

        $settings = app(DatevSettingsService::class);

        expect($settings->beraterNr())->toBe('12345')
            ->and($settings->mandantNr())->toBe('67890')
            ->and($settings->kontoLaenge())->toBe(5)
            ->and($settings->skr())->toBe('42')
            ->and($settings->isConfigured())->toBeTrue();
    });

    it('rejects charts of accounts other than SKR42', function (): void {
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->set('datevForm.berater_nr', '12345')
            ->set('datevForm.mandant_nr', '67890')
            ->set('datevForm.skr', '49')
            ->call('saveDatev')
            ->assertHasErrors(['datevForm.skr']);
    });

    it('derives the account number length from the selected SKR', function (): void {
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->set('datevForm.konto_laenge', 8) // manueller Eingriff wird beim Speichern überschrieben
            ->set('datevForm.berater_nr', '12345')
            ->set('datevForm.mandant_nr', '67890')
            ->call('saveDatev')
            ->assertHasNoErrors()
            ->assertSet('datevForm.konto_laenge', 5);

        expect(app(DatevSettingsService::class)->kontoLaenge())->toBe(5);
    });

    it('rejects invalid Beraternummer and Mandantennummer', function (): void {
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->set('datevForm.berater_nr', '999')      // < 1001
            ->set('datevForm.mandant_nr', '123456')   // > 99999
            ->call('saveDatev')
            ->assertHasErrors(['datevForm.berater_nr', 'datevForm.mandant_nr']);
    });

    it('requires Beraternummer and Mandantennummer', function (): void {
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->set('datevForm.berater_nr', '')
            ->set('datevForm.mandant_nr', '')
            ->call('saveDatev')
            ->assertHasErrors(['datevForm.berater_nr', 'datevForm.mandant_nr']);
    });

    it('loads stored numbers into the form once configured', function (): void {
        $settings = app(DatevSettingsService::class);
        $settings->setBeraterNr('4711');
        $settings->setMandantNr('42');

        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->assertSet('datevForm.berater_nr', '4711')
            ->assertSet('datevForm.mandant_nr', '42');
    });

});
