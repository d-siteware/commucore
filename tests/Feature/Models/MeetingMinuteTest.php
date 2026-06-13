<?php

declare(strict_types=1);

use App\Models\Protocols\Minutes\ActionItem;
use App\Models\Protocols\Minutes\Attendee;
use App\Models\Protocols\Minutes\MeetingMinute;
use App\Models\Protocols\Minutes\MeetingTopic;
use Carbon\Carbon;

describe('MeetingMinute model', function (): void {
    it('can be created with factory', function (): void {
        $minute = MeetingMinute::factory()->create();

        expect($minute)->toBeInstanceOf(MeetingMinute::class);
    });

    it('is guarded (mass assignable)', function (): void {
        $minute = MeetingMinute::create([
            'title' => 'Team Sync',
            'meeting_date' => '2024-06-01 10:00:00',
            'location' => 'Conference Room A',
            'content' => 'Discussed project milestones',
        ]);

        expect($minute->title)->toBe('Team Sync')
            ->and($minute->location)->toBe('Conference Room A')
            ->and($minute->content)->toBe('Discussed project milestones');
    });

    it('casts meeting_date as datetime', function (): void {
        $minute = MeetingMinute::factory()->create([
            'meeting_date' => '2024-06-01 10:00:00',
        ]);

        expect($minute->meeting_date)->toBeInstanceOf(Carbon::class)
            ->and($minute->meeting_date->format('Y-m-d H:i:s'))->toBe('2024-06-01 10:00:00');
    });

    it('has many attendees', function (): void {
        $minute = MeetingMinute::factory()
            ->has(Attendee::factory()->count(3), 'attendees')
            ->create();

        expect($minute->attendees)->toHaveCount(3)
            ->and($minute->attendees->first())->toBeInstanceOf(Attendee::class);
    });

    it('has many topics', function (): void {
        $minute = MeetingMinute::factory()
            ->has(MeetingTopic::factory()->count(2), 'topics')
            ->create();

        expect($minute->topics)->toHaveCount(2)
            ->and($minute->topics->first())->toBeInstanceOf(MeetingTopic::class);
    });

    it('has many action items', function (): void {
        $minute = MeetingMinute::factory()
            ->has(ActionItem::factory()->count(3), 'actionItems')
            ->create();

        expect($minute->actionItems)->toHaveCount(3)
            ->and($minute->actionItems->first())->toBeInstanceOf(ActionItem::class);
    });
});
