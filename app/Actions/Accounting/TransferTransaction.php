<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Livewire\Forms\Accounting\TransferTransactionForm;
use App\Models\Accounting\CancelTransaction;
use App\Models\Accounting\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class TransferTransaction
{
    public static function handle(Transaction $transaction, TransferTransactionForm $from): Transaction
    {
        return DB::transaction(function () use ($transaction, $from) {
            /**
             *  Track cancelation of transaction
             */
            CancelTransaction::create([
                'transaction_id' => $transaction->id,
                'user_id' => $from->user_id,
                'reason' => $from->reason,
            ]);

            /**
             *   Add cancel transaction to nullify canceled one:
             *   same type as the original with inverted VAT and amount net & gross,
             *   so account balances AND type-based reports neutralize exactly.
             */
            Transaction::create([
                'date' => Carbon::now('Europe/Berlin'),
                'label' => $transaction->label,
                'reference' => $transaction->reference,
                'description' => $transaction->description.' Storno: '.$from->reason,
                'amount_gross' => $transaction->amount_gross * -1,
                'vat' => $transaction->vat * -1,
                'amount_net' => $transaction->amount_net * -1,
                'account_id' => $transaction->account_id,
                'booking_account_id' => $transaction->booking_account_id,
                'type' => $transaction->type,
                'status' => $transaction->status,
            ]);

            return Transaction::create([
                'date' => Carbon::now('Europe/Berlin'),
                'label' => $transaction->label,
                'reference' => $transaction->reference,
                'description' => $transaction->description.' Umbgebucht: '.$from->reason,
                'amount_gross' => $transaction->amount_gross,
                'vat' => $transaction->vat,
                'amount_net' => $transaction->amount_net,
                'account_id' => $from->account_id,
                'booking_account_id' => $transaction->booking_account_id,
                'type' => $transaction->type,
                'status' => $transaction->status,
            ]);
        });
    }
}
