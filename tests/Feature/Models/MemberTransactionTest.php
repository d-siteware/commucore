<?php

declare(strict_types=1);

use App\Enums\TransactionStatus;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use Carbon\Carbon;

describe('MemberTransaction model', function (): void {
    it('can be created with factory', function (): void {
        $memberTransaction = MemberTransaction::factory()->create();

        expect($memberTransaction)->toBeInstanceOf(MemberTransaction::class)
            ->and($memberTransaction->member)->toBeInstanceOf(Member::class)
            ->and($memberTransaction->transaction)->toBeInstanceOf(Transaction::class);
    });

    it('belongs to a member', function (): void {
        $member = Member::factory()->create();
        $memberTransaction = MemberTransaction::factory()->create([
            'member_id' => $member->id,
        ]);

        expect($memberTransaction->member)->toBeInstanceOf(Member::class)
            ->and($memberTransaction->member->id)->toBe($member->id);
    });

    it('belongs to a transaction', function (): void {
        $transaction = Transaction::factory()->create();
        $memberTransaction = MemberTransaction::factory()->create([
            'transaction_id' => $transaction->id,
        ]);

        expect($memberTransaction->transaction)->toBeInstanceOf(Transaction::class)
            ->and($memberTransaction->transaction->id)->toBe($transaction->id);
    });

    it('casts is_membership_fee as boolean', function (): void {
        $memberTransaction = MemberTransaction::factory()->create([
            'is_membership_fee' => 1,
        ]);

        expect($memberTransaction->is_membership_fee)->toBeTrue();
    });

    it('casts receipt_sent_timestamp as datetime', function (): void {
        $memberTransaction = MemberTransaction::factory()->create([
            'receipt_sent_timestamp' => now(),
        ]);

        expect($memberTransaction->receipt_sent_timestamp)->toBeInstanceOf(Carbon::class);
    });

    it('has booked scope for booked transactions', function (): void {
        $bookedTransaction = Transaction::factory()->create(['status' => TransactionStatus::booked]);
        $submittedTransaction = Transaction::factory()->create(['status' => TransactionStatus::submitted]);

        MemberTransaction::factory()->create(['transaction_id' => $bookedTransaction->id]);
        MemberTransaction::factory()->create(['transaction_id' => $submittedTransaction->id]);

        $booked = MemberTransaction::booked()->get();

        expect($booked)->toHaveCount(1);
    });

    it('has paid scope as alias for booked', function (): void {
        $bookedTransaction = Transaction::factory()->create(['status' => TransactionStatus::booked]);
        MemberTransaction::factory()->create(['transaction_id' => $bookedTransaction->id]);

        $paid = MemberTransaction::paid()->get();

        expect($paid)->toHaveCount(1);
    });

    it('has submitted scope for submitted transactions', function (): void {
        $bookedTransaction = Transaction::factory()->create(['status' => TransactionStatus::booked]);
        $submittedTransaction = Transaction::factory()->create(['status' => TransactionStatus::submitted]);

        MemberTransaction::factory()->create(['transaction_id' => $bookedTransaction->id]);
        MemberTransaction::factory()->create(['transaction_id' => $submittedTransaction->id]);

        $submitted = MemberTransaction::submitted()->get();

        expect($submitted)->toHaveCount(1);
    });

    it('has membershipFees scope', function (): void {
        MemberTransaction::factory()->create(['is_membership_fee' => false]);
        MemberTransaction::factory()->create(['is_membership_fee' => true]);

        $fees = MemberTransaction::membershipFees()->get();

        expect($fees)->toHaveCount(1);
    });

    it('has forYear scope', function (): void {
        MemberTransaction::factory()->create(['fee_year' => 2023]);
        MemberTransaction::factory()->create(['fee_year' => 2024]);

        $year2024 = MemberTransaction::forYear(2024)->get();

        expect($year2024)->toHaveCount(1);
    });

    it('combines membership fees and year scopes', function (): void {
        MemberTransaction::factory()->create(['is_membership_fee' => true, 'fee_year' => 2024]);
        MemberTransaction::factory()->create(['is_membership_fee' => true, 'fee_year' => 2023]);
        MemberTransaction::factory()->create(['is_membership_fee' => false, 'fee_year' => 2024]);

        $fees2024 = MemberTransaction::membershipFees()->forYear(2024)->get();

        expect($fees2024)->toHaveCount(1);
    });
});
