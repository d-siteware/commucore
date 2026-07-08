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
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Form extends Component
{
    use HasPrivileges;

    /**
     * SKR42-Konten für Abendkassen-Einnahmen, in Prioritätsreihenfolge:
     *   51500 = Eintrittsgelder kulturelle Veranstaltungen (Zweckbetrieb)
     *   51000 = Eintrittsgelder aus sportlichen Veranstaltungen (Zweckbetrieb)
     *   51900 = Sonstige Einnahmen Zweckbetriebe
     */
    private const BOX_OFFICE_ACCOUNT_NUMBERS = ['51500', '51000', '51900'];

    public TransactionForm $form;

    public Event $event;

    public Collection $visitorList;

    public Collection $accountList;

    public Collection $bookingAccountList;

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
        $this->form->amount_net = Account::formatedAmount($this->event->entry_fee);
        $this->form->amount_gross = Account::formatedAmount($this->event->entry_fee);
        $this->form->type = TransactionType::Deposit;
        $this->form->status = TransactionStatus::submitted;
        $this->form->label = 'Einnahme Abendkasse '.$this->event->name;
        $this->form->reference = 'Besucher: ';
        $this->form->vat = 0;
        $this->form->tax = '0';
        $this->form->booking_account_id = $this->defaultBookingAccountId();
    }

    /**
     * Vorauswahl des Buchungskontos über die SKR42-Kontonummer
     * (nicht über die DB-ID, die von der Seeder-Reihenfolge abhängt).
     * Fällt auf null zurück, wenn keines der Konten existiert –
     * dann muss der Nutzer manuell wählen.
     */
    private function defaultBookingAccountId(): ?int
    {
        $accounts = BookingAccount::query()
            ->whereIn('number', self::BOX_OFFICE_ACCOUNT_NUMBERS)
            ->pluck('id', 'number');

        foreach (self::BOX_OFFICE_ACCOUNT_NUMBERS as $number) {
            if ($accounts->has($number)) {
                return (int) $accounts->get($number);
            }
        }

        return null;
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
            'form.amount_gross.required' => __('transaction.validation.boxoffice.amount_gross.required'),
            'form.account_id.required' => __('transaction.validation.boxoffice.account_id.required'),
        ]);

        for ($i = 0; $i < $this->ticketCounter; $i++) {
            CreateBoxOfficeEntry::handle($this->form, $this->event);
        }

        Flux::toast(
            text: $this->ticketCounter.' Tickets der Abendkasse '.$this->event->name.' erfasst',
            variant: 'success',
        );

    }

    public function render(): Factory|View
    {
        return view('livewire.accounting.transaction.boxoffice.form');
    }
}
