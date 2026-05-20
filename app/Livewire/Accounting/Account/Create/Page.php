<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Account\Create;

use App\Livewire\Forms\Accounting\AccountForm;
use App\Models\Accounting\Account;
use Livewire\Component;

final class Page extends Component
{
    public AccountForm $form;

    public Account $account;
}
