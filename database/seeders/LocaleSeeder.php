<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (! Locale::where('name', 'de')->exists()) {
            Locale::create([
                'name' => 'de',
                'label' => 'deutsch',
                'active' => true,
                'decimal_separator' => ',',
                'thousands_separator' => '.',
                'date_format' => 'DD.MM.JJJJ',
                'name_order' => 'last_first',
                'currency_symbol' => 'EUR',
                'currency_position' => 'after',
            ]);
        }
        if (! Locale::where('name', 'hu')->exists()) {
            Locale::create([
                'name' => 'hu',
                'label' => 'magyar',
                'active' => true,
                'decimal_separator' => ',',
                'thousands_separator' => '.',
                'date_format' => 'JJJJ.MM.DD.',
                'name_order' => 'last_first',
                'currency_symbol' => 'HUF',
                'currency_position' => 'after',
            ]);
        }
        if (! Locale::where('name', 'en')->exists()) {
            Locale::create([
                'name' => 'en',
                'label' => 'english',
                'active' => false,
                'decimal_separator' => '.',
                'thousands_separator' => ',',
                'date_format' => 'MM/DD/JJJJ',
                'name_order' => 'first_last',
                'currency_symbol' => 'USD',
                'currency_position' => 'before',
            ]);
        }
    }
}
