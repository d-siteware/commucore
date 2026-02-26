<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Enums\TransactionType;
use App\Models\Accounting\Account;
use App\Models\Accounting\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CancelTransaction
{
    /**
     * @param  array  $data  [ int $user_id, string $reason ]
     *
     * @throws Throwable
     */
    public static function handle(Transaction $transaction, array $data): Transaction
    {

        return DB::transaction(function () use ($transaction, $data) {

            $ct = \App\Models\Accounting\CancelTransaction::create([
                'transaction_id' => $transaction->id,
                'user_id' => $data['user_id'],
                'reason' => $data['reason'],
            ]);

            $oldType = $transaction->type;

            $transaction->type = TransactionType::Reversal;
            $transaction->save();

            return Transaction::create([
                'date' => Carbon::now('Europe/Berlin'),
                'label' => 'STORNO-'.$transaction->label,
                'reference' => $transaction->reference,
                'description' => $transaction->description.'STORNO -Grund: '.$data['reason'],
                'amount_gross' => Account::makeCentInteger($transaction->amount_gross) * -1,
                'vat' => $transaction->vat * -1,
                'tax' => Account::makeCentInteger($transaction->tax * -1),
                'amount_net' => Account::makeCentInteger($transaction->amount_net * -1),
                'account_id' => $transaction->account_id,
                'booking_account_id' => $transaction->booking_account_id,
                'type' => $oldType,
                'status' => $transaction->status,
            ]);

        });

    }
}
