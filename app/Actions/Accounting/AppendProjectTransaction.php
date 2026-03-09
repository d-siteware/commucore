<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Models\Accounting\Transaction;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;
use Illuminate\Support\Facades\DB;

final class AppendProjectTransaction
{
    public static function handle(Transaction $transaction, Project $project, ?int $allocatedAmount = null): Transaction
    {
        return DB::transaction(function () use ($transaction, $project, $allocatedAmount): Transaction {

            ProjectTransaction::create([
                'transaction_id' => $transaction->id,
                'project_id' => $project->id,
                'allocated_amount' => $allocatedAmount,
            ]);

            return $transaction;
        });
    }
}
