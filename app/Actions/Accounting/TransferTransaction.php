<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Enums\TransactionType;
use App\Livewire\Forms\Accounting\TransferTransactionForm;
use App\Models\Accounting\Account;
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
            \App\Models\Accounting\CancelTransaction::create([
                'transaction_id' => $transaction->id,
                'user_id' => $from->user_id,
                'reason' => $from->reason,
            ]);

            /**
             *   Add cancel transaction to nullify canceled one
             *   Invert VAT and amount net & gross
             */
            $amountGross = $transaction->amount_gross * -1;
            $amountNet = $transaction->amount_net * -1;
            $vatValue = $transaction->vat * -1;

            Transaction::create([
                'date' => Carbon::now('Europe/Berlin'),
                'label' => $transaction->label,
                'reference' => $transaction->reference,
                'description' => $transaction->description.' Storno: '.$from->reason,
                'amount_gross' => Account::makeCentInteger($amountGross),
                'vat' => $vatValue,
                'amount_net' => Account::makeCentInteger($amountNet),
                'account_id' => $transaction->account_id,
                'booking_account_id' => $transaction->booking_account_id,
                'type' => TransactionType::Reversal->value,
                'status' => $transaction->status,
            ]);

            $oldType = $transaction->type;

            //            $transaction->save();

            return Transaction::create([
                'date' => Carbon::now('Europe/Berlin'),
                'label' => $transaction->label,
                'reference' => $transaction->reference,
                'description' => $transaction->description.' Umbgebucht: '.$from->reason,
                'amount_gross' => Account::makeCentInteger($transaction->amount_gross),
                'vat' => $transaction->vat,
                'amount_net' => Account::makeCentInteger($transaction->amount_net),
                'account_id' => $from->account_id,
                'booking_account_id' => $transaction->booking_account_id,
                'type' => $oldType,
                'status' => $transaction->status,
            ]);
        });
    }
}
