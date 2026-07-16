<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Actions\Accounting\CreateBooking;
use App\Actions\Accounting\CreateTransaction;
use App\Actions\Accounting\UpdateTransaction;
use App\Enums\BookingAccountArea;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\FiscalYear;
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

    public ?int $account_id = null;

    public $booking_account_id = null;

    public ?TransactionType $type = null;

    public TransactionStatus $status = TransactionStatus::submitted;

    public ?BookingAccountArea $area = null;

    /** @var int|null Explizite FY-Zuordnung (Override, 10-Tage-Regel § 11 EStG) */
    public ?int $fiscal_year_id = null;

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
        $this->fiscal_year_id = $transaction->fiscal_year_id;
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
        $dateYear = rescue(
            fn () => $this->date ? (int) \Illuminate\Support\Carbon::parse($this->date)->format('Y') : null,
            null,
        );

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
            'fiscal_year_id' => [
                'nullable',
                'integer',
                Rule::exists('fiscal_years', 'id'),
                function (string $attribute, mixed $value, \Closure $fail) use ($dateYear): void {
                    $fy = FiscalYear::find($value);

                    if ($fy === null) {
                        return;
                    }

                    // 1. Darf nicht geschlossen sein
                    if ($fy->isClosed()) {
                        $fail(__('transaction.form.validation.fiscal_year_closed'));

                        return;
                    }

                    // 2. Plausibilität: nur date->year oder date->year ± 1
                    if ($dateYear !== null && abs($fy->year - $dateYear) > 1) {
                        $fail(__('transaction.form.validation.fiscal_year_plausibility'));
                    }
                },
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'label.required' => __('transaction.form.validation.label_required'),
            'label.string' => __('transaction.form.validation.label_required'),
            'amount_net.required' => 'Der Nettopreis fehlt.',
            'vat.required' => 'Die % MWst Angabe fehlt',
            'amount_gross.required' => 'Der Bruttobetrag muss angegeben werden.',
            'account_id.required' => __('transaction.form.validation.account_id_required'),
            'account_id.integer' => __('transaction.form.validation.account_id_required'),
            'type.required' => __('transaction.form.validation.type_required'),
            'status.required' => __('transaction.form.validation.status_required'),
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
