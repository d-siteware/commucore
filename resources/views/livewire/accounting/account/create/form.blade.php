<div>
    @can('update',\App\Models\Accounting\Account::class)
        <form wire:submit="storeData">
            <section class="space-y-6 mb-3 max-w-xl">
                <flux:input wire:model.blur="form.name"
                            label="{{ __('transaction.account.name')}}"
                />
                <flux:input wire:model.blur="form.number"
                            label="{{ __('transaction.account.number')}}"
                />
                <flux:input wire:model.blur="form.institute"
                            label="{{ __('transaction.account.institute')}}"
                />

                <flux:field>
                    <flux:label>{{ __('transaction.account.type')}}</flux:label>
                    <flux:select placeholder="{{ __('transaction.modal.account.type_placeholder') }}"
                                 wire:model.blur="form.type"
                                 variant="listbox"
                    >
                        @foreach(\App\Enums\AccountType::cases() as $type)
                            <flux:select.option value="{{ $type->value }}"
                            >{{ $type->value }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.type"/>
                </flux:field>
                <flux:input wire:model.blur="form.iban"
                            mask="aa99 9999 9999 9999 9999 99"
                            label="{{ __('transaction.account.iban')}}"
                />
                <flux:input wire:model.blur="form.bic"
                            mask="aaaaaaaaaa"
                            label="{{ __('transaction.account.bic')}}"
                />
                @if($state !=='create')
                    <flux:input readonly
                                variant="filled"
                                wire:model.blur="form.starting_amount"
                                label="{{ __('transaction.account.starting_amount')}}"
                    />
                @else
                    <flux:input wire:model.blur="form.starting_amount"
                                label="{{ __('transaction.account.starting_amount')}}"
                                x-mask:dynamic="$money($input, ',', '.')"
                    />
                @endif

            </section>
            <flux:button type="submit"
                         variant="primary"
                         size="sm"
            >{{ __('common.save') }}
            </flux:button>
        </form>
    @else

        <dl class="divide-y divide-zinc-100 max-w-xl">
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">{{ __('transaction.modal.account.name') }}</dt>
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

