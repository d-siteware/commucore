<?php

declare(strict_types=1);

namespace Database\Factories\Event;

use App\Models\Event\Event;
use App\Models\Event\EventTimeline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventTimeline>
 */
final class EventTimelineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('today', '+1 month');
        $end = (clone $start)->modify('+1 hour');

        return [
            'event_id'    => Event::factory(),
            'user_id'     => \App\Models\User::factory(),
            'title'       => $this->faker->sentence(),
            'title_extern'=> ['de' => $this->faker->sentence(), 'en' => $this->faker->sentence()],
            'description' => $this->faker->paragraph(),
            'start'       => $start,
            'end'         => $end,
            'duration'    => 60,
            'place'       => $this->faker->word(),
            'performer'   => $this->faker->name(),
        ];
    }
}
