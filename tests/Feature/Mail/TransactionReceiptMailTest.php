<?php

declare(strict_types=1);

use App\Mail\TransactionReceiptMail;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('sends receipt mail with correct data', function (): void {
    Mail::fake();

    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create();

    Mail::to($member->email)->send(new TransactionReceiptMail(
        member: $member,
        filename: '/tmp/nonexistent.pdf',
        transaction: $transaction,
    ));

    Mail::assertSent(TransactionReceiptMail::class, function (TransactionReceiptMail $mail) use ($member, $transaction): bool {
        return $mail->member->id === $member->id
            && $mail->transaction->id === $transaction->id
            && $mail->filename === '/tmp/nonexistent.pdf'
            && $mail->envelope()->subject === __('transaction.mail.receipt.subject');
    });
});

it('has correct view', function (): void {
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create();

    $mail = new TransactionReceiptMail(
        member: $member,
        filename: '/tmp/nonexistent.pdf',
        transaction: $transaction,
    );

    expect($mail->content()->view)->toBe('emails.send-transaction-receipt-mail');
});

it('has no attachments when file does not exist', function (): void {
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create();

    $mail = new TransactionReceiptMail(
        member: $member,
        filename: '/tmp/nonexistent.pdf',
        transaction: $transaction,
    );

    expect($mail->attachments())->toBe([]);
});

it('has attachment when file exists', function (): void {
    $member = Member::factory()->create();
    $transaction = Transaction::factory()->create();
    $filename = tempnam(sys_get_temp_dir(), 'receipt_').'.pdf';
    touch($filename);

    $mail = new TransactionReceiptMail(
        member: $member,
        filename: $filename,
        transaction: $transaction,
    );

    expect($mail->attachments())->toHaveCount(1);

    unlink($filename);
});
