<?php

declare(strict_types=1);

use App\Models\Event\Event;
use App\Models\Event\EventTimeline;
use App\Models\Membership\Member;
use App\Models\User;
use Carbon\Carbon;

describe('EventTimeline model', function (): void {
    it('can be created with factory', function (): void {
        $timeline = EventTimeline::factory()->create();

        expect($timeline)->toBeInstanceOf(EventTimeline::class)
            ->and($timeline->event)->toBeInstanceOf(Event::class)
            ->and($timeline->user)->toBeInstanceOf(User::class);
    });

    it('is guarded (mass assignable)', function (): void {
        $event = Event::factory()->create();
        $user = User::factory()->create();

        $timeline = EventTimeline::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'title' => 'Opening Ceremony',
            'description' => 'The grand opening',
            'start' => '2024-07-01 10:00:00',
            'end' => '2024-07-01 11:00:00',
            'duration' => 60,
            'place' => 'Main Hall',
            'performer' => 'John Smith',
        ]);

        expect($timeline->title)->toBe('Opening Ceremony')
            ->and($timeline->description)->toBe('The grand opening');
    });

    it('casts start and end as datetime', function (): void {
        $timeline = EventTimeline::factory()->create([
            'start' => '2024-07-01 10:00:00',
            'end' => '2024-07-01 12:30:00',
        ]);

        expect($timeline->start)->toBeInstanceOf(Carbon::class)
            ->and($timeline->start->format('Y-m-d H:i:s'))->toBe('2024-07-01 10:00:00')
            ->and($timeline->end)->toBeInstanceOf(Carbon::class)
            ->and($timeline->end->format('Y-m-d H:i:s'))->toBe('2024-07-01 12:30:00');
    });

    it('casts title_extern as array', function (): void {
        $timeline = EventTimeline::factory()->create([
            'title_extern' => ['de' => 'Eröffnung', 'en' => 'Opening'],
        ]);

        expect($timeline->title_extern)->toBeArray()
            ->and($timeline->title_extern['de'])->toBe('Eröffnung')
            ->and($timeline->title_extern['en'])->toBe('Opening');
    });

    it('belongs to an event', function (): void {
        $event = Event::factory()->create();
        $timeline = EventTimeline::factory()->create(['event_id' => $event->id]);

        expect($timeline->event)->toBeInstanceOf(Event::class)
            ->and($timeline->event->id)->toBe($event->id);
    });

    it('belongs to a user', function (): void {
        $user = User::factory()->create();
        $timeline = EventTimeline::factory()->create(['user_id' => $user->id]);

        expect($timeline->user)->toBeInstanceOf(User::class)
            ->and($timeline->user->id)->toBe($user->id);
    });

    it('belongs to a member (nullable)', function (): void {
        $member = Member::factory()->create();
        $timeline = EventTimeline::factory()->create(['member_id' => $member->id]);

        expect($timeline->member)->toBeInstanceOf(Member::class)
            ->and($timeline->member->id)->toBe($member->id);
    });
});
