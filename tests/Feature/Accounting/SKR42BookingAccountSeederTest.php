<?php

declare(strict_types=1);

use App\Models\Accounting\BoxofficePreset;
use Database\Seeders\SKR42BookingAccountSeeder;

/**
 * Regression: Der SKR42BookingAccountSeeder muss nach der Konto-Anlage auch
 * die Default-BoxofficePresets anlegen. Fehlt das, haben frische Instanzen
 * keine Abendkassen-Vorauswahl. Siehe Migration 000009 (Bestands-Pfad).
 */
describe('SKR42BookingAccountSeeder boxoffice presets', function (): void {

    it('creates boxoffice presets after seeding accounts', function (): void {
        $this->seed(SKR42BookingAccountSeeder::class);

        $presets = BoxofficePreset::query()
            ->whereHas('bookingAccount')
            ->orderBy('priority')
            ->get();

        expect($presets)->toHaveCount(3);

        $numbers = $presets->map(fn ($p) => $p->bookingAccount->number)->values()->toArray();
        expect($numbers)->toBe(['51500', '51000', '51900']);

        $priorities = $presets->pluck('priority')->sort()->values()->toArray();
        expect($priorities)->toBe([1, 2, 3]);
    });

    it('is idempotent when run twice', function (): void {
        $this->seed(SKR42BookingAccountSeeder::class);
        $this->seed(SKR42BookingAccountSeeder::class);

        $count = BoxofficePreset::count();
        expect($count)->toBe(3);
    });

});
