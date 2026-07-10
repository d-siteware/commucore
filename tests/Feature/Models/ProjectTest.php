<?php

declare(strict_types=1);

use App\Enums\ProjectStatus;
use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Project Model', function (): void {

    // =========================================================================
    // Scopes
    // =========================================================================

    describe('scopeActive', function (): void {
        it('returns only active projects', function (): void {
            Project::factory()->create(['status' => ProjectStatus::Active]);
            Project::factory()->create(['status' => ProjectStatus::Planned]);
            Project::factory()->create(['status' => ProjectStatus::Completed]);

            $results = Project::active()->get();

            expect($results)->toHaveCount(1)
                ->and($results->first()->status)->toBe(ProjectStatus::Active);
        });
    });

    describe('scopeInYear', function (): void {
        it('returns projects active within the given year', function (): void {
            // Voll innerhalb des Jahres
            Project::factory()->create([
                'start_date' => '2024-03-01',
                'end_date' => '2024-11-30',
            ]);
            // Überlappend: startet vorher, endet im Jahr
            Project::factory()->create([
                'start_date' => '2023-01-01',
                'end_date' => '2024-06-30',
            ]);
            // Überlappend: startet im Jahr, endet danach
            Project::factory()->create([
                'start_date' => '2024-09-01',
                'end_date' => '2025-03-31',
            ]);
            // Außerhalb: komplett vor dem Jahr
            Project::factory()->create([
                'start_date' => '2022-01-01',
                'end_date' => '2023-12-31',
            ]);
            // Außerhalb: komplett nach dem Jahr
            Project::factory()->create([
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
            ]);

            $results = Project::inYear(2024)->get();

            expect($results)->toHaveCount(3);
        });

        it('returns projects with no end_date as ongoing', function (): void {
            Project::factory()->create([
                'start_date' => '2024-01-01',
                'end_date' => null,
            ]);

            expect(Project::inYear(2024)->count())->toBe(1)
                ->and(Project::inYear(2025)->count())->toBe(1);
        });

        it('excludes projects with no start_date that ended before the year', function (): void {
            Project::factory()->create([
                'start_date' => null,
                'end_date' => '2023-12-31',
            ]);

            expect(Project::inYear(2024)->count())->toBe(0);
        });
    });

    // =========================================================================
    // Methods
    // =========================================================================

    describe('totalExpense', function (): void {
        it('sums full transaction amounts when no allocated_amount is set', function (): void {
            $project = Project::factory()->create();

            $tx1 = Transaction::factory()->create(['amount_gross' => 3000, 'type' => \App\Enums\TransactionType::Withdrawal]);
            $tx2 = Transaction::factory()->create(['amount_gross' => 2000, 'type' => \App\Enums\TransactionType::Withdrawal]);

            ProjectTransaction::factory()->create([
                'project_id' => $project->id,
                'transaction_id' => $tx1->id,
                'allocated_amount' => null,
            ]);
            ProjectTransaction::factory()->create([
                'project_id' => $project->id,
                'transaction_id' => $tx2->id,
                'allocated_amount' => null,
            ]);

            expect($project->totalExpense())->toBe(5000);
        });

        it('uses allocated_amount instead of full transaction when set', function (): void {
            $project = Project::factory()->create();
            $tx = Transaction::factory()->create(['amount_gross' => 10000, 'type' => \App\Enums\TransactionType::Withdrawal]);

            ProjectTransaction::factory()->create([
                'project_id' => $project->id,
                'transaction_id' => $tx->id,
                'allocated_amount' => 4000,
            ]);

            expect($project->totalExpense())->toBe(4000);
        });

        it('returns 0 for project with no transactions', function (): void {
            $project = Project::factory()->create();

            expect($project->totalExpense())->toBe(0);
        });
    });

    describe('totalFundingAllocated', function (): void {
        it('sums allocated_amount from project_fundings pivot', function (): void {
            $project = Project::factory()->create();
            $funding1 = Funding::factory()->create();
            $funding2 = Funding::factory()->create();

            $project->fundings()->attach($funding1->id, ['allocated_amount' => 5000_00]);
            $project->fundings()->attach($funding2->id, ['allocated_amount' => 3000_00]);

            expect($project->totalFundingAllocated())->toBe(8000_00);
        });

        it('returns 0 when no fundings are attached', function (): void {
            $project = Project::factory()->create();

            expect($project->totalFundingAllocated())->toBe(0);
        });
    });
});
