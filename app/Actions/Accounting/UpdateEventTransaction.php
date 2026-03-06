<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UpdateEventTransaction extends Action
{
    protected bool $updateTransaction;

    protected bool $updateEventTransaction;

    /**
     * @throws Throwable
     */
    public function handle(Transaction $transaction, Event $event, $name, $gender): Transaction
    {
        return DB::transaction(function () use ($transaction, $event, $name, $gender): \App\Models\Accounting\Transaction {

            $this->updateTransaction = $transaction->update([
                'date' => $transaction->date,
                'label' => $transaction->label,
                'reference' => $transaction->reference,
                'description' => $transaction->description,
                'amount_gross' => Account::makeCentInteger($transaction->amount_gross),
                'vat' => $transaction->vat,
                'amount_net' => Account::makeCentInteger($transaction->amount_net),
                'account_id' => $transaction->account_id,
                'booking_account_id' => $transaction->booking_account_id,
                'type' => $transaction->type,
                'status' => $transaction->status,
            ]);

            $event_transaction = EventTransaction::query()->where('event_id', $event->id)->where('transaction_id', $transaction->id)->first();

            if ($event_transaction) {
                $this->updateEventTransaction = $event_transaction->update([
                    'visitor_name' => $name,
                    'gender' => $gender,
                    'transaction_id' => $transaction->id,
                    'event_id' => $event->id,
                ]);
            }

            return $transaction;

        });

    }
}
