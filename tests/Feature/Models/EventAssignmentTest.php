<?php

declare(strict_types=1);

use App\Enums\AssignmentStatus;
use App\Models\Event\Event;
use App\Models\Event\EventAssignment;
use App\Models\Membership\Member;
use App\Models\User;
use Carbon\Carbon;

describe('EventAssignment model', function (): void {
    it('can be created with factory', function (): void {
        $assignment = EventAssignment::factory()->create();

        expect($assignment)->toBeInstanceOf(EventAssignment::class)
            ->and($assignment->event)->toBeInstanceOf(Event::class)
            ->and($assignment->member)->toBeInstanceOf(Member::class)
            ->and($assignment->user)->toBeInstanceOf(User::class);
    });

    it('is guarded (mass assignable)', function (): void {
        $event = Event::factory()->create();
        $member = Member::factory()->create();
        $user = User::factory()->create();

        $assignment = EventAssignment::create([
            'task' => 'Setup venue',
            'description' => 'Arrange chairs and tables',
            'event_id' => $event->id,
            'member_id' => $member->id,
            'user_id' => $user->id,
            'status' => AssignmentStatus::pending,
            'due_at' => '2024-07-01 10:00:00',
            'amount' => 5000,
        ]);

        expect($assignment->task)->toBe('Setup venue')
            ->and($assignment->description)->toBe('Arrange chairs and tables');
    });

    it('casts status as AssignmentStatus enum', function (): void {
        $assignment = EventAssignment::factory()->create([
            'status' => AssignmentStatus::confirmed,
        ]);

        expect($assignment->status)->toBeInstanceOf(AssignmentStatus::class)
            ->and($assignment->status)->toBe(AssignmentStatus::confirmed);
    });

    it('casts due_at as datetime', function (): void {
        $assignment = EventAssignment::factory()->create([
            'due_at' => '2024-07-01 10:00:00',
        ]);

        expect($assignment->due_at)->toBeInstanceOf(Carbon::class)
            ->and($assignment->due_at->format('Y-m-d H:i:s'))->toBe('2024-07-01 10:00:00');
    });

    it('belongs to an event', function (): void {
        $event = Event::factory()->create();
        $assignment = EventAssignment::factory()->create(['event_id' => $event->id]);

        expect($assignment->event)->toBeInstanceOf(Event::class)
            ->and($assignment->event->id)->toBe($event->id);
    });

    it('belongs to a member', function (): void {
        $member = Member::factory()->create();
        $assignment = EventAssignment::factory()->create(['member_id' => $member->id]);

        expect($assignment->member)->toBeInstanceOf(Member::class)
            ->and($assignment->member->id)->toBe($member->id);
    });

    it('belongs to a user', function (): void {
        $user = User::factory()->create();
        $assignment = EventAssignment::factory()->create(['user_id' => $user->id]);

        expect($assignment->user)->toBeInstanceOf(User::class)
            ->and($assignment->user->id)->toBe($user->id);
    });

    it('returns status color', function (): void {
        $assignment = EventAssignment::factory()->create([
            'status' => AssignmentStatus::confirmed,
        ]);

        expect($assignment->statusColor())->toBe('lime');
    });

    it('returns status label', function (): void {
        $assignment = EventAssignment::factory()->create([
            'status' => AssignmentStatus::pending,
        ]);

        expect($assignment->statusLabel())->toBe(__('assignment.status.pending'));
    });

    it('returns due string for future date', function (): void {
        $assignment = EventAssignment::factory()->create([
            'due_at' => now()->addDays(5),
        ]);

        expect($assignment->getDueString())->not->toBe('-');
    });

    it('returns due string for past date', function (): void {
        $assignment = EventAssignment::factory()->create([
            'due_at' => '2024-01-15 10:00:00',
        ]);

        expect($assignment->getDueString())->toBe('2024-01-15');
    });

    it('returns dash when no due date', function (): void {
        $assignment = EventAssignment::factory()->create([
            'due_at' => null,
        ]);

        expect($assignment->getDueString())->toBe('-');
    });

    it('formats amount as EUR string', function (): void {
        $assignment = EventAssignment::factory()->create([
            'amount' => 1500,
        ]);

        expect($assignment->amountString())->toBe('15,00 EUR');
    });

    it('returns fallback when no amount', function (): void {
        $assignment = EventAssignment::factory()->create([
            'amount' => null,
        ]);

        expect($assignment->amountString())->toBe('-,--');
    });
});
