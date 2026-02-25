<?php

declare(strict_types=1);

namespace Database\Factories\Event;

use App\Enums\Gender;
use App\Models\Event\EventVisitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventVisitor>
 */
final class EventVisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(Gender::class);

        return [
            'name' => fake()->name,
            'email' => fake()->email,
            'gender' => $gender,
        ];
    }
}
