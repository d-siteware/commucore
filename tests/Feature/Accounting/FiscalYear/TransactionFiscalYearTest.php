<?php

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use Carbon\Carbon;

describe('Transaction FiscalYear Methods', function (): void {

    it('can check if transaction is locked in specific fiscal year', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);
        $transaction = Transaction::factory()->create(['date' => '2024-06-15']);

        expect($transaction->isLockedInFiscalYear(2024))->toBeFalse();

        $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => now()]);

        expect($transaction->fresh()->isLockedInFiscalYear(2024))->toBeTrue();
    });

    it('can get locked at timestamp for fiscal year', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);
        $transaction = Transaction::factory()->create();
        $lockedAt = \Carbon\Carbon::parse('2024-12-31 23:59:59');

        $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => $lockedAt]);

        $result = $transaction->getLockedAtForFiscalYear(2024);

        expect($result)
            ->not->toBeNull()
            ->toContain('2024-12-31 23:59:59');
    });

    it('returns null for unlocked transaction', function (): void {
        $transaction = Transaction::factory()->create();

        expect($transaction->getLockedAtForFiscalYear(2024))->toBeNull();
    });

    it('can scope unlocked transactions', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);

        $locked = Transaction::factory()->create(['date' => '2024-01-15']);
        $unlocked = Transaction::factory()->create(['date' => '2024-06-15']);

        $fiscalYear->transactions()->attach($locked->id, ['locked_at' => now()]);

        $unlockedTransactions = Transaction::unlocked(2024)->get();

        expect($unlockedTransactions)
            ->toHaveCount(1)
            ->first()->id->toBe($unlocked->id);
    });

    it('can scope locked transactions', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);

        $locked = Transaction::factory()->create(['date' => '2024-01-15']);
        $unlocked = Transaction::factory()->create(['date' => '2024-06-15']);

        $fiscalYear->transactions()->attach($locked->id, ['locked_at' => now()]);

        $lockedTransactions = Transaction::lockedInYear(2024)->get();

        expect($lockedTransactions)
            ->toHaveCount(1)
            ->first()->id->toBe($locked->id);
    });

    it('checks if transaction is editable based on session year', function (): void {
        session(['financialYear' => 2024]);

        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);
        $transaction = Transaction::factory()->create(['date' => '2024-06-15']);

        expect($transaction->isEditable())->toBeTrue();

        $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => now()]);

        expect($transaction->fresh()->isEditable())->toBeFalse();
    });
});

describe('Transaction Fiscal Year Lock Methods', function (): void {

    it('returns null when fiscal year does not exist', function (): void {
        $transaction = Transaction::factory()->create(['date' => '2024-06-15']);

        expect($transaction->getLockedAtForFiscalYear(2024))->toBeNull();
    });

    it('returns null when transaction is not locked', function (): void {
        FiscalYear::factory()->create(['year' => 2024]);
        $transaction = Transaction::factory()->create(['date' => '2024-06-15']);

        expect($transaction->getLockedAtForFiscalYear(2024))->toBeNull();
    });

    it('returns locked_at timestamp when transaction is locked', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);
        $transaction = Transaction::factory()->create(['date' => '2024-06-15']);
        $lockedAt = Carbon::parse('2024-12-31 23:59:59');

        $fiscalYear->transactions()->attach($transaction->id, [
            'locked_at' => $lockedAt,
        ]);

        $result = $transaction->fresh()->getLockedAtForFiscalYear(2024);

        expect($result)
            ->not->toBeNull()
            ->toContain('2024-12-31 23:59:59');
    });

    it('checks if transaction is locked correctly', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);
        $transaction = Transaction::factory()->create(['date' => '2024-06-15']);

        expect($transaction->isLockedInFiscalYear(2024))->toBeFalse();

        $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => now()]);

        expect($transaction->fresh()->isLockedInFiscalYear(2024))->toBeTrue();
    });
});
