<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;
use Illuminate\Support\Facades\DB;

final class AppendEventTransaction
{
    public static function handle(Transaction $transaction, Event $event, ?string $name, ?string $gender): Transaction
    {
        return DB::transaction(function () use ($transaction, $event, $name, $gender): \App\Models\Accounting\Transaction {

            EventTransaction::create([
                'visitor_name' => $name,
                'gender' => $gender,
                'transaction_id' => $transaction->id,
                'event_id' => $event->id,
            ]);

            return $transaction;
        });

    }
}
