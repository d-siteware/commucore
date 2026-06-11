<?php

declare(strict_types=1);

namespace Database\Factories\Blog;

use App\Models\Blog\Post;
use App\Models\Blog\PostImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostImage>
 */
final class PostImageFactory extends Factory
{
    protected $model = PostImage::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'filename' => 'images/'.$this->faker->uuid().'.jpg',
            'original_filename' => $this->faker->word().'.jpg',
            'caption' => ['de' => $this->faker->sentence(), 'en' => $this->faker->sentence()],
            'author' => $this->faker->name(),
        ];
    }
}
