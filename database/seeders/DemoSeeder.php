<?php

namespace Database\Seeders;

use Database\Seeders\Demo\AccountReportSeeder;  // ← neu
use Database\Seeders\Demo\BlogPostSeeder;
use Database\Seeders\Demo\CashCountSeeder;
use Database\Seeders\Demo\DemoAccountSeeder;
use Database\Seeders\Demo\DemoUserSeeder;
use Database\Seeders\Demo\FundingSeeder;
use Database\Seeders\Demo\MailinglistSeeder;
use Database\Seeders\Demo\MeetingMinuteSeeder;
use Database\Seeders\Demo\MemberSeeder;
use Database\Seeders\Demo\OrganizationSeeder;
use Database\Seeders\Demo\ProjectSeeder;
use Database\Seeders\Demo\RoleSeeder;
use Database\Seeders\Demo\TransactionSeeder;
use Database\Seeders\Demo\VenueSeeder;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(crc32(config('app.key')));

        $this->call([
            DemoUserSeeder::class,
            OrganizationSeeder::class,
            DemoAccountSeeder::class,
            VenueSeeder::class,
            MemberSeeder::class,
            RoleSeeder::class,
            TransactionSeeder::class,
            AccountReportSeeder::class,
            CashCountSeeder::class,
            ProjectSeeder::class,
            FundingSeeder::class,
            MailinglistSeeder::class,
            BlogPostSeeder::class,
            MeetingMinuteSeeder::class,
        ]);
    }
}
