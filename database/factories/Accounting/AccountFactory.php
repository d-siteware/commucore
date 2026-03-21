<?php

declare(strict_types=1);

namespace Database\Factories\Accounting;

use App\Enums\AccountType;
use App\Models\Accounting\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
final class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'number' => fake()->name,
            'type' => fake()->randomElement(AccountType::cases()),
            'starting_amount' => fake()->randomNumber(),
        ];
    }
}
