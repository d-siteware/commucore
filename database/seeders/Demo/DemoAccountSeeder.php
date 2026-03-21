<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\AccountType;
use App\Models\Accounting\Account;
use Illuminate\Database\Seeder;

final class DemoAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::createOrFirst([
            'name' => 'Vereinskasse',
            'number' => 'VK1',
            'institute' => '',
            'iban' => '',
            'bic' => '',
            'starting_amount' => 50000,
            'type' => AccountType::cash->value,
        ]);
        Account::createOrFirst([
            'name' => 'PayPal',
            'number' => 'PP1',
            'institute' => '',
            'iban' => '',
            'bic' => '',
            'starting_amount' => 75000,
            'type' => AccountType::paypal->value,
        ]);
        Account::createOrFirst([
            'name' => 'Sparkasse Berlin',
            'number' => 'BK1',
            'institute' => 'Sparkasse Berlin',
            'iban' => 'DE12345678901234567890',
            'bic' => 'BELADEBEXXX',
            'starting_amount' => 150000,
            'type' => AccountType::bank->value,
        ]);
    }
}
