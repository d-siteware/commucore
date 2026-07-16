<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Event\Show;

use App\Enums\TransactionType;
use App\Helpers\MoneyHelper;
use App\Livewire\Forms\Accounting\TransactionForm;
use App\Livewire\Forms\Event\EventForm;
use App\Livewire\Traits\HandlesErrors;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\FiscalYear;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class EventPayment extends Component
{
    use HandlesErrors;
    public EventForm $eventForm;

    public TransactionForm $transactionForm;

    public $member_id = 'extern';

    public bool $setEntryFee = false;

    #[Computed]
    public function members(): Collection
    {
        return Member::select('id', 'name', 'first_name')
            ->where('left_at', null)
            ->get();
    }

    #[Computed]
    public function accounts(): Collection
    {
        return Account::select('id', 'name')->get();
    }

    #[Computed]
    public function booking_accounts(): Collection
    {
        $typeId = FiscalYear::contextFiscalYear()?->booking_account_type_id;

        return BookingAccount::select('id', 'label', 'number')
            ->when($typeId !== null, fn ($q) => $q->where('booking_account_type_id', $typeId))
            ->get();
    }

    public function mount(Event $event): void
    {
        $this->eventForm->setEvent($event);
        $this->transactionForm->type = TransactionType::Deposit;
        $this->transactionForm->amount_gross = MoneyHelper::formatCents((int) ($this->eventForm->entry_fee * 100), withSymbol: false);
        $this->transactionForm->label = 'Zahlung Abendkasse';
        $this->transactionForm->date = $this->eventForm->event_date;
    }

    public function updatedSetEntryFee(): void
    {
        $this->transactionForm->amount_gross = $this->setEntryFee
            ? MoneyHelper::formatCents((int) ($this->eventForm->entry_fee_discounted * 100), withSymbol: false)
            : MoneyHelper::formatCents((int) ($this->eventForm->entry_fee * 100), withSymbol: false);
    }

    public function storePayment(): void
    {
        // TODO correct CreateEventTransaction or this form
        $this->dispatch('updated-payments');

    }

    public function addEventPayment(): void
    {
        try {
            $this->storePayment();
        } catch (\Throwable $e) {
            $this->handleError('Zahlung speichern fehlgeschlagen', $e);
        }
    }

    public function render(): View
    {
        return view('livewire.event.show.event-payment-form');
    }
}
