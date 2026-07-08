<div>
    <form wire:submit="cancel"
          class="space-y-6"
    >

        <input type="hidden"
               wire:model="form.transaction_id"
        >
        <flux:textarea wire:model="form.reason"
                       label="{{ __('transaction.cancel-transaction-modal.reason.label') }}"
        />

        <flux:button variant="danger"
                     type="submit"
        >{{ __('transaction.cancel-transaction-modal.btn.submit.label') }}</flux:button>

    </form>

</div>
