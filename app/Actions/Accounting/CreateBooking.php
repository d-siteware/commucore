<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Models\Accounting\Transaction;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

final class CreateBooking extends Action
{
    public static function handle(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = Transaction::findOrFail($data['id']);
            $transaction->update([
                'booking_account_id' => $data['booking_account_id'],
                'status' => $data['status'],
            ]);

            return $transaction->refresh();
        });
        //       /
    }
}
