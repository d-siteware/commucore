<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Accounting\CreateBookingAccount;
use App\Enums\BookingAccountType;
use Illuminate\Database\Seeder;

final class BookingAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

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

    }
}
