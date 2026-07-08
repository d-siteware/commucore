<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Models\Accounting\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class CancelTransaction
{
    /**
     * Storniert eine Transaktion über eine Gegenbuchung.
     *
     * Die Originaltransaktion bleibt unverändert (Typ, Beträge, Status).
     * Die Gegenbuchung übernimmt den Typ des Originals mit negierten
     * Beträgen, sodass sich Salden UND typ-basierte Auswertungen
     * (Einnahmen = Summe Deposits usw.) exakt neutralisieren.
     * Der Audit-Eintrag in cancel_transactions verknüpft Original und
     * Gegenbuchung (reversal_transaction_id).
     *
     * @param  array  $data  [ int $user_id, string $reason ]
     *
     * @throws Throwable
     */
    public static function handle(Transaction $transaction, array $data): Transaction
    {

        return DB::transaction(function () use ($transaction, $data) {

            $alreadyInvolved = \App\Models\Accounting\CancelTransaction::query()
                ->where('transaction_id', $transaction->id)
                ->orWhere('reversal_transaction_id', $transaction->id)
                ->exists();

            if ($alreadyInvolved) {
                throw new RuntimeException('Transaction was already cancelled or is a cancellation booking itself.');
            }

            $audit = \App\Models\Accounting\CancelTransaction::create([
                'transaction_id' => $transaction->id,
                'user_id' => $data['user_id'],
                'reason' => $data['reason'],
                'status' => $transaction->status->value,
            ]);

            $storno = Transaction::create([
                'date' => Carbon::now('Europe/Berlin'),
                'label' => 'STORNO-'.$transaction->label,
                'reference' => $transaction->reference,
                'description' => $transaction->description.'STORNO -Grund: '.$data['reason'],
                'amount_gross' => $transaction->amount_gross * -1,
                'vat' => $transaction->vat * -1,
                'amount_net' => $transaction->amount_net * -1,
                'account_id' => $transaction->account_id,
                'booking_account_id' => $transaction->booking_account_id,
                'type' => $transaction->type,
                'status' => $transaction->status,
            ]);

            $audit->update(['reversal_transaction_id' => $storno->id]);

            return $storno;

        });

    }
}
