<?php

declare(strict_types=1);

namespace Database\Factories\Blog;

use App\Enums\EventStatus;
use App\Models\Blog\Post;
use App\Models\Blog\PostType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
final class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'title' => [
                'de' => fake('de_DE')->text(50),
                'hu' => fake('hu_HU')->text(50),
                'en' => fake('en_UK')->text(50),
            ],
            'slug' => [
                'de' => fake('de_DE')->slug(4),
                'hu' => fake('hu_HU')->slug(4),
                'en' => fake('en_UK')->slug(4),
            ],
            'body' => [
                'de' => fake('de_DE')->sentences(8, true),
                'hu' => fake('hu_HU')->sentences(8, true),
                'en' => fake('en_UK')->sentences(8, true),
            ],
            'user_id' => User::factory()->create()->id,
            'status' => fake()->randomElement(EventStatus::toArray()),
            'post_type_id' => PostType::factory()->create()->id,
            'label' => fake()->text(30),
            'published_at' => Carbon::today()->format('Y-m-d'),

        ];
    }
}
