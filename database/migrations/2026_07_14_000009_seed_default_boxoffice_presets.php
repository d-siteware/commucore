<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data-Migration: Legt die Default-BoxofficePresets für SKR42 an.
     * Steuert die Vorauswahl des Buchungskontos im Abendkassen-Formular.
     *
     * Reihenfolge (priority):
     *   1 → 51500 (Eintrittsgelder kulturelle Veranstaltungen)
     *   2 → 51000 (Eintrittsgelder aus sportlichen Veranstaltungen)
     *   3 → 51900 (Sonstige Einnahmen Zweckbetriebe)
     *
     * down()-Methode ist absichtlich leer – ein Zurücksetzen auf 0 Presets
     * würde die Abendkassen-Vorauswahl ersatzlos wegfallen lassen.
     */
    public function up(): void
    {
        $skr42Id = DB::table('booking_account_types')
            ->where('slug', 'skr42')
            ->value('id');

        if ($skr42Id === null) {
            return;
        }

        $defaults = [
            ['number' => '51500', 'priority' => 1],
            ['number' => '51000', 'priority' => 2],
            ['number' => '51900', 'priority' => 3],
        ];

        foreach ($defaults as $preset) {
            $accountId = DB::table('booking_accounts')
                ->where('booking_account_type_id', $skr42Id)
                ->where('number', $preset['number'])
                ->value('id');

            if ($accountId === null) {
                continue;
            }

            DB::table('boxoffice_presets')->updateOrInsert(
                [
                    'booking_account_type_id' => $skr42Id,
                    'booking_account_id' => $accountId,
                ],
                [
                    'booking_account_type_id' => $skr42Id,
                    'booking_account_id' => $accountId,
                    'priority' => $preset['priority'],
                ],
            );
        }
    }

    public function down(): void
    {
        // no-op – siehe Klassen-Docblock
    }
};
