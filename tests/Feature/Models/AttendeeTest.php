<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use App\Models\Protocols\Minutes\Attendee;
use App\Models\Protocols\Minutes\MeetingMinute;

describe('Attendee model', function (): void {
    it('can be created with factory', function (): void {
        $attendee = Attendee::factory()->create();

        expect($attendee)->toBeInstanceOf(Attendee::class)
            ->and($attendee->meetingMinute)->toBeInstanceOf(MeetingMinute::class);
    });

    it('is guarded (mass assignable)', function (): void {
        $minute = MeetingMinute::factory()->create();
        $member = Member::factory()->create();

        $attendee = Attendee::create([
            'meeting_minute_id' => $minute->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'member_id' => $member->id,
        ]);

        expect($attendee->name)->toBe('John Doe')
            ->and($attendee->email)->toBe('john@example.com')
            ->and($attendee->meeting_minute_id)->toBe($minute->id);
    });

    it('belongs to a meeting minute', function (): void {
        $minute = MeetingMinute::factory()->create();
        $attendee = Attendee::factory()->create(['meeting_minute_id' => $minute->id]);

        expect($attendee->meetingMinute)->toBeInstanceOf(MeetingMinute::class)
            ->and($attendee->meetingMinute->id)->toBe($minute->id);
    });

    it('belongs to a member (nullable)', function (): void {
        $member = Member::factory()->create();
        $attendee = Attendee::factory()->create(['member_id' => $member->id]);

        expect($attendee->member)->toBeInstanceOf(Member::class)
            ->and($attendee->member->id)->toBe($member->id);
    });

    it('can be created without a member', function (): void {
        $minute = MeetingMinute::factory()->create();

        $attendee = Attendee::create([
            'meeting_minute_id' => $minute->id,
            'name' => 'Guest User',
        ]);

        expect($attendee->name)->toBe('Guest User')
            ->and($attendee->member_id)->toBeNull();
    });
});
