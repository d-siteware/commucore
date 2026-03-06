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
            ]);
        }
        if (! Locale::where('name', 'hu')->exists()) {
            Locale::create([
                'name' => 'hu',
                'label' => 'magyar',
                'active' => true,
            ]);
        }
        if (! Locale::where('name', 'en')->exists()) {
            Locale::create([
                'name' => 'en',
                'label' => 'english',
                'active' => false,
            ]);
        }
    }
}
