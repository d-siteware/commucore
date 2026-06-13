<?php

declare(strict_types=1);

use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;

describe('EventTransaction model', function (): void {
    it('can be created', function (): void {
        $event = Event::factory()->create();
        $transaction = Transaction::factory()->create();

        $eventTransaction = EventTransaction::create([
            'event_id' => $event->id,
            'transaction_id' => $transaction->id,
            'visitor_name' => 'John Doe',
            'gender' => 'male',
        ]);

        expect($eventTransaction)->toBeInstanceOf(EventTransaction::class)
            ->and($eventTransaction->visitor_name)->toBe('John Doe')
            ->and($eventTransaction->gender)->toBe('male');
    });

    it('belongs to an event', function (): void {
        $event = Event::factory()->create();
        $transaction = Transaction::factory()->create();

        $eventTransaction = EventTransaction::create([
            'event_id' => $event->id,
            'transaction_id' => $transaction->id,
        ]);

        expect($eventTransaction->event)->toBeInstanceOf(Event::class)
            ->and($eventTransaction->event->id)->toBe($event->id);
    });

    it('belongs to a transaction', function (): void {
        $event = Event::factory()->create();
        $transaction = Transaction::factory()->create();

        $eventTransaction = EventTransaction::create([
            'event_id' => $event->id,
            'transaction_id' => $transaction->id,
        ]);

        expect($eventTransaction->transaction)->toBeInstanceOf(Transaction::class)
            ->and($eventTransaction->transaction->id)->toBe($transaction->id);
    });
});
