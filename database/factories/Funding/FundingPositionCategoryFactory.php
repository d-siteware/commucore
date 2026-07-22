<?php

declare(strict_types=1);

namespace Database\Factories\Funding;

use App\Models\Funding\FundingPositionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundingPositionCategory>
 */
final class FundingPositionCategoryFactory extends Factory
{
    protected $model = FundingPositionCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'slug' => FundingPositionCategory::CUSTOM_SLUG_PREFIX.\Illuminate\Support\Str::slug($name),
            'name' => $name,
            'is_system' => false,
            'source' => 'custom',
            'sort' => 100,
        ];
    }

    public function system(): static
    {
        return $this->state(fn (): array => [
            'slug' => 'system-'.\Illuminate\Support\Str::random(8),
            'is_system' => true,
            'source' => 'system',
        ]);
    }
}
