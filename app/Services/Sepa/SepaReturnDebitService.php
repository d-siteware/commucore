<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\Enums\TransactionStatus;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Membership\SepaMandate;
use App\Notifications\SepaReturnDebitNotification;
use Illuminate\Support\Facades\DB;

final class SepaReturnDebitService
{
    public function handleReturn(
        Transaction $transaction,
        Member $member,
        string $returnReason,
        ?string $returnReference = null,
    ): void {
        DB::transaction(function () use ($transaction, $member, $returnReason, $returnReference) {
            $originalDescription = $transaction->description;
            $returnInfo = 'Rücklastschrift: '.$returnReason;
            if ($returnReference) {
                $returnInfo .= ' (Ref: '.$returnReference.')';
            }

            $transaction->update([
                'status' => TransactionStatus::returned,
                'description' => $originalDescription
                    ? $originalDescription."\n".$returnInfo
                    : $returnInfo,
            ]);

            $member->notify(new SepaReturnDebitNotification(
                member: $member,
                transaction: $transaction,
                reason: $returnReason,
            ));
        });
    }

    public function recollect(
        Transaction $returnedTransaction,
        Member $member,
        SepaMandate $mandate,
    ): Transaction {
        return DB::transaction(function () use ($returnedTransaction, $member) {
            $newTransaction = Transaction::create([
                'date' => now(),
                'label' => 'Wiedereinzug: '.$returnedTransaction->label,
                'reference' => 'RECOLLECT-'.$returnedTransaction->id,
                'description' => 'Wiedereinzug nach Rücklastschrift (Original: '.$returnedTransaction->id.'): '.$returnedTransaction->description,
                'amount_gross' => $returnedTransaction->amount_gross,
                'vat' => $returnedTransaction->vat,
                'amount_net' => $returnedTransaction->amount_net,
                'account_id' => $returnedTransaction->account_id,
                'booking_account_id' => $returnedTransaction->booking_account_id,
                'type' => $returnedTransaction->type,
                'status' => TransactionStatus::submitted,
                'area' => $returnedTransaction->area,
            ]);

            MemberTransaction::create([
                'member_id' => $member->id,
                'transaction_id' => $newTransaction->id,
                'date' => now(),
                'is_membership_fee' => true,
                'fee_year' => now()->year,
            ]);

            return $newTransaction;
        });
    }

    public function getRecentReturns(int $limit = 20): array
    {
        return Transaction::query()
            ->where('status', TransactionStatus::returned)
            ->whereHas('member_transaction.member')
            ->with(['member_transaction.member', 'member_transaction' => function ($q) {
                $q->where('is_membership_fee', true);
            }])
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(function (Transaction $t) {
                $memberTx = $t->member_transaction;

                return [
                    'transaction' => $t,
                    'member' => $memberTx?->member,
                    'amount' => $t->amount_net,
                    'reason' => $t->description,
                    'returned_at' => $t->updated_at,
                    'can_recollect' => $memberTx?->member?->sepaMandates()
                        ->where('status', \App\Enums\SepaMandateStatus::Active)
                        ->whereNull('payment_completed_at')
                        ->exists() ?? false,
                ];
            })
            ->all();
    }
}
