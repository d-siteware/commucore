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

        $standardIdentity = DemoIdentity::named('Standard', 'User', 'standard.user@commu-core.app');

        $user = User::factory()->create([
            'name' => $standardIdentity['first_name'].' '.$standardIdentity['last_name'],
            'first_name' => $standardIdentity['first_name'],
            'email' => $standardIdentity['email'],
            'username' => 'demoUser',
            'gender' => Gender::ma,
            'is_admin' => false,
            'password' => Hash::make('commuCore-1234'),
            'locale' => 'de',
        ]);

        Member::factory()->create([
            'name' => $standardIdentity['last_name'],
            'first_name' => $standardIdentity['first_name'],
            'entered_at' => date('Y-m-d H:i:s'),
            'is_deducted' => false,
            'birth_date' => now()->subYears(20)->format('Y-m-d'),
            'email' => $standardIdentity['email'],
            'city' => 'Berlin',
            'country' => 'Deutschland',
            'user_id' => $user->id,
            'type' => MemberType::ST->value,
            'fee_type' => MemberFeeType::FULL->value,
            'locale' => 'de',
        ]);

        // ---- Admin User

        $adminIdentity = DemoIdentity::named('Admin', 'User', 'admin.user@commu-core.app');

        $admin = User::factory()->create([
            'name' => $adminIdentity['first_name'].' '.$adminIdentity['last_name'],
            'email' => $adminIdentity['email'],
            'username' => 'adminUser',
            'first_name' => $adminIdentity['first_name'],
            'gender' => Gender::ma,
            'is_admin' => true,
            'password' => Hash::make('commuCoreAdmin-1234'),
            'locale' => 'de',
        ]);

        Member::factory()->create([
            'entered_at' => date('Y-m-d H:i:s'),
            'is_deducted' => false,
            'birth_date' => now()->subYears(25)->format('Y-m-d'),
            'name' => $adminIdentity['last_name'],
            'first_name' => $adminIdentity['first_name'],
            'email' => $adminIdentity['email'],
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
