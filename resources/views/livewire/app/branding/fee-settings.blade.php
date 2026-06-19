<div class="space-y-6">

    {{-- Beitragsbeträge --}}
    <div>
        <flux:heading size="sm">{{ __('fees.settings.amounts_heading') }}</flux:heading>
        <flux:subheading>{{ __('fees.settings.amounts_hint') }}</flux:subheading>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        <flux:field>
            <flux:label>{{ __('fees.settings.full_amount') }}</flux:label>
            <div class="relative">
                <flux:input
                        wire:model="fullAmount"
                        type="number"
                        min="0"
                        step="1"
                        suffix="{{ __('fees.settings.cent_suffix') }}"
                />
            </div>
            <flux:description>{{ __('fees.settings.full_amount_hint') }}</flux:description>
            <flux:error name="fullAmount"/>
        </flux:field>

        <flux:field>
            <flux:label>{{ __('fees.settings.discounted_amount') }}</flux:label>
            <flux:input
                    wire:model="discountedAmount"
                    type="number"
                    min="0"
                    step="1"
                    suffix="{{ __('fees.settings.cent_suffix') }}"
            />
            <flux:description>{{ __('fees.settings.discounted_amount_hint') }}</flux:description>
            <flux:error name="discountedAmount"/>
        </flux:field>

    </div>

    {{-- Beitragsvorschau --}}
    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800/50 p-4 text-sm space-y-1 flex justify-between items-center">
        <section>
            <p class="font-medium text-zinc-700 dark:text-zinc-300">{{ __('fees.settings.preview') }}</p>
            <p class="text-zinc-500 dark:text-zinc-400">
                {{ __('fees.type.full') }}: {{ \App\Helpers\MoneyHelper::formatCents($fullAmount) }}
            </p>
            <p class="text-zinc-500 dark:text-zinc-400">
                {{ __('fees.type.discounted') }}: {{ \App\Helpers\MoneyHelper::formatCents($discountedAmount) }}
            </p>
            <p class="text-zinc-500 dark:text-zinc-400">
                {{ __('fees.type.free') }}: 0,00 €
            </p>
        </section>
        <aside>
            <p class="font-medium text-zinc-700 dark:text-zinc-300">{{ __('fees.settings.fee_per_year') }}</p>
            <p class="text-xl lg:text-2xl lg:my-4">{{{ $feePerYear }}}</p>
        </aside>

    </div>

    <flux:separator/>

    {{-- Einzugsintervall --}}
    <div>
        <flux:heading size="sm">{{ __('fees.settings.interval_heading') }}</flux:heading>
        <flux:subheading>{{ __('fees.settings.interval_hint') }}</flux:subheading>
    </div>


    <flux:field class="grow">
        <flux:label>{{ __('fees.settings.interval_label') }}</flux:label>
        <flux:select wire:model.live="interval">
            @foreach($this->intervalOptions() as $value => $label)
                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:error name="interval"/>
    </flux:field>

    {{-- Custom-Intervall-Felder --}}
    @if($this->isCustomInterval())
        <div class="grid grid-cols-2 gap-4">

            <flux:field>
                <flux:label>{{ __('fees.settings.interval_n') }}</flux:label>
                <flux:input
                        wire:model.debounce="intervalN"
                        type="number"
                        min="1"
                        max="365"
                />
                <flux:error name="intervalN"/>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('fees.settings.interval_unit') }}</flux:label>
                <flux:select wire:model.live="intervalUnit">
                    <flux:select.option value="d">{{ __('fees.settings.unit_day') }}</flux:select.option>
                    <flux:select.option value="m">{{ __('fees.settings.unit_month') }}</flux:select.option>
                    <flux:select.option value="y">{{ __('fees.settings.unit_year') }}</flux:select.option>
                </flux:select>
                <flux:error name="intervalUnit"/>
            </flux:field>


        </div>
    @endif

    <flux:separator/>

    <div class="flex justify-end">
        <flux:button wire:click="save"
                     variant="primary"
        >
            {{ __('common.save') }}
        </flux:button>
    </div>

    <x-action-message class="me-3"
                      on="saved"
    >
        {{ __('common.saved') }}
    </x-action-message>

</div>