<?php

declare(strict_types=1);

namespace Database\Factories\Funding;

use App\Enums\FundingStatus;
use App\Models\Funding\Funding;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Funding>
 */
final class FundingFactory extends Factory
{
    protected $model = Funding::class;

    public function definition(): array
    {
        $start = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));

        return [
            'title' => $this->faker->sentence(4),
            'funder' => $this->faker->randomElement([
                'Stadt München',
                'Freistaat Bayern',
                'Bundesministerium',
                'Europäischer Sozialfonds',
                'Deutsche Stiftung',
            ]),
            'description' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(FundingStatus::cases())->value,
            'approved_amount' => $this->faker->optional(0.8)->numberBetween(100_00, 50_000_00), // Cent
            'funding_period_start' => $start->toDateString(),
            'funding_period_end' => $this->faker->optional(0.8)->dateTimeBetween($start, '+3 years'),
            'reference' => $this->faker->optional()->bothify('AZ-####-??'),
            'booking_account_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => FundingStatus::Active->value]);
    }

    public function approved(): static
    {
        return $this->state(['status' => FundingStatus::Approved->value]);
    }

    public function withAmount(int $cents): static
    {
        return $this->state(['approved_amount' => $cents]);
    }

    public function inYear(int $year): static
    {
        return $this->state([
            'funding_period_start' => "{$year}-01-01",
            'funding_period_end' => "{$year}-12-31",
        ]);
    }
}
