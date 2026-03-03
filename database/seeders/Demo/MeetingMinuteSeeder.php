<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\ActionItem;
use App\Models\Attendee;
use App\Models\MeetingMinute;
use App\Models\MeetingTopic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

final class MeetingMinuteSeeder extends Seeder
{
    private const MEETING_TYPES = [
        'board',
        'general_assembly',
        'working_group',
        'general',
    ];

    public function run(): void
    {
        foreach (self::MEETING_TYPES as $type) {
            foreach (DemoMeetingText::meetingsForType($type) as $meetingData) {
                $this->createMeetingMinute($meetingData);
            }
        }

        $this->command->info('MeetingMinuteSeeder: '.MeetingMinute::count().' Protokolle erstellt.');
    }

    private function createMeetingMinute(array $data): void
    {
        $meetingDate = Carbon::now()->subDays(rand(14, 365));

        /** @var MeetingMinute $minute */
        $minute = MeetingMinute::create([
            'title' => $data['title'],
            'meeting_date' => $meetingDate,
            'location' => $data['location'],
        ]);

        $this->createAttendees($minute);
        $this->createTopics($minute, $data['topics'], $meetingDate);
    }

    private function createAttendees(MeetingMinute $minute): void
    {
        $allAttendees = DemoMeetingText::attendees();
        $count = rand(3, count($allAttendees));

        foreach (array_slice($allAttendees, 0, $count) as $attendeeData) {
            Attendee::create([
                'meeting_minute_id' => $minute->id,
                'name' => $attendeeData['name'],
                'email' => $attendeeData['email'],
                'member_id' => null,
            ]);
        }
    }

    private function createTopics(MeetingMinute $minute, array $topics, Carbon $meetingDate): void
    {
        foreach ($topics as $topicData) {
            /** @var MeetingTopic $topic */
            $topic = MeetingTopic::create([
                'meeting_id' => $minute->id,
                'content' => $topicData['content'],
            ]);

            $this->createActionItems($minute, $topic, $topicData['action_items'], $meetingDate);
        }
    }

    private function createActionItems(
        MeetingMinute $minute,
        MeetingTopic $topic,
        array $actionItems,
        Carbon $meetingDate
    ): void {
        foreach ($actionItems as $itemData) {
            ActionItem::create([
                'meeting_minute_id' => $minute->id,
                'meeting_topic_id' => $topic->id,
                'description' => $itemData['description'],
                'due_date' => (clone $meetingDate)->addDays($itemData['due_days']),
                'assignee_id' => null,
                'completed' => 0,
            ]);
        }
    }
}
