<?php

declare(strict_types=1);

namespace Database\Factories\Protocols\Minutes;

use App\Models\Protocols\Minutes\MeetingMinute;
use App\Models\Protocols\Minutes\MeetingTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingTopic>
 */
final class MeetingTopicFactory extends Factory
{
    protected $model = MeetingTopic::class;

    public function definition()
    {
        return [
            'content' => $this->faker->paragraph(),
            'meeting_id' => MeetingMinute::factory(),
        ];
    }
}
