<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Actions\Accounting\CreateAccount;
use App\Actions\Accounting\UpdateAccount;
use App\Enums\AccountType;
use App\Models\Accounting\Account;
use App\Rules\ValidIban;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class AccountForm extends Form
{
    public Account $account;

    public $id;

    public $name;

    public $number;

    public $institute;

    public $type;

    public $iban;

    public $bic;

    public $starting_amount;

    public function set(Account $account): void
    {
        $this->name = $account->name;
        $this->number = $account->number;
        $this->institute = $account->institute;
        $this->type = $account->type;
        $this->iban = $account->iban;
        $this->bic = $account->bic;
        $this->starting_amount = \App\Helpers\MoneyHelper::formatCents($account->starting_amount, withSymbol: false);
        $this->id = $account->id;
    }

    public function create(): void
    {
        $this->validate();
        $account = CreateAccount::handle([
            'name' => $this->name,
            'number' => $this->number,
            'institute' => $this->institute,
            'type' => $this->type,
            'iban' => $this->iban,
            'bic' => $this->bic,
            'starting_amount' => Account::makeCentInteger($this->starting_amount),
        ]);

        Flux::toast(
            text: __('account.toast.payment_account_created.text'),
            heading: __('account.toast.payment_account_created.heading'),
            variant: 'success',
        );
        $this->id = $account->id;

        Flux::modal('add-account-modal')
            ->close();
    }

    public function update(): void
    {
        $this->validate();
        UpdateAccount::handle($this);

    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique('accounts', 'name')->ignore($this->id ?? null)],
            'number' => ['required', 'string', Rule::unique('accounts', 'number')->ignore($this->id ?? null)],
            'type' => ['required', Rule::enum(AccountType::class)],
            'institute' => 'string|nullable',
            'iban' => [
                Rule::requiredIf(fn () => $this->normalizedType() === AccountType::bank),
                'nullable',
                'string',
                'max:34',
                new ValidIban,
            ],
            'bic' => ['nullable', 'string', 'max:11', 'regex:/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/'],
            'starting_amount' => 'required',
        ];
    }

    private function normalizedType(): ?AccountType
    {
        return $this->type instanceof AccountType
            ? $this->type
            : AccountType::tryFrom((string) $this->type);
    }
}
