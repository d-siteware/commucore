<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Actions\Accounting\CreateTransaction;
use App\Livewire\Forms\Accounting\TransactionForm;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;
use App\Models\Event\EventVisitor;
use Illuminate\Support\Facades\DB;

final class CreateBoxOfficeEntry
{
    public static function handle(TransactionForm $form, Event $event): EventVisitor
    {
        return DB::transaction(function () use ($form, $event) {
            $transaction = CreateTransaction::handle($form);

            EventTransaction::create([
                'transaction_id' => $transaction->id,
                'event_id' => $event->id,
            ]);

            return EventVisitor::create([
                'name' => 'Karte Abendkasse',
                'event_id' => $event->id,
                'transaction_id' => $transaction->id,
            ]);
        });
    }
}
