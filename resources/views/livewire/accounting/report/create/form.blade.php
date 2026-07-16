<div>
    <form wire:submit="storeReportData">
        <section class="space-y-6">
            <flux:radio.group wire:model.live="setRange"
                              label="{{ __('reports.account.timespan') }}"
                              variant="segmented"
            >
                <flux:radio value="{{ \Carbon\Carbon::today('Europe/Berlin')->subMonths(2)->month }}"
                            label="{{ \Carbon\Carbon::today('Europe/Berlin')->locale(app()->getLocale())->subMonths(2)->monthName }}"
                />
                <flux:radio value="{{ \Carbon\Carbon::today('Europe/Berlin')->subMonth()->month }}"
                            label="{{ \Carbon\Carbon::today('Europe/Berlin')->locale(app()->getLocale())->subMonth()->monthName }}"
                            checked
                />

                <flux:radio value="{{ \Carbon\Carbon::today('Europe/Berlin')->month }}"
                            label="{{ \Carbon\Carbon::today('Europe/Berlin')->locale(app()->getLocale())->monthName }}"
                />
            </flux:radio.group>
            <section class="grid gap-3 grid-cols-2">
                <flux:date-picker locale="{{ app()->getLocale() }}" wire:model.blur="form.period_start"
                                  label="{{ __('reports.account.start') }}"
                />
                <flux:date-picker locale="{{ app()->getLocale() }}" wire:model.blur="form.period_end"
                                  label="{{ __('reports.account.end') }}"
                />

            </section>
            <flux:button wire:click="getTransactions">{{ __('reports.account.btn.get_transactions') }}</flux:button>
            <flux:accordion transition>
                <flux:accordion.item>
                    <flux:accordion.heading>{{ $msg }}</flux:accordion.heading>

                    <flux:accordion.content class="max-h-36 overflow-y-auto">
                        @if($transactions)
                            <ul>
                                @forelse($transactions as $transaction)
                                    <li class="flex justify-between items-center">
                                        <span>{{ $transaction->label }}</span>
                                        <span class="{{ $transaction->type->color() }} pr-2">{{ \App\Helpers\MoneyHelper::formatCents((int) ($transaction->amount_gross * $transaction->type->multiplier()), withSymbol: false) }}</span>
                                    </li>
                                @empty
                                    <li>-</li>
                                @endforelse
                            </ul>
                        @endif
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <section class="grid gap-3 grid-cols-2">
                <flux:input wire:model.blur="form.starting_amount"
                            label="{{ __('reports.account.starting_amount') }}"
                />
                <flux:input wire:model.blur="form.end_amount"
                            label="{{ __('reports.account.end_amount') }}"
                />
            </section>
            <section class="grid gap-3 grid-cols-2">
                <flux:input wire:model.blur="form.total_income"
                            label="{{ __('reports.account.total_income') }}"
                />
                <flux:input wire:model.blur="form.total_expenditure"
                            label="{{ __('reports.account.total_expenditure') }}"
                />
            </section>

            <flux:textarea wire:model.blur="form.notes"
                           rows="auto"
                           label="{{ __('reports.account.notes') }}"
            />

            <flux:button type="submit">{{ __('reports.account.btn.store_data') }}</flux:button>
        </section>


    </form>
</div>
