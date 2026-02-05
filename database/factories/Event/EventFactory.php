<?php

declare(strict_types=1);

namespace Database\Factories\Event;

use App\Enums\EventStatus;
use App\Models\Event\Event;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
final class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inter = fake()->numberBetween(0, 5);

        $event_date = Carbon::today();
        $start = $event_date->format('H:i');
        $end = $event_date->addHours($inter)->format('H:i');

        $fee = fake()->randomNumber(2);
        $disc_fee = round($fee * 0.7, 0);

        return [
            'name' => $this->faker->domainName,
            'event_date' => $event_date->format('Y-m-d'),
            'start_time' => $start,
            'end_time' => $end,
            'slug' => [
                'de' => fake()->unique()->slug(4),
                'hu' => fake()->unique()->slug(5),
            ],
            'entry_fee' => $fee,
            'entry_fee_discounted' => $disc_fee,
            'excerpt' => [
                'de' => fake()->sentences(2, true),
                'hu' => fake()->sentences(2, true),
            ],
            'title' => [
                'de' => fake()->sentence(),
                'hu' => fake()->sentence(),
            ],
            'description' => [
                'de' => fake()->sentences(10, true),
                'hu' => fake()->sentences(10, true),
            ],
            'status' => EventStatus::PUBLISHED->value, // Fixed for test
            //            'status' => $this->faker->randomElement(EventStatus::cases())->value,
            'venue_id' => Venue::factory()->create(),
        ];
    }
}
