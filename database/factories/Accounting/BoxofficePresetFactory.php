<?php

declare(strict_types=1);

namespace Database\Factories\Accounting;

use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\BookingAccountType;
use App\Models\Accounting\BoxofficePreset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoxofficePreset>
 */
final class BoxofficePresetFactory extends Factory
{
    protected $model = BoxofficePreset::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_account_type_id' => BookingAccountType::factory(),
            'booking_account_id' => BookingAccount::factory(),
            'priority' => fake()->numberBetween(1, 10),
        ];
    }
}
