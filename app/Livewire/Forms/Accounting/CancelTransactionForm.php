<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use Livewire\Form;

final class CancelTransactionForm extends Form
{
    public $reason;

    public $user_id;

    public $transaction_id;
}
