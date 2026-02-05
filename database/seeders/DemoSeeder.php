<?php

namespace Database\Seeders;

use Database\Seeders\Demo\MailinglistSeeder;
use Database\Seeders\Demo\MemberSeeder;
use Database\Seeders\Demo\TransactionSeeder;
use Database\Seeders\Demo\VenueSeeder;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(crc32(config('app.key')));

        $this->call([
            VenueSeeder::class,
            MemberSeeder::class,
            TransactionSeeder::class,
            MailinglistSeeder::class,
        ]);
    }
}
