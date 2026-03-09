<?php

declare(strict_types=1);

use App\Enums\TransactionType;
use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;
use App\Models\User;
use App\Services\Accounting\AnnualReportService;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->service = new AnnualReportService;

    $this->actingAs($this->user);
});

describe('AnnualReportService', function (): void {

    // =========================================================================
    // buildProjects
    // =========================================================================

    describe('buildProjects', function (): void {

        it('includes projects active in the given year', function (): void {
            Project::factory()->inYear(2024)->active()->create(['title' => 'Jugendclub']);

            $data = $this->service->build(2024);
            $projects = $data['snapshot']['projects'];

            expect($projects)->toHaveCount(1)
                ->and($projects[0]['title'])->toBe('Jugendclub');
        });

        it('excludes projects outside the given year', function (): void {
            Project::factory()->create([
                'start_date' => '2022-01-01',
                'end_date' => '2023-12-31',
            ]);

            $data = $this->service->build(2024);

            expect($data['snapshot']['projects'])->toHaveCount(0);
        });

        it('calculates income and expense from transactions', function (): void {
            $project = Project::factory()->inYear(2024)->create();

            $income = Transaction::factory()->create([
                'date' => '2024-06-01',
                'type' => TransactionType::Deposit,
                'amount_gross' => 5000_00,
            ]);
            $expense = Transaction::factory()->create([
                'date' => '2024-06-15',
                'type' => TransactionType::Withdrawal,
                'amount_gross' => 3000_00,
            ]);

            ProjectTransaction::factory()->create([
                'project_id' => $project->id,
                'transaction_id' => $income->id,
                'allocated_amount' => null,
            ]);
            ProjectTransaction::factory()->create([
                'project_id' => $project->id,
                'transaction_id' => $expense->id,
                'allocated_amount' => null,
            ]);

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['projects'][0];

            expect($snapshot['income'])->toBe(5000_00)
                ->and($snapshot['expense'])->toBe(3000_00)
                ->and($snapshot['balance'])->toBe(2000_00);
        });

        it('uses allocated_amount from pivot when set', function (): void {
            $project = Project::factory()->inYear(2024)->create();

            $tx = Transaction::factory()->create([
                'date' => '2024-06-01',
                'type' => TransactionType::Withdrawal,
                'amount_gross' => 10000_00,
            ]);

            ProjectTransaction::factory()->create([
                'project_id' => $project->id,
                'transaction_id' => $tx->id,
                'allocated_amount' => 4000_00, // nur Teilbetrag
            ]);

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['projects'][0];

            expect($snapshot['expense'])->toBe(4000_00); // allocated_amount, nicht amount_gross
        });

        it('calculates funding_allocated from project_fundings pivot', function (): void {
            $project = Project::factory()->inYear(2024)->create();
            $funding = Funding::factory()->inYear(2024)->create();

            $project->fundings()->attach($funding->id, ['allocated_amount' => 4000_00]);

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['projects'][0];

            expect($snapshot['funding_allocated'])->toBe(4000_00);
        });

        it('calculates coverage_rate correctly', function (): void {
            $project = Project::factory()->inYear(2024)->create();
            $funding = Funding::factory()->inYear(2024)->create();

            $expense = Transaction::factory()->create([
                'date' => '2024-06-01',
                'type' => TransactionType::Withdrawal,
                'amount_gross' => 8000_00,
            ]);

            $project->fundings()->attach($funding->id, ['allocated_amount' => 4000_00]);
            ProjectTransaction::factory()->create([
                'project_id' => $project->id,
                'transaction_id' => $expense->id,
                'allocated_amount' => null,
            ]);

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['projects'][0];

            expect($snapshot['coverage_rate'])->toBe(50.0); // 4000/8000 * 100
        });

        it('returns 0.0 coverage_rate when expense is 0', function (): void {
            Project::factory()->inYear(2024)->create();

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['projects'][0];

            expect($snapshot['coverage_rate'])->toBe(0.0);
        });

        it('snapshot project contains all expected keys', function (): void {
            Project::factory()->inYear(2024)->create();

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['projects'][0];

            expect($snapshot)->toHaveKeys([
                'id', 'title', 'status', 'start_date', 'end_date',
                'income', 'expense', 'balance', 'funding_allocated', 'coverage_rate',
            ]);
        });

        it('returns empty array when no projects exist for year', function (): void {
            $data = $this->service->build(2024);

            expect($data['snapshot']['projects'])->toBeArray()->toBeEmpty();
        });
    });

    // =========================================================================
    // buildFundings
    // =========================================================================

    describe('buildFundings', function (): void {

        it('includes fundings active in the given year', function (): void {
            Funding::factory()->inYear(2024)->create(['funder' => 'Stadt München']);

            $data = $this->service->build(2024);

            expect($data['snapshot']['fundings'])->toHaveCount(1)
                ->and($data['snapshot']['fundings'][0]['funder'])->toBe('Stadt München');
        });

        it('excludes fundings outside the given year', function (): void {
            Funding::factory()->create([
                'funding_period_start' => '2022-01-01',
                'funding_period_end' => '2023-12-31',
            ]);

            $data = $this->service->build(2024);

            expect($data['snapshot']['fundings'])->toHaveCount(0);
        });

        it('calculates received amount from funding transactions', function (): void {
            $funding = Funding::factory()->inYear(2024)->create(['approved_amount' => 10000_00]);
            $tx = Transaction::factory()->create([
                'date' => '2024-03-01',
                'type' => TransactionType::Deposit,
                'amount_gross' => 7000_00,
            ]);

            FundingTransaction::factory()->create([
                'funding_id' => $funding->id,
                'transaction_id' => $tx->id,
                'allocated_amount' => null,
            ]);

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['fundings'][0];

            expect($snapshot['received'])->toBe(7000_00)
                ->and($snapshot['approved_amount'])->toBe(10000_00);
        });

        it('calculates allocated_to_projects and remaining', function (): void {
            $funding = Funding::factory()->inYear(2024)->create(['approved_amount' => 10000_00]);
            $project1 = Project::factory()->inYear(2024)->create();
            $project2 = Project::factory()->inYear(2024)->create();

            $funding->projects()->attach($project1->id, ['allocated_amount' => 3000_00]);
            $funding->projects()->attach($project2->id, ['allocated_amount' => 2000_00]);

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['fundings'][0];

            expect($snapshot['allocated_to_projects'])->toBe(5000_00)
                ->and($snapshot['remaining'])->toBe(5000_00);
        });

        it('includes attached projects in funding snapshot', function (): void {
            $funding = Funding::factory()->inYear(2024)->create();
            $project = Project::factory()->inYear(2024)->create(['title' => 'Jugendclub']);

            $funding->projects()->attach($project->id, ['allocated_amount' => 2000_00]);

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['fundings'][0];

            expect($snapshot['projects'])->toHaveCount(1)
                ->and($snapshot['projects'][0]['title'])->toBe('Jugendclub')
                ->and($snapshot['projects'][0]['allocated_amount'])->toBe(2000_00);
        });

        it('snapshot funding contains all expected keys', function (): void {
            Funding::factory()->inYear(2024)->create();

            $data = $this->service->build(2024);
            $snapshot = $data['snapshot']['fundings'][0];

            expect($snapshot)->toHaveKeys([
                'id', 'title', 'funder', 'reference', 'status',
                'approved_amount', 'received', 'allocated_to_projects',
                'remaining', 'period_start', 'period_end', 'projects',
            ]);
        });

        it('returns empty array when no fundings exist for year', function (): void {
            $data = $this->service->build(2024);

            expect($data['snapshot']['fundings'])->toBeArray()->toBeEmpty();
        });
    });
});
