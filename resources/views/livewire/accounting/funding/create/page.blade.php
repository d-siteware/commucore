<div>
    <flux:heading size="xl" class="mb-3 lg:mb-9">{{ __('fundings.create.page.title') }}</flux:heading>

    <form wire:submit="createFunding">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

            <section class="space-y-6">

                <flux:input wire:model="form.title"
                            label="{{ __('fundings.form.title') }}"
                />

                <flux:input wire:model="form.funder"
                            label="{{ __('fundings.form.funder') }}"
                />

                <flux:input wire:model="form.reference"
                            label="{{ __('fundings.form.reference') }}"
                            description="{{ __('fundings.form.reference_hint') }}"
                />

                <flux:select wire:model="form.status"
                             variant="listbox"
                             label="{{ __('fundings.form.status') }}"
                >
                    @foreach(\App\Enums\FundingStatus::cases() as $s)
                        <flux:select.option value="{{ $s->value }}">
                            <flux:badge color="{{ $s->color() }}">{{ $s->label() }}</flux:badge>
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:textarea wire:model="form.description"
                               rows="auto"
                               label="{{ __('fundings.form.description') }}"
                />

            </section>

            <section class="space-y-6">

                <flux:field>
                    <flux:label>{{ __('fundings.form.approved_amount') }}</flux:label>
                    <flux:input.group>
                        <flux:input wire:model="form.approved_amount"
                                    placeholder="0,00"
                                    x-mask:dynamic="$money($input, ',', '.')"
                        />
                        <flux:input.group.suffix>{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</flux:input.group.suffix>
                    </flux:input.group>
                    <flux:error name="form.approved_amount"/>
                </flux:field>

                <flux:date-picker wire:model="form.funding_period_start"
                                  with-today
                                  selectable-header
                                  label="{{ __('fundings.form.period_start') }}"
                />

                <flux:date-picker wire:model="form.funding_period_end"
                                  with-today
                                  selectable-header
                                  label="{{ __('fundings.form.period_end') }}"
                />

            </section>

        </div>

        <div class="mt-6 flex justify-between">
            <flux:button href="{{ route('funding.index') }}"
                         wire:navigate
                         variant="ghost"
            >{{ __('app.btn.cancel') }}</flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('fundings.create.btn.submit') }}
            </flux:button>
        </div>
    </form>

    @if(!app()->isProduction())
        <x-debug/>
    @endif
</div>