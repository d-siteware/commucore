<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\MemberFeeType;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            LocaleSeeder::class,
            DefaultBookingAccountTypeSeeder::class,
            SKR42BookingAccountSeeder::class,
            DatevSettingsSeeder::class,
        ]);

        if (! app()->environment(['production', 'demo'])) {

            User::factory()->create([
                'name' => 'Körtrvélyessy',
                'email' => 'daniel@thermo-control.com',
                'username' => 'Daniel',
                'first_name' => 'Daniel',
                'gender' => Gender::ma,
                'is_admin' => true,
                'password' => Hash::make('33 hkB47!!'),
                'locale' => 'de',
            ]);

            Member::factory()->create([
                'entered_at' => date('Y-m-d H:i:s'),
                'is_deducted' => false,
                'birth_date' => '1974-01-07',
                'name' => 'Körtvélyessy',
                'first_name' => 'Daniel',
                'email' => 'daniel@thermo-control.com',
                'phone' => '+493040586940',
                'mobile' => '+491735779408',
                'address' => 'Grünspechtweg 19',
                'city' => 'Berlin',
                'user_id' => 1,
                'type' => MemberType::MD->value,
                'fee_type' => MemberFeeType::FULL->value,
                'locale' => 'de',
            ]);
        }
    }
}
