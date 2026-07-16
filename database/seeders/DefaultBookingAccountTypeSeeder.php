<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Accounting\BookingAccountType;
use App\Models\Accounting\PaymentAccountMapping;
use Illuminate\Database\Seeder;

/**
 * Legt den Standard-Kontenrahmen (SKR42) sowie die Default-Geldkontomappings an.
 *
 * Idempotent: Läuft nur, wenn noch kein SKR42-Type existiert.
 * Für Bestandsinstanzen wird dieser Seeder zum No-op, da die Data-Migrationen
 * (2026_07_14_000005, 2026_07_14_000006) die Daten bereits angelegt haben.
 */
final class DefaultBookingAccountTypeSeeder extends Seeder
{
    public function run(): void
    {
        $skr42 = BookingAccountType::firstOrCreate(
            ['slug' => 'skr42'],
            [
                'name' => 'SKR42',
                'datev_skr_code' => '42',
                'account_length' => 5,
            ],
        );

        $defaults = [
            ['account_type' => 'cash',   'booking_account_number' => '16000'],
            ['account_type' => 'bank',   'booking_account_number' => '18000'],
            ['account_type' => 'paypal', 'booking_account_number' => '18100'],
        ];

        foreach ($defaults as $mapping) {
            PaymentAccountMapping::firstOrCreate(
                [
                    'booking_account_type_id' => $skr42->id,
                    'account_type' => $mapping['account_type'],
                ],
                [
                    'booking_account_number' => $mapping['booking_account_number'],
                ],
            );
        }
    }
}
