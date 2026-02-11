<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use Illuminate\Support\Facades\DB;

final class AppendMemberTransaction
{
    public static function handle(Transaction $transaction, Member $member,?bool $is_membership_fee, int
    $fee_year): bool
    {
        $fee_year = $fee_year ?? now()->year;

        DB::transaction(function () use ($transaction, $member, $fee_year, $is_membership_fee) {
            MemberTransaction::create([
                'transaction_id' => $transaction->id,
                'member_id' => $member->id,
                'is_membership_fee' => $is_membership_fee,
                'fee_year' => $fee_year,
            ]);

            return true;
        });

        return false;
    }
}
