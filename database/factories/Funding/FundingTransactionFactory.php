<?php

namespace Database\Factories\Funding;

use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Funding\FundingTransaction>
 */
class FundingTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'funding_id' => fake()->randomElement(Funding::pluck('id')->toArray()),
            'transaction_id' => fake()->randomElement(Transaction::pluck('id')->toArray()),
            'allocated_amount' => fake()->randomNumber(),
        ];
    }
}
