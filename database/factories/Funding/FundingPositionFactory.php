<?php

declare(strict_types=1);

namespace Database\Factories\Funding;

use App\Models\Funding\Funding;
use App\Models\Funding\FundingPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundingPosition>
 */
final class FundingPositionFactory extends Factory
{
    protected $model = FundingPosition::class;

    public function definition(): array
    {
        return [
            'funding_id' => Funding::factory(),
            'title' => $this->faker->words(3, true),
            'budget' => $this->faker->numberBetween(1_000_00, 100_000_00), // Cent
            'funding_position_category_id' => null,
            'member_id' => null,
            'due_date' => null,
            'description' => null,
        ];
    }

    public function withBudget(int $cents): static
    {
        return $this->state(['budget' => $cents]);
    }
}
