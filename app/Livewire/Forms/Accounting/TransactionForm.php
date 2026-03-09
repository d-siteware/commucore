<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Actions\Accounting\CreateBooking;
use App\Actions\Accounting\CreateTransaction;
use App\Actions\Accounting\UpdateTransaction;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Transaction;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class TransactionForm extends Form
{
    public $id;

    public $label;

    public $date;

    public $reference;

    public $description;

    public $amount_net;

    public $vat;

    public $tax;

    public $amount_gross;

    public $account_id;

    public $receipt_id;

    public $booking_account_id;

    public $type;

    public $status = TransactionStatus::submitted->value;

    public function set(Transaction $transaction): void
    {
        $this->id = $transaction->id;
        $this->label = $transaction->label;
        $this->date = $transaction->date->format('Y-m-d');
        $this->amount_net = $transaction->netForHumans();
        $this->vat = ($transaction->vat);
        $this->tax = $transaction->taxForHumans();
        $this->amount_gross = $transaction->grossForHumans(false);
        $this->account_id = $transaction->account_id;
        $this->booking_account_id = $transaction->booking_account_id;
        $this->type = $transaction->type;
        $this->status = $transaction->status;
        $this->reference = $transaction->reference;
        $this->description = $transaction->description;
    }

    public function book(): Transaction
    {
        $this->validate([
            'booking_account_id' => 'required|exists:booking_accounts,id',
            'status' => Rule::enum(TransactionStatus::class),
        ]);

        return CreateBooking::handle([
            'id' => $this->id,
            'booking_account_id' => $this->booking_account_id,
            'status' => $this->status,
        ]);
    }

    public function create(): Transaction
    {
        $this->validate();

        return CreateTransaction::handle($this);
    }

    public function update(): Transaction
    {
        $this->validate();

        return UpdateTransaction::handle($this);
    }

    protected function rules(): array
    {
        return [
            'id' => ['nullable'],
            'label' => ['string', 'required_unless:id,null'],
            'amount_net' => ['required'],
            'date' => ['required', 'date'],
            'vat' => ['required', 'integer'],
            'tax' => ['nullable'],
            'amount_gross' => ['required'],
            'account_id' => ['required', 'integer'],
            'receipt_id' => ['nullable'],
            'reference' => ['nullable'],
            'description' => ['nullable'],
            'booking_account_id' => ['nullable', 'integer'],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'status' => ['required', Rule::enum(TransactionStatus::class)],
        ];
    }

    protected function messages(): array
    {
        return [
            'label.required' => 'Bitte eine Bezeichnung der Buchung eingeben.',
            'label.string' => 'Bitte eine Bezeichnung der Buchung eingeben.',
            'amount_net.required' => 'Der Nettopreis fehlt.',
            'vat.required' => 'Die % MWst Angabe fehlt',
            'amount_gross.required' => 'Der Bruttobetrag muss angegeben werden.',
            'account_id.required' => 'Bitte ein Zahlungskonto angeben',
            'account_id.integer' => 'Bitte ein Zahlungskonto angeben',
            'receipt_id.required' => 'Es wurde noch kein Beleg angefügt!',
            'type.required' => 'Der Typ der Buchung muss angegeben werden',
            'status.required' => 'Der Buchungsstatus muss angegeben werden',
        ];
    }
}
