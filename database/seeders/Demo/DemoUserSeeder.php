<?php

namespace Database\Seeders\Demo;

use App\Enums\Gender;
use App\Enums\MemberFeeType;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {

        //   ---- Standard DemoUser

        $user = User::factory()->withPersonalTeam()->create([
            'email' => 'standard.user@commu-core.app',
            'username' => 'demoUser',
            'gender' => Gender::ma,
            'is_admin' => false,
            'password' => Hash::make('commuCore-1234'),
        ]);

        Member::factory()->create([
            'entered_at' => date('Y-m-d H:i:s'),
            'is_deducted' => false,
            'birth_date' => now()->subYears(20)->format('Y-m-d'),
            'email' => 'standard.user@commu-core.app',
            'city' => 'Berlin',
            'country' => 'Deutschland',
            'user_id' => $user->id,
            'type' => MemberType::ST->value,
            'fee_type' => MemberFeeType::FULL->value,
            'locale' => 'de',
        ]);

        // ---- Admin User

        $admin = User::factory()->withPersonalTeam()->create([
            'name' => 'Demo user',
            'email' => 'admin.user@commu-core.app',
            'username' => 'adminUser',
            'first_name' => 'Admino',
            'gender' => Gender::ma,
            'is_admin' => false,
            'password' => Hash::make('commuCoreAdmin-1234'),
        ]);

        Member::factory()->create([
            'entered_at' => date('Y-m-d H:i:s'),
            'is_deducted' => false,
            'birth_date' => now()->subYears(25)->format('Y-m-d'),
            'name' => 'Demo User',
            'first_name' => 'Admino',
            'email' => 'admin.user@commu-core.app',
            'phone' => '+49134567890',
            'mobile' => '+49171234568974',
            'address' => 'Grünspechtweg 19',
            'city' => 'Berlin',
            'user_id' => $admin->id,
            'type' => MemberType::MD->value,
            'fee_type' => MemberFeeType::FULL->value,
            'locale' => 'de',
        ]);

    }
}
