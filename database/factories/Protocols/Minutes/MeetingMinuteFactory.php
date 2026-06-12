<?php

declare(strict_types=1);

namespace Database\Factories\Protocols\Minutes;

use App\Models\Protocols\Minutes\MeetingMinute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingMinute>
 */
final class MeetingMinuteFactory extends Factory
{
    protected $model = MeetingMinute::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'meeting_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'location' => $this->faker->optional()->city(),
        ];
    }
}
