<?php

declare(strict_types=1);

use App\Actions\Accounting\AppendMemberTransaction;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('appends a member transaction link and returns true', function (): void {
    $transaction = Transaction::factory()->create();
    $member = Member::factory()->create();

    $result = AppendMemberTransaction::handle($transaction, $member, is_membership_fee: true, fee_year: 2026);

    expect($result)->toBeTrue();

    expect(MemberTransaction::where('transaction_id', $transaction->id)
        ->where('member_id', $member->id)
        ->exists()
    )->toBeTrue();
});

it('stores membership fee flag and year', function (): void {
    $transaction = Transaction::factory()->create();
    $member = Member::factory()->create();

    AppendMemberTransaction::handle($transaction, $member, is_membership_fee: true, fee_year: 2025);

    $pivot = MemberTransaction::where('transaction_id', $transaction->id)->first();

    expect($pivot)->not->toBeNull()
        ->and($pivot->is_membership_fee)->toBeTrue()
        ->and($pivot->fee_year)->toBe(2025);
});

it('stores non-membership fee link', function (): void {
    $transaction = Transaction::factory()->create();
    $member = Member::factory()->create();

    AppendMemberTransaction::handle($transaction, $member, is_membership_fee: false, fee_year: 2026);

    $pivot = MemberTransaction::where('transaction_id', $transaction->id)->first();

    expect($pivot)->not->toBeNull()
        ->and($pivot->is_membership_fee)->toBeFalse();
});
