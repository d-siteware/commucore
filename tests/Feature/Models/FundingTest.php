<?php

declare(strict_types=1);

use App\Enums\FundingStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;
use App\Models\Project\Project;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Funding Model', function (): void {

    // =========================================================================
    // Scopes
    // =========================================================================

    describe('scopeActive', function (): void {
        it('returns only active fundings', function (): void {
            Funding::factory()->create(['status' => FundingStatus::Active]);
            Funding::factory()->create(['status' => FundingStatus::Applied]);
            Funding::factory()->create(['status' => FundingStatus::Completed]);

            $results = Funding::active()->get();

            expect($results)->toHaveCount(1)
                ->and($results->first()->status)->toBe(FundingStatus::Active);
        });
    });

    describe('scopeInYear', function (): void {
        it('returns fundings active within the given year', function (): void {
            Funding::factory()->create([
                'funding_period_start' => '2024-01-01',
                'funding_period_end'   => '2024-12-31',
            ]);
            Funding::factory()->create([
                'funding_period_start' => '2023-06-01',
                'funding_period_end'   => '2024-06-30',
            ]);
            // Außerhalb
            Funding::factory()->create([
                'funding_period_start' => '2022-01-01',
                'funding_period_end'   => '2023-12-31',
            ]);

            expect(Funding::inYear(2024)->count())->toBe(2);
        });

        it('includes open-ended fundings (no end date)', function (): void {
            Funding::factory()->create([
                'funding_period_start' => '2024-01-01',
                'funding_period_end'   => null,
            ]);

            expect(Funding::inYear(2024)->count())->toBe(1)
                ->and(Funding::inYear(2026)->count())->toBe(1);
        });
    });

    // =========================================================================
    // Methods
    // =========================================================================

    describe('totalReceived', function (): void {
        it('sums full transaction amounts when no allocated_amount', function (): void {
            $funding = Funding::factory()->create();

            $tx1 = Transaction::factory()->create([
                'type'         => TransactionType::Deposit,
                'amount_gross' => 5000_00,
            ]);
            $tx2 = Transaction::factory()->create([
                'type'         => TransactionType::Deposit,
                'amount_gross' => 3000_00,
            ]);

            FundingTransaction::factory()->create([
                'funding_id'       => $funding->id,
                'transaction_id'   => $tx1->id,
                'allocated_amount' => null,
            ]);
            FundingTransaction::factory()->create([
                'funding_id'       => $funding->id,
                'transaction_id'   => $tx2->id,
                'allocated_amount' => null,
            ]);

            expect($funding->totalReceived())->toBe(8000_00);
        });

        it('uses allocated_amount when set', function (): void {
            $funding = Funding::factory()->create();
            $tx      = Transaction::factory()->create([
                'type'         => TransactionType::Deposit,
                'amount_gross' => 10000_00,
            ]);

            FundingTransaction::factory()->create([
                'funding_id'       => $funding->id,
                'transaction_id'   => $tx->id,
                'allocated_amount' => 4000_00,
            ]);

            expect($funding->totalReceived())->toBe(4000_00);
        });

        it('returns 0 when no transactions are attached', function (): void {
            $funding = Funding::factory()->create();

            expect($funding->totalReceived())->toBe(0);
        });
    });

    describe('remainingAmount', function (): void {
        it('returns approved_amount minus project allocations', function (): void {
            $funding  = Funding::factory()->create(['approved_amount' => 10000_00]);
            $project1 = Project::factory()->create();
            $project2 = Project::factory()->create();

            $funding->projects()->attach($project1->id, ['allocated_amount' => 3000_00]);
            $funding->projects()->attach($project2->id, ['allocated_amount' => 2000_00]);

            expect($funding->remainingAmount())->toBe(5000_00);
        });

        it('returns full approved_amount when no projects attached', function (): void {
            $funding = Funding::factory()->create(['approved_amount' => 5000_00]);

            expect($funding->remainingAmount())->toBe(5000_00);
        });

        it('returns 0 when approved_amount is null', function (): void {
            $funding = Funding::factory()->create(['approved_amount' => null]);

            expect($funding->remainingAmount())->toBe(0);
        });
    });

    describe('usageReport', function (): void {
        it('returns correct usage report structure', function (): void {
            $funding = Funding::factory()->create(['approved_amount' => 10000_00]);
            $project = Project::factory()->create();
            $tx      = Transaction::factory()->create([
                'type'         => TransactionType::Deposit,
                'amount_gross' => 6000_00,
            ]);

            $funding->projects()->attach($project->id, ['allocated_amount' => 7000_00]);
            FundingTransaction::factory()->create([
                'funding_id'       => $funding->id,
                'transaction_id'   => $tx->id,
                'allocated_amount' => null,
            ]);

            $report = $funding->usageReport();

            expect($report)
                ->toHaveKeys(['approved', 'allocated_to_projects', 'received', 'remaining'])
                ->and($report['approved'])->toBe(10000_00)
                ->and($report['allocated_to_projects'])->toBe(7000_00)
                ->and($report['received'])->toBe(6000_00)
                ->and($report['remaining'])->toBe(3000_00); // 10000 - 7000
        });

        it('handles fully used funding', function (): void {
            $funding = Funding::factory()->create(['approved_amount' => 5000_00]);
            $project = Project::factory()->create();

            $funding->projects()->attach($project->id, ['allocated_amount' => 5000_00]);

            $report = $funding->usageReport();

            expect($report['remaining'])->toBe(0);
        });
    });
});