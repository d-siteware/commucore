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

        Locale::create([
            'name' => 'de',
            'label' => 'deutsch',
            'active' => true,
        ]);

        Locale::create([
            'name' => 'hu',
            'label' => 'magyar',
            'active' => true,
        ]);

        Locale::create([
            'name' => 'en',
            'label' => 'english',
            'active' => false,
        ]);

    }
}
