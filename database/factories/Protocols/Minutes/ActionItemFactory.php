<?php

declare(strict_types=1);

namespace Database\Factories\Protocols\Minutes;

use App\Models\Membership\Member;
use App\Models\Protocols\Minutes\ActionItem;
use App\Models\Protocols\Minutes\MeetingMinute;
use App\Models\Protocols\Minutes\MeetingTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActionItem>
 */
final class ActionItemFactory extends Factory
{
    protected $model = ActionItem::class;

    public function definition()
    {
        return [
            'meeting_minute_id' => MeetingMinute::factory(),
            'meeting_topic_id' => MeetingTopic::factory(),
            'description' => $this->faker->sentence(),
            'assignee_id' => Member::factory(),
            'due_date' => $this->faker->dateTimeThisMonth(),
            'completed' => false,
        ];
    }
}
