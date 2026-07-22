<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Funding\FundingPositionCategory;
use Illuminate\Database\Seeder;

/**
 * Legt die provisorischen System-Kategorien für Förderpositionen an
 * (Fresh-Install-Pfad; Bestandsinstanzen erhalten sie per Data-Migration
 * 2026_07_22_000004 – der Seeder ist dann ein No-op).
 *
 * Idempotent: firstOrCreate auf slug.
 */
final class FundingPositionCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (FundingPositionCategory::systemDefaults() as $default) {
            FundingPositionCategory::firstOrCreate(
                ['slug' => $default['slug']],
                [
                    'name' => $default['name'],
                    'is_system' => true,
                    'source' => 'system',
                    'sort' => $default['sort'],
                ],
            );
        }
    }
}
