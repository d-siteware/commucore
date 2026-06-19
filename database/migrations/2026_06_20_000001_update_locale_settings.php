<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the locales table has all required columns
        if (! Schema::hasColumn('locales', 'currency_symbol')) {
            Schema::table('locales', function (Blueprint $table) {
                $table->string('currency_symbol')->nullable()->after('name_order');
                $table->string('currency_position')->nullable()->default('after')->after('currency_symbol');
                $table->string('date_format')->nullable()->default('DD.MM.JJJJ')->after('thousands_separator');
            });
        }

        // Update de locale: name_order should be last_first (German convention)
        DB::table('locales')
            ->where('name', 'de')
            ->update([
                'name_order' => 'last_first',
                'currency_symbol' => 'EUR',
                'currency_position' => 'after',
                'date_format' => 'DD.MM.JJJJ',
            ]);

        // Update hu locale if exists
        DB::table('locales')
            ->where('name', 'hu')
            ->update([
                'name_order' => 'last_first',
                'currency_symbol' => 'HUF',
                'currency_position' => 'after',
                'date_format' => 'JJJJ.MM.DD.',
            ]);

        // Update en locale if exists
        DB::table('locales')
            ->where('name', 'en')
            ->update([
                'name_order' => 'first_last',
                'currency_symbol' => 'USD',
                'currency_position' => 'before',
                'date_format' => 'MM/DD/JJJJ',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert de locale
        DB::table('locales')
            ->where('name', 'de')
            ->update(['name_order' => 'first_last']);
    }
};
