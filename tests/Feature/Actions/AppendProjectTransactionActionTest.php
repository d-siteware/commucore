<?php

declare(strict_types=1);

use App\Actions\Accounting\AppendProjectTransaction;
use App\Models\Accounting\Transaction;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('appends a project transaction link', function (): void {
    $transaction = Transaction::factory()->create();
    $project = Project::factory()->create();

    $result = AppendProjectTransaction::handle($transaction, $project);

    expect(ProjectTransaction::where('transaction_id', $transaction->id)
        ->where('project_id', $project->id)
        ->exists()
    )->toBeTrue()
        ->and($result->id)->toBe($transaction->id);
});

it('stores allocated amount when provided', function (): void {
    $transaction = Transaction::factory()->create();
    $project = Project::factory()->create();

    AppendProjectTransaction::handle($transaction, $project, allocatedAmount: 2500);

    $pivot = ProjectTransaction::where('transaction_id', $transaction->id)
        ->where('project_id', $project->id)
        ->first();

    expect($pivot)->not->toBeNull()
        ->and($pivot->allocated_amount)->toBe(2500);
});
