<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Livewire\Forms\Accounting\AccountForm;
use App\Models\Accounting\Account;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class UpdateAccount extends Action
{
    public static function handle(AccountForm $account): ?Account
    {
        try {
            DB::transaction(function () use ($account): void {
                Account::where('id', $account->id)
                    ->update([
                        'name' => $account->name,
                        'number' => $account->number,
                        'institute' => $account->institute,
                        'type' => $account->type,
                        'iban' => $account->iban,
                        'bic' => $account->bic,
                        'starting_amount' => Account::makeCentInteger($account->starting_amount),
                    ]);
            });

            return Account::find($account->id);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }
}
