<?php

declare(strict_types=1);

namespace Database\Factories\Project;

use App\Enums\ProjectStatus;
use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Project>
 */
final class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $start = Carbon::parse($this->faker->dateTimeBetween('-2 years', 'now'));

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(ProjectStatus::cases())->value,
            'start_date' => $start->toDateString(),
            'end_date' => $this->faker->optional(0.7)->dateTimeBetween($start, '+2 years'),
            'booking_account_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => ProjectStatus::Active->value]);
    }

    public function planned(): static
    {
        return $this->state(['status' => ProjectStatus::Planned->value]);
    }

    public function completed(): static
    {
        return $this->state(['status' => ProjectStatus::Completed->value]);
    }

    public function inYear(int $year): static
    {
        return $this->state([
            'start_date' => "{$year}-01-01",
            'end_date' => "{$year}-12-31",
        ]);
    }
}
