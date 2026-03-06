<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Transaction\Boxoffice;

use App\Actions\Event\CreateBoxOfficeEntry;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Livewire\Forms\Accounting\TransactionForm;
use App\Livewire\Forms\Event\EventVisitorForm;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Event\Event;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Form extends Component
{
    use HasPrivileges;

    public TransactionForm $form;

    public Event $event;

    public Collection $visitorList;

    public $accountList;

    public $bookingAccountList;

    public EventVisitorForm $visitorForm;

    public int $ticketCounter;

    public function mount(Event $event): void
    {
        $this->event = $event;
        $this->accountList = Account::query()
            ->select('id', 'name')
            ->get();
        $this->bookingAccountList = BookingAccount::query()
            ->select('id', 'label', 'number')
            ->get();
        $this->init();
    }

    protected function init(): void
    {
        $this->ticketCounter = 0;
        $this->form->date = now()->format('Y-m-d');
        $this->form->amount_net = Account::makeCentInteger($this->event->entry_fee);
        $this->form->amount_gross = Account::formatedAmount($this->event->entry_fee);
        $this->form->type = TransactionType::Deposit->value;
        $this->form->status = TransactionStatus::submitted->value;
        $this->form->label = 'Einnahme Abendkasse '.$this->event->name;
        $this->form->reference = 'Besucher: ';
        $this->form->vat = 0;
        $this->form->tax = 0;
        $this->form->booking_account_id = 2;  // Change preselected for box office id;
    }

    public function addBoxOfficePayment(): void
    {

        if ($this->ticketCounter <= 0) {
            Flux::toast(
                text: 'Es muss wenigstens eine Karte berechnet werden!',
                variant: 'danger',
            );

            return;
        }

        $this->checkPrivilege(Event::class);

        $this->validate([
            'form.amount_gross' => ['required'],
            'form.account_id' => 'required',
        ], [
            'form.amount_gross.required' => 'Eintrittspreis angeben (0 für freien Eintritt)',
            'form.account_id.required' => 'Bitte ein Finanzkonto auswählen',
        ]);

        for ($i = 0; $i < $this->ticketCounter; $i++) {
            CreateBoxOfficeEntry::handle($this->form, $this->event);
        }

        Flux::toast(
            text: $this->ticketCounter.' Tickets der Abendkasse '.$this->event->name.' erfasst',
            variant: 'success',
        );

    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('livewire.accounting.transaction.boxoffice.form');
    }
}
