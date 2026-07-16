<?php

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use Carbon\Carbon;

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Europe/Berlin'));
});

afterEach(function (): void {
    Carbon::setTestNow();
});

describe('Transaction FiscalYear Methods', function (): void {

    it('can check if transaction is locked in specific fiscal year (pivot)', function (): void {
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

    it('can scope unlocked transactions (FY open)', function (): void {
        $openFy = FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);
        $closedFy = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => now()]);

        $inOpen = Transaction::factory()->create([
            'date' => '2026-06-15',
            'fiscal_year_id' => $openFy->id,
        ]);
        Transaction::factory()->create([
            'date' => '2025-06-15',
            'fiscal_year_id' => $closedFy->id,
        ]);

        $unlocked = Transaction::unlocked()->get();

        expect($unlocked)->toHaveCount(1)
            ->first()->id->toBe($inOpen->id);
    });

    it('can scope locked transactions (FY closed)', function (): void {
        $openFy = FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);
        $closedFy = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => now()]);

        $inClosed = Transaction::factory()->create([
            'date' => '2025-06-15',
            'fiscal_year_id' => $closedFy->id,
        ]);
        Transaction::factory()->create([
            'date' => '2026-06-15',
            'fiscal_year_id' => $openFy->id,
        ]);

        $locked = Transaction::lockedInYear()->get();

        expect($locked)->toHaveCount(1)
            ->first()->id->toBe($inClosed->id);
    });

    it('checks if transaction is editable based on FY open/closed', function (): void {
        $openFy = FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);

        $transaction = Transaction::factory()->create([
            'date' => '2026-06-15',
            'fiscal_year_id' => $openFy->id,
        ]);

        expect($transaction->isEditable())->toBeTrue();

        $openFy->update(['closed_at' => now()]);

        expect($transaction->fresh()->isEditable())->toBeFalse();
    });

    it('scope in fiscal year filters correctly', function (): void {
        $fy2025 = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => null]);
        $fy2026 = FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);

        Transaction::factory()->create(['date' => '2025-01-01', 'fiscal_year_id' => $fy2025->id]);
        Transaction::factory()->create(['date' => '2026-01-01', 'fiscal_year_id' => $fy2026->id]);

        expect(Transaction::inFiscalYear($fy2025->id)->count())->toBe(1);
    });
});

describe('Transaction Fiscal Year Lock Methods', function (): void {

    it('returns null when fiscal year does not exist', function (): void {
        $transaction = Transaction::factory()->create();

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
