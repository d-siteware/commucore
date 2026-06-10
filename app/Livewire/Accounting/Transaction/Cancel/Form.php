<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Transaction\Cancel;

use App\Actions\Accounting\CancelTransaction;
use App\Enums\TransactionStatus;
use App\Livewire\Forms\Accounting\CancelTransactionForm;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\Transaction;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

final class Form extends Component
{
    use HasPrivileges;

    public ?Transaction $transaction = null;

    public CancelTransactionForm $form;

    public ?int $transactionId = null;

    protected $listeners = ['cancel-transaction' => 'loadTransaction'];

    public function loadTransaction(int $transactionId): void
    {
        $this->form->transaction_id = $transactionId;
    }

    public function mount(?int $transactionId = null): void
    {
        $this->transaction = Transaction::findOrFail($transactionId);
        $this->form->transaction_id = $transactionId;
        $this->form->status = $this->transaction->status;
        $this->form->user_id = Auth::user()->id;
    }

    public function cancel(): void
    {
        $this->checkPrivilege(Transaction::class);

        $this->validate([
            'form.reason' => 'required',
            'form.user_id' => 'required|exists:users,id',
            'form.transaction_id' => 'required|exists:transactions,id',
            'form.status' => Rule::enum(TransactionStatus::class),
        ],
            [
                'reason.required' => __('transaction.cancel-transaction-modal.reason.error'),
            ]);

        CancelTransaction::handle($this->transaction, ['transaction_id' => $this->form->transaction_id, 'user_id' => $this->form->user_id, 'status' => $this->form->status, 'reason' => $this->form->reason]);

        $this->dispatch('transaction-updated');
        Flux::toast(
            text: __('transaction.cancel-success.text', ['label' => $this->transaction->label]),
            heading: __('transaction.cancel-success.heading'),
            variant: 'success',
        );

        Flux::modal('cancel-transaction')
            ->close();
    }

    public function render(): View
    {
        return view('livewire.accounting.transaction.cancel.form');
    }
}
