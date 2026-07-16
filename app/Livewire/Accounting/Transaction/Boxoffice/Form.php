<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Transaction\Boxoffice;

use App\Actions\Event\CreateBoxOfficeEntry;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Livewire\Forms\Accounting\TransactionForm;
use App\Livewire\Forms\Event\EventVisitorForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\BoxofficePreset;
use App\Models\Accounting\FiscalYear;
use App\Models\Event\Event;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Form extends Component
{
    use HandlesErrors;
    use HasPrivileges;

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

        $typeId = FiscalYear::getActive()?->booking_account_type_id;
        $this->bookingAccountList = BookingAccount::query()
            ->select('id', 'label', 'number')
            ->when($typeId !== null, fn ($q) => $q->where('booking_account_type_id', $typeId))
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
     * Vorauswahl des Buchungskontos über die BoxofficePresets
     * des aktiven Buchungstyps.
     * Fällt auf null zurück, wenn kein Preset existiert –
     * dann muss der Nutzer manuell wählen.
     */
    private function defaultBookingAccountId(): ?int
    {
        $typeId = FiscalYear::getActive()?->booking_account_type_id;

        if ($typeId === null) {
            return null;
        }

        $preset = BoxofficePreset::where('booking_account_type_id', $typeId)
            ->orderBy('priority')
            ->first();

        return $preset?->booking_account_id;
    }

    public function addBoxOfficePayment(): void
    {
        try {
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
        } catch (\Throwable $e) {
            $this->handleError('Abendkasse-Buchung fehlgeschlagen', $e);
        }
    }

    public function render(): Factory|View
    {
        return view('livewire.accounting.transaction.boxoffice.form');
    }
}
