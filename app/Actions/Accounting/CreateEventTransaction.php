<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Livewire\Forms\Accounting\TransactionForm;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;
use Illuminate\Support\Facades\DB;

final class CreateEventTransaction
{
    public static function handle(TransactionForm $form, Event $event): Transaction
    {
        return DB::transaction(function () use ($form, $event): \App\Models\Accounting\Transaction {
            $transaction = CreateTransaction::handle($form);

            EventTransaction::create([
                'transaction_id' => $transaction->id,
                'event_id' => $event->id,
            ]);

            return $transaction;
        });
    }
}
