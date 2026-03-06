<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Livewire\Forms\Accounting\TransactionForm;
use App\Models\Accounting\Account;
use App\Models\Accounting\Transaction;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

final class UpdateTransaction extends Action
{
    public static function handle(TransactionForm $form): Transaction
    {
        return DB::transaction(function () use ($form) {

            Transaction::where('id', $form->id)->update([
                'date' => $form->date,
                'label' => $form->label,
                'reference' => $form->reference,
                'description' => $form->description,
                'amount_gross' => Account::makeCentInteger($form->amount_gross),
                'vat' => $form->vat,
                'amount_net' => Account::makeCentInteger($form->amount_net),
                'account_id' => $form->account_id,
                'booking_account_id' => $form->booking_account_id,
                'type' => $form->type,
                'status' => $form->status,
            ]);

            return Transaction::find($form->id);

        });
    }
}
