<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Legt DATEV-Standardwerte (Platzhalter) in der settings-Tabelle an.
 *
 * Echte Beraternummer und Mandantennummer müssen später über die
 * Admin-UI (Einstellungen → DATEV) eingetragen werden.
 */
class DatevSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'key' => 'berater_nr',
                'value' => '0000',        // TODO: echte Beraternummer eintragen
                'type' => 'string',
            ],
            [
                'key' => 'mandant_nr',
                'value' => '00000',       // TODO: echte Mandantennummer eintragen
                'type' => 'string',
            ],
            [
                'key' => 'fiscal_year_start',
                'value' => 1,             // Januar
                'type' => 'integer',
            ],
            [
                'key' => 'konto_laenge',
                'value' => 4,             // SKR49 = immer 4-stellig
                'type' => 'integer',
            ],
            [
                'key' => 'application_info',
                'value' => 'CommuCore',
                'type' => 'string',
            ],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                [
                    'group' => 'datev',
                    'key' => $setting['key'],
                ],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }
    }
}
