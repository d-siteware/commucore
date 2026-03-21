<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Event\Show;

use App\Enums\TransactionType;
use App\Livewire\Forms\Accounting\TransactionForm;
use App\Livewire\Forms\Event\EventForm;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class EventPayment extends Component
{
    public EventForm $eventForm;

    public TransactionForm $transactionForm;

    public $member_id = 'extern';

    public bool $setEntryFee = false;

    #[Computed]
    public function members(): \Illuminate\Database\Eloquent\Collection
    {
        return Member::select('id', 'name', 'first_name')
            ->where('left_at', null)
            ->get();
    }

    #[Computed]
    public function accounts(): \Illuminate\Database\Eloquent\Collection
    {
        return Account::select('id', 'name')->get();
    }

    #[Computed]
    public function booking_accounts(): \Illuminate\Database\Eloquent\Collection
    {
        return BookingAccount::select('id', 'label', 'number')->get();
    }

    public function mount(Event $event): void
    {
        $this->eventForm->setEvent($event);
        $this->transactionForm->type = TransactionType::Deposit;
        $this->transactionForm->amount_gross = number_format($this->eventForm->entry_fee, 2, ',', '.');
        $this->transactionForm->label = 'Zahlung Abendkasse';
        $this->transactionForm->date = $this->eventForm->event_date;
    }

    public function updatedSetEntryFee(): void
    {
        $this->transactionForm->amount_gross = $this->setEntryFee
            ? number_format($this->eventForm->entry_fee_discounted, 2, ',', '.')
            : number_format($this->eventForm->entry_fee, 2, ',', '.');
    }

    public function storePayment(): void
    {
        // TODO correct CreateEventTransaction or this form
        $this->dispatch('updated-payments');

    }

    public function addEventPayment(): void
    {
        $this->storePayment();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.event.show.event-payment-form');
    }
}
