<?php

declare(strict_types=1);

namespace Database\Factories\Membership;

use App\Models\Membership\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Membership\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $baseName = $this->faker->jobTitle();

        return [
            'name' => [
                'de' => $baseName.' (DE)',
                'hu' => $baseName.' (HU)',
                'en' => $baseName,
            ],
            'description' => $this->faker->sentence(),
            'can_manage_accounting' => false,
            'sort' => 0,
        ];
    }

    public function withAccounting(): static
    {
        return $this->state(fn (array $attributes) => [
            'can_manage_accounting' => true,
        ]);
    }
}
