<?php

declare(strict_types=1);

use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventVisitor;
use App\Models\Membership\Member;

describe('EventVisitor model', function (): void {
    it('can be created with factory', function (): void {
        $event = Event::factory()->create();
        $visitor = EventVisitor::factory()->create(['event_id' => $event->id]);

        expect($visitor)->toBeInstanceOf(EventVisitor::class);
    });

    it('has fillable attributes', function (): void {
        $event = Event::factory()->create();
        $member = Member::factory()->create();
        $transaction = Transaction::factory()->create();

        $visitor = EventVisitor::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+987654321',
            'event_id' => $event->id,
            'member_id' => $member->id,
            'transaction_id' => $transaction->id,
            'gender' => 'female',
        ]);

        expect($visitor->name)->toBe('Jane Doe')
            ->and($visitor->email)->toBe('jane@example.com')
            ->and($visitor->event_id)->toBe($event->id);
    });

    it('belongs to an event', function (): void {
        $event = Event::factory()->create();
        $visitor = EventVisitor::factory()->create(['event_id' => $event->id]);

        expect($visitor->event)->toBeInstanceOf(Event::class)
            ->and($visitor->event->id)->toBe($event->id);
    });

    it('belongs to a member (nullable)', function (): void {
        $event = Event::factory()->create();
        $member = Member::factory()->create();
        $visitor = EventVisitor::factory()->create([
            'event_id' => $event->id,
            'member_id' => $member->id,
        ]);

        expect($visitor->member)->toBeInstanceOf(Member::class)
            ->and($visitor->member->id)->toBe($member->id);
    });

    it('belongs to a transaction (nullable)', function (): void {
        $event = Event::factory()->create();
        $transaction = Transaction::factory()->create();
        $visitor = EventVisitor::factory()->create([
            'event_id' => $event->id,
            'transaction_id' => $transaction->id,
        ]);

        expect($visitor->transaction)->toBeInstanceOf(Transaction::class)
            ->and($visitor->transaction->id)->toBe($transaction->id);
    });

    it('stores gender string', function (): void {
        $event = Event::factory()->create();
        $visitor = EventVisitor::factory()->create([
            'event_id' => $event->id,
            'gender' => 'male',
        ]);

        expect($visitor->gender)->toBe('male');
    });
});
