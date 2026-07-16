<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Account\Create;

use App\Livewire\Forms\Accounting\AccountForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\Account;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Component;

final class Form extends Component
{
    use HandlesErrors;
    use HasPrivileges;

    public AccountForm $form;

    public Account $account;

    public string $state = 'create';

    public function mount(Account $account): void
    {
        $this->form->set($account);
    }

    public function updated(string $propertyName): void
    {
        if (str_starts_with($propertyName, 'form.')) {
            $this->form->validateOnly(substr($propertyName, 5));
        }
    }

    public function updatedAccount(Account $account): void
    {
        $this->checkPrivilege(Account::class, 'update');

        $this->form->set($account);
    }

    public function storeData(): void
    {
        try {
            $this->checkPrivilege(Account::class, 'update');

            if ($this->state === 'create') {
                $this->createAccount();
            } else {
                $this->updateAccountData();
            }
        } catch (\Throwable $e) {
            $this->handleError('Konto speichern fehlgeschlagen', $e);
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

    public function render(): View
    {
        return view('livewire.accounting.account.create.form');
    }
}
