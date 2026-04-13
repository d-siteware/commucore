<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Account\Create;

use App\Actions\Accounting\UpdateAccount;
use App\Livewire\Forms\Accounting\AccountForm;
use App\Models\Accounting\Account;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;

final class Form extends Component
{
    public AccountForm $form;

    public Account $account;

    public function mount(Account $account): void
    {
        $this->form->set($account);
    }

    public function updatedAccount(Account $account): void
    {
        $this->form->set($account);
    }

    public function updateAccountData(): void
    {
        $this->checkUser();
        UpdateAccount::handle($this->form);
        Flux::toast(
            heading: 'Erfolg',
            text: 'Das Konto  wurde aktualisiert',
            variant: 'success',
        );
    }

    protected function checkUser(): void
    {
        try {
            $this->authorize('update', $this->form);
        } catch (AuthorizationException $e) {
            Flux::toast(
                text: 'You have no permission to edit this! '.$e->getMessage(),
                heading: 'Forbidden',
                variant: 'danger',
            );

            return;
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.account.create.form');
    }
}
