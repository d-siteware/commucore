<?php

declare(strict_types=1);

namespace Database\Factories\Accounting;

use App\Models\Accounting\BookingAccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingAccountType>
 */
final class BookingAccountTypeFactory extends Factory
{
    protected $model = BookingAccountType::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'slug' => fake()->unique()->slug(1),
            'datev_skr_code' => (string) fake()->randomNumber(2),
            'account_length' => 5,
        ];
    }
}
