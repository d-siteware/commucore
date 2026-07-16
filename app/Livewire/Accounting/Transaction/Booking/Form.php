<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Transaction\Booking;

use App\Livewire\Forms\Accounting\TransactionForm;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class Form extends Component
{
    public ?Transaction $transaction = null;

    public TransactionForm $form;

    public Collection $bookingAccountList;

    protected $listeners = ['book-transaction' => 'loadTransaction'];

    public function loadTransaction(int $transactionId): void
    {

        $this->transaction = Transaction::find($transactionId);
        $this->form->set($this->transaction);
    }

    public function mount(?int $transactionId = null): void
    {
        $this->transaction = Transaction::find($transactionId);
        $this->form->set($this->transaction);

        $typeId = FiscalYear::getActive()?->booking_account_type_id;
        $this->bookingAccountList = BookingAccount::select('id', 'label', 'number')
            ->when($typeId !== null, fn ($q) => $q->where('booking_account_type_id', $typeId))
            ->get();
    }

    public function updateBookingStatus(): void
    {
        $booking = $this->form->book();
        $this->dispatch('transaction-updated');

        Flux::toast(
            text: __('transaction.booking-update-success.text'),
            heading: __('transaction.booking-update-success.heading'),
            variant: 'success',
        );

        Flux::modal('book-transaction')
            ->close();
    }

    public function render(): View
    {
        return view('livewire.accounting.transaction.booking.form');
    }
}
