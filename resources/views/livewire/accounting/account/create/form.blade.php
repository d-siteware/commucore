
<div>
    @can('update',\App\Models\Accounting\Account::class)
        <form wire:submit="updateAccountData">
            <section class="space-y-6 mb-3 max-w-xl">
                <flux:input wire:model="form.name"
                            label="{{ __('transaction.account.name')}}"
                />
                <flux:input wire:model="form.number"
                            label="{{ __('transaction.account.number')}}"
                />
                <flux:input wire:model="form.institute"
                            label="{{ __('transaction.account.institute')}}"
                />
                <flux:input wire:model="form.type"
                            label="{{ __('transaction.account.type')}}"
                />
                <flux:input wire:model="form.iban"
                            label="{{ __('transaction.account.iban')}}"
                />
                <flux:input wire:model="form.bic"
                            label="{{ __('transaction.account.bic')}}"
                />
                <flux:input readonly
                            variant="filled"
                            wire:model="form.starting_amount"
                            label="{{ __('transaction.account.starting_amount')}}"
                />
            </section>
            <flux:button type="submit"
                         variant="primary"
                         size="sm"
            >Speichern
            </flux:button>
        </form>
    @else

        <dl class="divide-y divide-zinc-100 max-w-xl">
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Name</dt>
                <dd class="mt-1 text-sm/6  sm:col-span-2 sm:mt-0">{{ $form->name }}</dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Nummer</dt>
                <dd class="mt-1 text-sm/6  sm:col-span-2 sm:mt-0">{{ $form->number }}</dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Institut</dt>
                <dd class="mt-1 text-sm/6  sm:col-span-2 sm:mt-0">{{ $form->institute }}</dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Typ</dt>
                <dd class="mt-1 text-sm/6  sm:col-span-2 sm:mt-0">{{ $form->type }}</dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">IBAN</dt>
                <dd class="mt-1 text-sm/6  sm:col-span-2 sm:mt-0">{{ $form->iban }}</dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">BIC/SWIFT</dt>
                <dd class="mt-1 text-sm/6  sm:col-span-2 sm:mt-0">{{ $form->bic }}</dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Startguthaben</dt>
                <dd class="mt-1 text-sm/6  sm:col-span-2 sm:mt-0">{{ Account::formatedAmount((int) $form->starting_amount) }}</dd>
            </div>

        </dl>
    @endcan
</div>

