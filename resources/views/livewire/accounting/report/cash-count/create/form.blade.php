<div>



    <form wire:submit="store" class="space-y-6">

       <section class="grid grid-cols-1 lg:grid-cols-2 gap-3">
           <flux:input wire:model="form.label" label="Label" />
           <flux:date-picker wire:model="form.counted_at" label="vom" with-today/>
       </section>

        <flux:separator class="my-4 lg:my-12" text="Scheine {{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}" />

        <section class="grid grid-cols-3 lg:grid-cols-6 gap-3">
            <flux:input type="number" min="0" wire:model="form.euro_two_hundred" label="200" />
            <flux:input type="number" min="0" wire:model="form.euro_one_hundred" label="100" />
            <flux:input type="number" min="0" wire:model="form.euro_fifty" label="50" />
            <flux:input type="number" min="0" wire:model="form.euro_twenty" label="20" />
            <flux:input type="number" min="0" wire:model="form.euro_ten" label="10" />
            <flux:input type="number" min="0" wire:model="form.euro_five" label="5" />
        </section>

        <flux:separator class="my-4 lg:my-12" text="Münzen" />

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

        <flux:textarea rows="auto" wire:model="form.notes" label="Bemerkungen" />

        <flux:button variant="primary" type="submit" icon-trailing="banknotes">Erfassen</flux:button>
    </form>

</div>
