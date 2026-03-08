<?php

namespace Database\Factories\Project;

use App\Models\Accounting\Transaction;
use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project\ProjectTransaction>
 */
class ProjectTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => fake()->randomElement(Project::pluck('id')->toArray()),
            'transaction_id' => fake()->randomElement(Transaction::pluck('id')->toArray()),
            'allocated_amount' => fake()->randomNumber(),

        ];
    }
}
