<div>

    <header class="flex flex-col lg:flex-row lg:items-end mb-6 space-y-3">
        <flux:heading size="xl">{{ __('account.index.title') }}</flux:heading>
        <flux:spacer/>
        <aside class="flex gap-3">
            <flux:select wire:model="selectedAccount"
                         variant="listbox"
                         searchable
                         placeholder="Konto auswählen ..."
                         class="w-52"
            >

                @foreach($this->accounts as $item)
                    <flux:select.option wire:key="{{ $item->id }}"
                                        value="{{ $item->id }}"
                    >{{ $item->name }}</flux:select.option>
                @endforeach

            </flux:select>
            <flux:button variant="primary"
                         wire:click="editAccount"
            >{{ __('account.index.btn.fetch_data') }}</flux:button>
        </aside>

    </header>

    <flux:tab.group>
        <flux:tabs>
            <flux:tab name="account-index-details"
                      wire:click="setSelectedTab('account-index-details')"
            >Details
            </flux:tab>
            <flux:tab name="account-index-transactions"
                      wire:click="setSelectedTab('account-index-transactions')"
            >Buchungen
            </flux:tab>
            <flux:tab name="account-index-reports"
                      wire:click="setSelectedTab('account-index-reports')"
            >Berichte
            </flux:tab>
            <flux:tab name="account-index-cashcounts"
                      wire:click="setSelectedTab('account-index-cashcounts')"
            >Zähllisten
            </flux:tab>
        </flux:tabs>
        <flux:tab.panel name="account-index-details">
            @if($account)
                <livewire:accounting.account.create.form :account="$account"
                                                         wire:key="account-form-{{ $account->id }}"
                />
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="account-index-transactions">
            @if($selectedAccount)
                <nav class="flex items-center justify-end">
                    <flux:button href="{{ route('transaction.create') }}"
                                 size="sm"
                                 variant="primary"
                    >{{ __('transaction.create.page.title') }}</flux:button>
                </nav>

                <flux:table :paginate="$this->transactions">
                    <flux:table.columns>
                        <flux:table.column>Bezeichnung</flux:table.column>
                        <flux:table.column align="right" class="hidden lg:table-cell">Betrag</flux:table.column>
                        <flux:table.column class="hidden lg:table-cell">Typ</flux:table.column>
                        <flux:table.column class="hidden lg:table-cell">Status</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->transactions as $item)
                            <flux:table.row :key="$item->id">
                                <flux:table.cell>
                                    <span class="lg:table-cell hidden">
                                        {{ $item->label }}
                                    </span>

                                    <div class="lg:hidden flex flex-col">
                                    <span class="text-wrap">{{ $item->label }}</span>
                                    <span class="text-sm"> <span class="{{ $item->grossColor() }}">{{ $item->grossForHumans()}}</span> | {{ $item->type }} | <span class="{{ $item->status->color() }}">{{ $item->status->label() }}</span></span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell align="end" class="hidden lg:table-cell">
                               <span class="text-{{ $item->grossColor() }}-700">
                                    {{ $item->grossForHumans()}}
                               </span>
                                </flux:table.cell>
                                <flux:table.cell class="hidden lg:table-cell">
                                    {{ $item->type->label() }}
                                </flux:table.cell>
                                <flux:table.cell class="hidden lg:table-cell">
                                    {{ $item->status->label() }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="account-index-reports">

            @if($selectedAccount)

                <nav class="flex items-center justify-end">
                    <flux:button wire:click="createReport"
                                 size="sm"
                                 variant="primary"
                    >{{ __('account.index.btn.create_report') }}</flux:button>
                </nav>

                <flux:table :paginate="$this->reports">
                    <flux:table.columns>
                        <flux:table.column>Zeitraum</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->reports as $item)
                            <flux:table.row :key="$item->id">
                                <flux:table.cell>
                                    {{ $item->period_start->isoFormat('MMM YY') }}
                                    -
                                    {{ $item->period_end->isoFormat('MMM YY') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $item->status }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>


            @endif

        </flux:tab.panel>
        <flux:tab.panel name="account-index-cashcounts">
            @if($selectedAccount)

                <nav class="flex items-center justify-end">
                    <flux:button wire:click="createCashCountReport"
                                 size="sm"
                                 variant="primary"
                    >{{ __('account.index.btn.create_vcashcount') }}</flux:button>
                </nav>


                <flux:table :paginate="$this->cashCounts">
                    <flux:table.columns>
                        <flux:table.column>Label</flux:table.column>
                        <flux:table.column>Gezählt</flux:table.column>
                        <flux:table.column>Summe</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($this->cashCounts as $item)
                            <flux:table.row :key="$item->id">
                                <flux:table.cell>
                                    {{ $item->label }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $item->counted_at->isoFormat('MMM YY') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $item->sumString() }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

            @endif
        </flux:tab.panel>
    </flux:tab.group>

    @if($selectedAccount)
        <flux:modal name="create-monthly-report"
                    class="w-full"
        >
            <flux:heading size="lg"
                          class="mb-3 lg:mb-6"
            >{{ __('reports.account.new.header') }}</flux:heading>
            <livewire:accounting.report.create.form :account-id="$selectedAccount"/>
        </flux:modal>
    @endif

    @if($is_cash_account)
        <flux:modal name="create-cash-count"
                    class="w-full"
        >
            <flux:heading size="lg"
                          class="mb-3 lg:mb-6"
            >{{ __('account.cashcount.create.heading') }}</flux:heading>
            <livewire:accounting.report.cash-count.create.form :account-id="$selectedAccount"/>
        </flux:modal>
    @endif
</div>
