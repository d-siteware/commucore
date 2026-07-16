<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data-Migration: Legt den SKR42-BookingAccountType an (falls nicht vorhanden)
     * und setzt booking_account_type_id auf bestehenden booking_accounts und fiscal_years.
     *
     * down()-Methode ist absichtlich leer – ein Zurücksetzen der Type-ID auf null
     * würde bestehende Datenintegrität zerstören.
     */
    public function up(): void
    {
        $skr42Id = DB::table('booking_account_types')
            ->where('slug', 'skr42')
            ->value('id');

        if ($skr42Id === null) {
            $skr42Id = DB::table('booking_account_types')->insertGetId([
                'name' => 'SKR42',
                'slug' => 'skr42',
                'datev_skr_code' => '42',
                'account_length' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('booking_accounts')
            ->whereNull('booking_account_type_id')
            ->update(['booking_account_type_id' => $skr42Id]);

        DB::table('fiscal_years')
            ->whereNull('booking_account_type_id')
            ->update(['booking_account_type_id' => $skr42Id]);
    }

    public function down(): void
    {
        // no-op – siehe Klassen-Docblock
    }
};
