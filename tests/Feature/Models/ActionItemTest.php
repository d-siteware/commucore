<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use App\Models\Protocols\Minutes\ActionItem;
use App\Models\Protocols\Minutes\MeetingMinute;
use App\Models\Protocols\Minutes\MeetingTopic;
use Carbon\Carbon;

describe('ActionItem model', function (): void {
    it('can be created with factory', function (): void {
        $item = ActionItem::factory()->create();

        expect($item)->toBeInstanceOf(ActionItem::class)
            ->and($item->meetingMinute)->toBeInstanceOf(MeetingMinute::class)
            ->and($item->meetingTopic)->toBeInstanceOf(MeetingTopic::class)
            ->and($item->assignee)->toBeInstanceOf(Member::class);
    });

    it('is guarded (mass assignable)', function (): void {
        $minute = MeetingMinute::factory()->create();
        $topic = MeetingTopic::factory()->create(['meeting_id' => $minute->id]);
        $member = Member::factory()->create();

        $item = ActionItem::create([
            'meeting_minute_id' => $minute->id,
            'meeting_topic_id' => $topic->id,
            'description' => 'Follow up on budget',
            'assignee_id' => $member->id,
            'completed' => false,
        ]);

        expect($item->description)->toBe('Follow up on budget')
            ->and($item->completed)->toBeFalsy();
    });

    it('casts due_date as datetime', function (): void {
        $item = ActionItem::factory()->create([
            'due_date' => '2024-07-15 14:00:00',
        ]);

        expect($item->due_date)->toBeInstanceOf(Carbon::class)
            ->and($item->due_date->format('Y-m-d H:i:s'))->toBe('2024-07-15 14:00:00');
    });

    it('belongs to a meeting minute', function (): void {
        $minute = MeetingMinute::factory()->create();
        $item = ActionItem::factory()->create(['meeting_minute_id' => $minute->id]);

        expect($item->meetingMinute)->toBeInstanceOf(MeetingMinute::class)
            ->and($item->meetingMinute->id)->toBe($minute->id);
    });

    it('belongs to a meeting topic', function (): void {
        $topic = MeetingTopic::factory()->create();
        $item = ActionItem::factory()->create(['meeting_topic_id' => $topic->id]);

        expect($item->meetingTopic)->toBeInstanceOf(MeetingTopic::class)
            ->and($item->meetingTopic->id)->toBe($topic->id);
    });

    it('belongs to an assignee (member)', function (): void {
        $member = Member::factory()->create();
        $item = ActionItem::factory()->create(['assignee_id' => $member->id]);

        expect($item->assignee)->toBeInstanceOf(Member::class)
            ->and($item->assignee->id)->toBe($member->id);
    });
});
