<?php

declare(strict_types=1);

use App\Models\Protocols\Minutes\ActionItem;
use App\Models\Protocols\Minutes\MeetingMinute;
use App\Models\Protocols\Minutes\MeetingTopic;

describe('MeetingTopic model', function (): void {
    it('can be created with factory', function (): void {
        $topic = MeetingTopic::factory()->create();

        expect($topic)->toBeInstanceOf(MeetingTopic::class)
            ->and($topic->meetingMinute)->toBeInstanceOf(MeetingMinute::class);
    });

    it('is guarded (mass assignable)', function (): void {
        $minute = MeetingMinute::factory()->create();

        $topic = MeetingTopic::create([
            'meeting_id' => $minute->id,
            'content' => 'Budget Review',
        ]);

        expect($topic->content)->toBe('Budget Review')
            ->and($topic->meeting_id)->toBe($minute->id);
    });

    it('belongs to a meeting minute', function (): void {
        $minute = MeetingMinute::factory()->create();
        $topic = MeetingTopic::factory()->create(['meeting_id' => $minute->id]);

        expect($topic->meetingMinute)->toBeInstanceOf(MeetingMinute::class)
            ->and($topic->meetingMinute->id)->toBe($minute->id);
    });

    it('has many action items', function (): void {
        $topic = MeetingTopic::factory()
            ->has(ActionItem::factory()->count(2), 'actionItems')
            ->create();

        expect($topic->actionItems)->toHaveCount(2)
            ->and($topic->actionItems->first())->toBeInstanceOf(ActionItem::class);
    });
});
