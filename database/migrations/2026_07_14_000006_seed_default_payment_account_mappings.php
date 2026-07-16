<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data-Migration: Legt die Default-Geldkontomappings für SKR42 an.
     * Ersetzt den bisherigen DatevGeldkontoResolver (Hardcode).
     *
     * down()-Methode ist absichtlich leer – ein Zurücksetzen auf 0 Mappings
     * würde den DATEV-Export brechen.
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
            ['booking_account_type_id' => $skr42Id, 'account_type' => 'cash',   'booking_account_number' => '16000'],
            ['booking_account_type_id' => $skr42Id, 'account_type' => 'bank',   'booking_account_number' => '18000'],
            ['booking_account_type_id' => $skr42Id, 'account_type' => 'paypal', 'booking_account_number' => '18100'],
        ];

        foreach ($defaults as $mapping) {
            DB::table('payment_account_mappings')->updateOrInsert(
                ['booking_account_type_id' => $skr42Id, 'account_type' => $mapping['account_type']],
                $mapping,
            );
        }
    }

    public function down(): void
    {
        // no-op – siehe Klassen-Docblock
    }
};
