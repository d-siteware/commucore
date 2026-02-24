<div>
    <header class="py-3 border-b border-zinc-200 mb-6 flex justify-between">
        <flux:heading size="xl">{{ __('account.dashboard.heading', ['year' => session('financialYear')]) }}</flux:heading>

        <livewire:accounting.fiscal-year-switcher.form/>
    </header>

    <section class="grid grid-cols-1 gap-3 lg:grid-cols-2 xl:grid-cols-3 lg:gap-6">

        <flux:card>
            <flux:heading>{{ __('account.dashboard.transactions.title') }}</flux:heading>

            <flux:table :paginate="$this->transactions">
                <flux:table.columns>
                    <flux:table.column>{{ __('account.dashboard.transactions.columns.label') }}</flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'amount'"
                                       :direction="$sortDirection"
                                       wire:click="sort('amount_gross')"
                    >{{ __('account.dashboard.transactions.columns.amount') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->transactions as $items)
                        <flux:table.row :key="$items->id">
                            <flux:table.cell class="text-wrap hyphens-auto">
                                {{ $items->label }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="{{ $items->grossColor() }}">
                                    {{ $items->grossForHumans() }}
                                </span>
                            </flux:table.cell>

                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <section class="my-3 flex justify-between items-center gap-3">
                <flux:button href="{{ route('transaction.index') }}">{{ __('account.dashboard.transactions.btn.overview') }}</flux:button>
                @can('create', App\Models\Accounting\Account::class)
                    <flux:button variant="primary"
                                 href="{{ route('transaction.create') }}"
                    ><span class="hidden lg:inline">{{ __('account.dashboard.transactions.btn.create') }}</span><span class="lg:hidden">{{ __('account.dashboard.transactions.btn.create_short') }}</span>
                    </flux:button>
                @endcan
            </section>
        </flux:card>

        <flux:card>
            <section class="space-y-6 my-3">
                <flux:separator text="{{ __('account.dashboard.sections.balance_sheet') }}"/>
                <x-balance-sheet/>
                <flux:separator text="{{ __('account.dashboard.sections.cash_counts') }}"/>
                <x-cash-count-reports/>
            </section>

        </flux:card>
        <flux:card>
            <flux:heading>{{ __('account.dashboard.reports.title') }}</flux:heading>
            <section class="space-y-6 my-3">
                <flux:table :paginate="$this->reports">
                    <flux:table.columns>
                        <flux:table.column sortable
                                           :sorted="$sortBy === 'range'"
                                           :direction="$sortDirection"
                                           wire:click="sort('period_start')">{{ __('account.dashboard.reports.columns.period') }}</flux:table.column>
                        <flux:table.column
                        >{{ __('account.dashboard.reports.columns.status') }}
                        </flux:table.column>

                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->reports as $items)
                            <flux:table.row :key="$items->id">
                                <flux:table.cell class="text-wrap hyphens-auto">
                                    {{ $items->period_start->isoFormat('DD MMM') }} -
                                    {{ $items->period_end->isoFormat('DD MMM') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $items->status }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button size="xs" icon-trailing="printer" variant="primary" href="{{ route('accounts.report.print',$items) }}" target="_blank" >
                                        {{ __('account.dashboard.reports.btn.print') }}
                                    </flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </section>
        </flux:card>

    </section>


    <flux:modal name="delete-cash-count"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('account.cashcount.delete.heading')  }}</flux:heading>
                <flux:text class="mt-2" size="sm"><p>{{ __('account.cashcount.delete.label',['label'=>$selectedCashCount->label??'']) }}</p>
                    <p>{{ __('account.cashcount.delete.warning') }}</p></flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('account.cashcount.delete.btn.cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="danger"
                             wire:click="deleteCashCount"
                >{{ __('account.cashcount.delete.btn.submit') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="edit-cash-count" variant="flyout" position="right">
        <section class="gap-3">
            <flux:heading size="lg">{{ __('account.cashcount.edit.heading') }}</flux:heading>


        </section>

    </flux:modal>

</div>