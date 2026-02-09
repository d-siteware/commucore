<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Accounting\CreateBookingAccount;
use App\Enums\AccountType;
use App\Enums\BookingAccountType;
use App\Enums\Gender;
use App\Enums\MemberFeeType;
use App\Enums\MemberType;
use App\Models\Accounting\Account;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(LocaleSeeder::class);

        User::factory()->withPersonalTeam()->create([
            'name' => 'Körtrvélyessy',
            'email' => 'daniel@thermo-control.com',
            'username' => 'Daniel',
            'first_name' => 'Daniel',
            'gender' => Gender::ma->value,
            'is_admin' => true,
            'password' => Hash::make('33 hkB47!!'),
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
        ]);

        CreateBookingAccount::create([
            'type' => BookingAccountType::Income->value,
            'number' => '5706',
            'label' => 'Einnahmen aus sonstigen Veranstaltungen',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Income->value,
            'number' => '8002',
            'label' => 'Eintrittsgelder aus geselligen Veranstaltungen',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Expenses->value,
            'number' => '2701',
            'label' => 'Büromaterial',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Expenses->value,
            'number' => '6841',
            'label' => 'Porto, Telefon',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Expenses->value,
            'number' => '6343',
            'label' => 'Bürobedarf',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Expenses->value,
            'number' => '3251',
            'label' => 'Gezahlte Spenden/Zuwendungen',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Expenses->value,
            'number' => '3553',
            'label' => 'Nicht abzugsfähige Bewirtungskosten',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Expenses->value,
            'number' => '3770',
            'label' => 'Säumnis- / Verspätungszuschläge',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Expenses->value,
            'number' => '3780',
            'label' => 'Gewährte Spenden / Zuwendungen',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Expenses->value,
            'number' => '6700',
            'label' => 'Mieten, Pachten',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Expenses->value,
            'number' => '2552',
            'label' => 'Ehrenamtspauschale',
        ]);

        CreateBookingAccount::create([
            'type' => BookingAccountType::Income->value,
            'number' => '2000',
            'label' => 'EINNAHMEN',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Income->value,
            'number' => '2110',
            'label' => 'Echte Mitgliedsbeiträge <300€',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Income->value,
            'number' => '2300',
            'label' => 'Zuschüsse',
        ]);
        CreateBookingAccount::create([
            'type' => BookingAccountType::Income->value,
            'number' => '2301',
            'label' => 'Zuschüsse von Verbänden',
        ]);

        Account::create([
            'name' => 'Vereinskasse',
            'number' => 'VK1',
            'institute' => '',
            'iban' => '',
            'bic' => '',
            'starting_amount' => 15840,
            'type' => AccountType::cash->value,
        ]);
        Account::create([
            'name' => 'PayPal',
            'number' => 'PP1',
            'institute' => '',
            'iban' => '',
            'bic' => '',
            'starting_amount' => 0,
            'type' => AccountType::paypal->value,
        ]);

    }
}
