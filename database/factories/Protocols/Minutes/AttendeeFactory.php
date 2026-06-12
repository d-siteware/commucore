<?php

declare(strict_types=1);

namespace Database\Factories\Protocols\Minutes;

use App\Models\Membership\Member;
use App\Models\Protocols\Minutes\Attendee;
use App\Models\Protocols\Minutes\MeetingMinute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendee>
 */
final class AttendeeFactory extends Factory
{
    protected $model = Attendee::class;

    public function definition()
    {
        return [
            'meeting_minute_id' => MeetingMinute::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'member_id' => Member::factory(),
        ];
    }
}
