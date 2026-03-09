<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;
use Illuminate\Support\Facades\DB;

final class AppendFundingTransaction
{
    public static function handle(Transaction $transaction, Funding $funding, ?int $allocatedAmount = null): Transaction
    {
        return DB::transaction(function () use ($transaction, $funding, $allocatedAmount): Transaction {

            FundingTransaction::create([
                'transaction_id' => $transaction->id,
                'funding_id' => $funding->id,
                'allocated_amount' => $allocatedAmount,
            ]);

            return $transaction;
        });
    }
}
