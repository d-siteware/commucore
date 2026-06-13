<?php

declare(strict_types=1);

use App\Models\Accounting\Transaction;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;

describe('ProjectTransaction model', function (): void {
    it('can be created', function (): void {
        $project = Project::factory()->create();
        $transaction = Transaction::factory()->create();

        $pt = ProjectTransaction::create([
            'project_id' => $project->id,
            'transaction_id' => $transaction->id,
            'allocated_amount' => 5000,
        ]);

        expect($pt)->toBeInstanceOf(ProjectTransaction::class)
            ->and($pt->allocated_amount)->toBe(5000);
    });

    it('belongs to a project', function (): void {
        $project = Project::factory()->create();
        $transaction = Transaction::factory()->create();

        $pt = ProjectTransaction::create([
            'project_id' => $project->id,
            'transaction_id' => $transaction->id,
        ]);

        expect($pt->project)->toBeInstanceOf(Project::class)
            ->and($pt->project->id)->toBe($project->id);
    });

    it('belongs to a transaction', function (): void {
        $project = Project::factory()->create();
        $transaction = Transaction::factory()->create();

        $pt = ProjectTransaction::create([
            'project_id' => $project->id,
            'transaction_id' => $transaction->id,
        ]);

        expect($pt->transaction)->toBeInstanceOf(Transaction::class)
            ->and($pt->transaction->id)->toBe($transaction->id);
    });

    it('casts allocated_amount as integer', function (): void {
        $project = Project::factory()->create();
        $transaction = Transaction::factory()->create();

        $pt = ProjectTransaction::create([
            'project_id' => $project->id,
            'transaction_id' => $transaction->id,
            'allocated_amount' => 5000,
        ]);

        expect($pt->allocated_amount)->toBeInt();
    });

    it('effectiveAmount returns allocated_amount when set', function (): void {
        $project = Project::factory()->create();
        $transaction = Transaction::factory()->create(['amount_gross' => 10000]);

        $pt = ProjectTransaction::create([
            'project_id' => $project->id,
            'transaction_id' => $transaction->id,
            'allocated_amount' => 5000,
        ]);

        expect($pt->effectiveAmount())->toBe(5000);
    });

    it('effectiveAmount returns transaction amount when not allocated', function (): void {
        $project = Project::factory()->create();
        $transaction = Transaction::factory()->create(['amount_gross' => 10000]);

        $pt = ProjectTransaction::create([
            'project_id' => $project->id,
            'transaction_id' => $transaction->id,
            'allocated_amount' => null,
        ]);

        expect($pt->effectiveAmount())->toBe(10000);
    });
});
