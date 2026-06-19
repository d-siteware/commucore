<?php

declare(strict_types=1);

namespace Database\Factories\Accounting;

use App\Models\Accounting\FiscalYear;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FiscalYear>
 */
final class FiscalYearFactory extends Factory
{
    protected $model = FiscalYear::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = $this->faker->year();

        return [
            'year' => now()->year,
            'opened_at' => Carbon::create($year, 1, 1),
            'closed_at' => null,
            'opened_by' => null,
            'closed_by' => null,
        ];
    }

    // ==================== States ====================

    public function open(): static
    {
        return $this->state([
            'closed_at' => null,
            'closed_by' => null,
        ]);
    }

    public function closed(): static
    {
        return $this->state(function (array $attributes): array {
            $year = $attributes['year'];

            return [
                'closed_at' => Carbon::create($year, 12, 31, 23, 59, 59),
                'closed_by' => null,
            ];
        });
    }

    public function forYear(int $year): static
    {
        return $this->state([
            'year' => $year,
            'opened_at' => Carbon::create($year, 1, 1),
        ]);
    }
}
