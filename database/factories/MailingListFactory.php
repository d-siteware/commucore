<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MailingList;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MailingList>
 */
final class MailingListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->freeEmail,
            'terms_accepted' => true,
            'update_on_events' => fake()->randomElement([true, false]),
            'update_on_articles' => fake()->randomElement([true, false]),
            'update_on_notifications' => fake()->randomElement([true, false]),
            'verified_at' => fake()->dateTimeBetween('-1 month'),
            'verification_token' => Str::random(40),
            'locale' => fake()->randomElement(['de', 'hu']),
        ];
    }
}
