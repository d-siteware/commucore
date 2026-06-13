<?php

declare(strict_types=1);

use App\Livewire\Accounting\Transaction\Booking\Form as BookingForm;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
});

test('a transaction can be booked with a booking account', function (): void {
    $bookingAccount = BookingAccount::factory()->create();
    $transaction = Transaction::factory()->create(['status' => 'submitted']);

    Livewire::test(BookingForm::class, ['transactionId' => $transaction->id])
        ->set('form.booking_account_id', $bookingAccount->id)
        ->set('form.status', 'booked')
        ->call('updateBookingStatus')
        ->assertHasNoErrors();

    $transaction->refresh();
    expect($transaction->booking_account_id)->toBe($bookingAccount->id)
        ->and($transaction->status->value)->toBe('booked');
});

test('booking updates the status on the transaction', function (): void {
    $bookingAccount = BookingAccount::factory()->create();
    $transaction = Transaction::factory()->create([
        'status' => 'submitted',
        'booking_account_id' => null,
    ]);

    Livewire::test(BookingForm::class, ['transactionId' => $transaction->id])
        ->set('form.booking_account_id', $bookingAccount->id)
        ->set('form.status', 'booked')
        ->call('updateBookingStatus')
        ->assertHasNoErrors();

    $transaction->refresh();
    expect($transaction->status->value)->toBe('booked');
});

test('booking requires a booking account', function (): void {
    $transaction = Transaction::factory()->create(['status' => 'submitted']);

    Livewire::test(BookingForm::class, ['transactionId' => $transaction->id])
        ->set('form.status', 'booked')
        ->call('updateBookingStatus')
        ->assertHasErrors(['form.booking_account_id']);
});

test('booking requires a valid booking account reference', function (): void {
    $transaction = Transaction::factory()->create(['status' => 'submitted']);

    Livewire::test(BookingForm::class, ['transactionId' => $transaction->id])
        ->set('form.booking_account_id', 999999)
        ->set('form.status', 'booked')
        ->call('updateBookingStatus')
        ->assertHasErrors(['form.booking_account_id']);
});
