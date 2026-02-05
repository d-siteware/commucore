<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\MailingList;
use Illuminate\Database\Seeder;

final class MailinglistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mailinglist::factory()->count(52)->create();
    }
}
