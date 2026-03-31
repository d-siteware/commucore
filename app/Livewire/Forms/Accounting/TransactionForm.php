<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Actions\Accounting\CreateBooking;
use App\Actions\Accounting\CreateTransaction;
use App\Actions\Accounting\UpdateTransaction;
use App\Enums\BookingAccountArea;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Transaction;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class TransactionForm extends Form
{
    public ?int $id = null;

    public string $label = '';

    public string $date = '';

    public ?string $reference = null;

    public ?string $description = null;

    public string $amount_net = '';   // bleibt string – kommt als formatierter Wert rein

    public int $vat = 0;

    public string $tax = '';          // nur Anzeige, nicht in DB

    public string $amount_gross = ''; // bleibt string – Account::makeCentInteger() erwartet das

    public int|null $account_id = null;

    public int|null $booking_account_id = null;

    public TransactionType|null $type = null;

    public TransactionStatus $status = TransactionStatus::submitted;

    public BookingAccountArea|null $area = null;

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
        $this->area = $transaction->area;
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
            'reference' => ['nullable'],
            'description' => ['nullable'],
            'booking_account_id' => ['nullable', 'integer'],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'status' => ['required', Rule::enum(TransactionStatus::class)],
            'area' => ['nullable', Rule::enum(BookingAccountArea::class)],
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
            'type.required' => 'Der Typ der Buchung muss angegeben werden',
            'status.required' => 'Der Buchungsstatus muss angegeben werden',
        ];
    }

    public function setType(string $value): void
    {
        $this->type = TransactionType::from($value);
    }

    public function setStatus(string $value): void
    {
        $this->status = TransactionStatus::from($value);
    }
}
