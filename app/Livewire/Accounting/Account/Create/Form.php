<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Account\Create;

use App\Livewire\Forms\Accounting\AccountForm;
use App\Models\Accounting\Account;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\View\View;
use Livewire\Component;

final class Form extends Component
{
    public AccountForm $form;

    public Account $account;

    public string $state = 'create';

    public function mount(Account $account): void
    {
        $this->form->set($account);
    }

    public function updatedAccount(Account $account): void
    {
        $this->form->set($account);
    }

    public function storeData(): void
    {
        $this->checkUser();

        if ($this->state === 'create') {
            $this->createAccount();
        } else {
            $this->updateAccountData();
        }
    }

    private function createAccount(): void
    {
        $this->form->create();
        $this->state = 'new';
        Flux::toast(
            text: __('account.toast.created.text'),
            heading: __('account.toast.created.heading'),
            variant: 'success',
        );
    }

    private function updateAccountData(): void
    {

        $this->form->update();
        Flux::toast(
            text: __('account.toast.updated.text'),
            heading: __('account.toast.updated.heading'),
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

    public function render(): View
    {
        return view('livewire.accounting.account.create.form');
    }
}
