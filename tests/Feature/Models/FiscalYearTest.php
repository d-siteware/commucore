<?php

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

beforeEach(function (): void {
    $this->user = User::factory()->create(['is_admin' => true]);
});

describe('FiscalYear Model', function (): void {

    it('can create a fiscal year', function (): void {
        $fiscalYear = FiscalYear::create([
            'year' => 2024,
            'opened_at' => now(),
            'opened_by' => $this->user->id,
        ]);

        expect($fiscalYear)
            ->year->toBe(2024)
            ->opened_at->toBeInstanceOf(Carbon::class)
            ->opened_by->toBe($this->user->id)
            ->closed_at->toBeNull();
    });

    it('has correct relationships', function (): void {
        $fiscalYear = FiscalYear::factory()->create([
            'opened_by' => $this->user->id,
        ]);

        expect($fiscalYear->openedBy)
            ->toBeInstanceOf(User::class)
            ->id->toBe($this->user->id);
    });

    it('can check if fiscal year is closed', function (): void {
        $openYear = FiscalYear::factory()->create(['year' => now()->year, 'closed_at' => null]);
        $closedYear = FiscalYear::factory()->create(['year' => now()->subYear()->year, 'closed_at' => now()]);

        expect($openYear->isClosed())->toBeFalse()
            ->and($openYear->isOpen())->toBeTrue()
            ->and($closedYear->isClosed())->toBeTrue()
            ->and($closedYear->isOpen())->toBeFalse();
    });

    it('can get active fiscal year', function (): void {
        FiscalYear::factory()->create(['year' => 2022, 'closed_at' => now()]);
        FiscalYear::factory()->create(['year' => 2023, 'closed_at' => now()]);
        $activeFY = FiscalYear::factory()->create(['year' => 2024, 'closed_at' => null]);

        $result = FiscalYear::getActive();

        expect($result)
            ->not->toBeNull()
            ->id->toBe($activeFY->id)
            ->year->toBe(2024);
    });

    it('returns null when no active fiscal year exists', function (): void {
        FiscalYear::factory()->create(['year' => 2023, 'closed_at' => now()]);

        expect(FiscalYear::getActive())->toBeNull();
    });

    it('can get current fiscal year from session', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);
        session(['fiscalYearId' => $fiscalYear->id]);

        $result = FiscalYear::getCurrent();

        expect($result)
            ->not->toBeNull()
            ->id->toBe($fiscalYear->id);
    });

    it('returns null when session fiscal year does not exist', function (): void {
        session(['fiscalYearId' => 99999]);

        expect(FiscalYear::getCurrent())->toBeNull();
    });

    it('can get or create fiscal year', function (): void {
        expect(FiscalYear::count())->toBe(0);

        $fiscalYear = FiscalYear::getOrCreate(2024, $this->user->id);

        expect(FiscalYear::count())->toBe(1)
            ->and($fiscalYear->year)->toBe(2024)
            ->and($fiscalYear->opened_by)->toBe($this->user->id);

        // Should not create duplicate
        $sameFiscalYear = FiscalYear::getOrCreate(2024, $this->user->id);

        expect(FiscalYear::count())->toBe(1)
            ->and($sameFiscalYear->id)->toBe($fiscalYear->id);
    });

    it('prevents duplicate years', function (): void {
        FiscalYear::factory()->create(['year' => 2024]);

        FiscalYear::create(['year' => 2024, 'opened_at' => now()]);
    })->throws(QueryException::class);
});

describe('FiscalYear Transaction Relationship', function (): void {

    it('can attach transactions to fiscal year', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);
        $transaction = Transaction::factory()->create([
            'date' => '2024-06-15',
        ]);

        $fiscalYear->transactions()->attach($transaction->id, [
            'locked_at' => now(),
        ]);

        expect($fiscalYear->transactions)->toHaveCount(1)
            ->first()->id->toBe($transaction->id);
    });

    it('can detach transactions from fiscal year', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);
        $transaction = Transaction::factory()->create();

        $fiscalYear->transactions()->attach($transaction->id, ['locked_at' => now()]);
        expect($fiscalYear->transactions)->toHaveCount(1);

        $fiscalYear->transactions()->detach($transaction->id);
        expect($fiscalYear->fresh()->transactions)->toHaveCount(0);
    });

    it('stores locked_at timestamp in pivot table', function (): void {
        $fiscalYear = FiscalYear::factory()->create(['year' => 2024]);
        $transaction = Transaction::factory()->create();
        $lockedAt = Carbon::parse('2024-12-31 23:59:59');

        $fiscalYear->transactions()->attach($transaction->id, [
            'locked_at' => $lockedAt,
        ]);

        $pivotTransaction = $fiscalYear->transactions()->first();

        expect($pivotTransaction->pivot->locked_at)
            ->toBeInstanceOf(Carbon::class)
            ->equalTo($lockedAt);
    });
});
