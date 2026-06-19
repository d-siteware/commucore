<div>



    <form wire:submit="store" class="space-y-6">

       <section class="grid grid-cols-1 lg:grid-cols-2 gap-3">
           <flux:input wire:model="form.label" label="{{ __('cash_count.columns.label') }}" />
           <flux:date-picker locale="{{ app()->getLocale() }}" wire:model="form.counted_at" label="{{ __('account.cashcount.dated') }}" with-today/>
       </section>

        <flux:separator class="my-4 lg:my-12" text="{{ __('cash_count.bills', ['currency' => \App\Helpers\MoneyHelper::getCurrencySymbol()]) }}" />

        <section class="grid grid-cols-3 lg:grid-cols-6 gap-3">
            <flux:input type="number" min="0" wire:model="form.euro_two_hundred" label="200" />
            <flux:input type="number" min="0" wire:model="form.euro_one_hundred" label="100" />
            <flux:input type="number" min="0" wire:model="form.euro_fifty" label="50" />
            <flux:input type="number" min="0" wire:model="form.euro_twenty" label="20" />
            <flux:input type="number" min="0" wire:model="form.euro_ten" label="10" />
            <flux:input type="number" min="0" wire:model="form.euro_five" label="5" />
        </section>

        <flux:separator class="my-4 lg:my-12" text="{{ __('cash_count.coins') }}" />

        <section class="grid grid-cols-4 lg:grid-cols-8 gap-3">
            <flux:input type="number" min="0" wire:model="form.euro_two" label="2 {{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}" />
            <flux:input type="number" min="0" wire:model="form.euro_one" label="1 {{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}" />
            <flux:input type="number" min="0" wire:model="form.cent_fifty" label="50 Cent" />
            <flux:input type="number" min="0" wire:model="form.cent_twenty" label="20 Cent" />
            <flux:input type="number" min="0" wire:model="form.cent_ten" label="10 Cent" />
            <flux:input type="number" min="0" wire:model="form.cent_five" label="5 Cent" />
            <flux:input type="number" min="0" wire:model="form.cent_two" label="2 Cent" />
            <flux:input type="number" min="0" wire:model="form.cent_one" label="1 Cent" />
        </section>

        <flux:textarea rows="auto" wire:model="form.notes" label="{{ __('cash_count.notes') }}" />

        <flux:button variant="primary" type="submit" icon-trailing="banknotes">{{ __('account.cashcount.create.btn.submit') }}</flux:button>
    </form>

</div>
