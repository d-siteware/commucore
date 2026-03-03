<?php

declare(strict_types=1);

namespace Database\Factories\Blog;

use App\Enums\EventStatus;
use App\Models\Blog\Post;
use App\Models\Blog\PostType;
use App\Models\Locale;
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
        foreach(Locale::available() as $locale){
            $titles[] = [$locale => fake()->text(50)];
            $slugs[] =  [$locale => fake()->text()];
            $bodies[] =  [$locale => fake()->text()];
        }



        return [
            'title' => $titles,
            'slug' => $slugs,
            'body' => $bodies,
            'user_id' => User::factory()->create()->id,
            'status' => fake()->randomElement(EventStatus::toArray()),
            'post_type_id' => PostType::factory()->create()->id,
            'label' => fake()->text(30),
            'published_at' => Carbon::today()->format('Y-m-d'),

        ];
    }
}
