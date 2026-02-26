<?php

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\User;
use App\Services\Accounting\FiscalYearService;

beforeEach(function () {
    $this->service = new FiscalYearService;
    $this->user = User::factory()
        ->create(['is_admin' => true]);
});

describe('FiscalYearService - Close with Selection', function () {
    it('can close fiscal year with selected transactions', function () {
        $fiscalYear = FiscalYear::factory()
            ->create(['year' => 2024]);
        $transactions = Transaction::factory()
            ->count(5)
            ->create(['date' => '2024-06-15']);
        $selectedIds = $transactions->take(3)
            ->pluck('id')
            ->toArray();

        $result = $this->service->closeFiscalYearWithSelection(
            2024,
            $selectedIds,
            $this->user->id
        );

        expect($result->isClosed())
            ->toBeTrue()
            ->and($result->closed_by)
            ->toBe($this->user->id)
            ->and($result->transactions)
            ->toHaveCount(3);
    });

    it('throws exception when closing already closed fiscal year', function () {
        $fiscalYear = FiscalYear::factory()
            ->create([
                'year' => 2024,
                'closed_at' => now(),
            ]);
        $transaction = Transaction::factory()
            ->create();

        $this->service->closeFiscalYearWithSelection(
            2024,
            [$transaction->id],
            $this->user->id
        );
    })->throws(\Exception::class, 'already closed');

    it('throws exception when no transactions selected', function () {
        FiscalYear::factory()
            ->create(['year' => 2024]);

        $this->service->closeFiscalYearWithSelection(2024, [], $this->user->id);
    })->throws(\Exception::class, 'No transactions selected');

    it('throws exception when invalid transaction ids provided', function () {
        FiscalYear::factory()
            ->create(['year' => 2024]);
        $validTransaction = Transaction::factory()
            ->create(['date' => '2024-06-15']);

        $this->service->closeFiscalYearWithSelection(
            2024,
            [$validTransaction->id, 99999], // 99999 doesn't exist
            $this->user->id
        );
    })->throws(\Exception::class, 'invalid');

    it('prevents locking already locked transactions', function () {
        $fiscalYear = FiscalYear::factory()
            ->create(['year' => 2024]);
        $transaction = Transaction::factory()
            ->create(['date' => '2024-06-15']);

        // Lock it first
        $fiscalYear->transactions()
            ->attach($transaction->id, ['locked_at' => now()]);

        // Try to lock again
        $this->service->closeFiscalYearWithSelection(
            2024,
            [$transaction->id],
            $this->user->id
        );
    })->throws(\Exception::class);
});

describe('FiscalYearService - Close All', function () {
    it('can close fiscal year with all transactions', function () {
        $fiscalYear = FiscalYear::factory()
            ->create(['year' => 2024]);
        Transaction::factory()
            ->count(5)
            ->create(['date' => '2024-06-15']);

        $result = $this->service->closeFiscalYear(2024, $this->user->id);

        expect($result->isClosed())
            ->toBeTrue()
            ->and($result->transactions)
            ->toHaveCount(5);
    });

    it('throws exception when no transactions exist', function () {
        FiscalYear::factory()
            ->create(['year' => 2024]);

        $this->service->closeFiscalYear(2024, $this->user->id);
    })->throws(\Exception::class, 'No transactions found');
});

describe('FiscalYearService - Reopen', function () {
    it('can reopen closed fiscal year', function () {
        $fiscalYear = FiscalYear::factory()
            ->create([
                'year' => 2024,
                'closed_at' => now(),
                'closed_by' => $this->user->id,
            ]);
        $transaction = Transaction::factory()
            ->create();
        $fiscalYear->transactions()
            ->attach($transaction->id, ['locked_at' => now()]);

        $result = $this->service->reopenFiscalYear(2024);

        expect($result->isOpen())
            ->toBeTrue()
            ->and($result->closed_at)
            ->toBeNull()
            ->and($result->closed_by)
            ->toBeNull()
            ->and($result->transactions)
            ->toHaveCount(0);
    });

    it('throws exception when reopening open fiscal year', function () {
        FiscalYear::factory()
            ->create(['year' => 2024, 'closed_at' => null]);

        $this->service->reopenFiscalYear(2024);
    })->throws(\Exception::class, 'already open');
});

describe('FiscalYearService - Snapshot', function () {
    it('can get snapshot of closed fiscal year', function () {
        $fiscalYear = FiscalYear::factory()
            ->create([
                'year' => 2024,
                'closed_at' => now(),
                'closed_by' => $this->user->id,
                'opened_by' => $this->user->id,
            ]);

        $income = Transaction::factory()
            ->create([
                'date' => '2024-06-15',
                'type' => \App\Enums\TransactionType::Deposit->value,
                'amount_gross' => 10000, // 100€
            ]);
        $expense = Transaction::factory()
            ->create([
                'date' => '2024-06-20',
                'type' => \App\Enums\TransactionType::Withdrawal->value,
                'amount_gross' => 5000, // 50€
            ]);

        $fiscalYear->transactions()
            ->attach([
                $income->id => ['locked_at' => now()],
                $expense->id => ['locked_at' => now()],
            ]);

        $snapshot = $this->service->getSnapshot(2024);

        expect($snapshot)
            ->toHaveKeys(['fiscal_year', 'metadata', 'transactions', 'summary'])
            ->and($snapshot['metadata']['year'])
            ->toBe(2024)
            ->and($snapshot['metadata']['is_closed'])
            ->toBeTrue()
            ->and($snapshot['summary']['transaction_count'])
            ->toBe(2)
            ->and($snapshot['summary']['total_income'])
            ->toBe(10000)
            ->and($snapshot['summary']['total_expense'])
            ->toBe(5000)
            ->and($snapshot['summary']['balance'])
            ->toBe(5000);
    });

    it('snapshot includes locked_at timestamp', function () {
        $fiscalYear = FiscalYear::factory()
            ->create(['year' => 2024, 'closed_at' => now()]);
        $transaction = Transaction::factory()
            ->create();
        $lockedAt = now();

        $fiscalYear->transactions()
            ->attach($transaction->id, ['locked_at' => $lockedAt]);

        $snapshot = $this->service->getSnapshot(2024);

        expect($snapshot['transactions']->first()['locked_at'])->not->toBeNull();
    });
});
