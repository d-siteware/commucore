<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\MemberType;
use App\Models\Membership\Member;
use Illuminate\Database\Seeder;

final class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment() !== 'production') {

            Member::factory(4)
                ->create([
                    'type' => MemberType::MD->value,
                ]);
            Member::factory(8)
                ->create([
                    'type' => MemberType::ST->value,
                ]);
            Member::factory(8)
                ->create([
                    'type' => MemberType::AP->value,
                    'applied_at' => now()->subDays(random_int(0, 4)),
                ]);

            //            Event::factory(10)
            //                ->create();
        }
    }
}
