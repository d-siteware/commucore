<?php

declare(strict_types=1);

namespace Database\Factories\Blog;

use App\Models\Blog\PostType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostType>
 */
final class PostTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ['de' => fake()->text(50), 'de' => $this->faker->text(50)],
            'slug' => fake()->slug(),
            'description' => fake()->text(),
            'color' => fake()->randomElement([
                'red',
                'orange',
                'amber',
                'yellow',
                'lime',
                'green',
                'emerald',
                'teal',
                'cyan',
                'sky',
                'blue',
                'indigo',
                'violet',
                'purple',
                'fuchsia',
                'pink',
                'rose',
            ]),
        ];
    }
}
