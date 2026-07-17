<div>
    @if($account)
           <header class="flex flex-col lg:flex-row lg:items-center mb-6 space-y-3">
               <flux:heading size="xl">{{ __('account.index.title') }}</flux:heading>
               <flux:spacer/>
               <aside class="flex gap-3">
                    <flux:select wire:model="selectedAccount"
                                 variant="listbox"
                                 searchable
                                 placeholder="{{ __('account.select_placeholder') }}"
                                 class="w-52"
                                 size="sm"
                    >

                        @foreach($this->accounts as $item)
                            <flux:select.option wire:key="{{ $item->id }}"
                                                value="{{ $item->id }}"
                            >{{ $item->name }}</flux:select.option>
                        @endforeach

                    </flux:select>
                   <flux:button variant="outline"
                                wire:click="editAccount"
                                size="sm"
                   >{{ __('account.index.btn.fetch_data') }}</flux:button>
                   @can('create', \App\Models\Accounting\Account::class)
                       <flux:button variant="primary"
                                    href="{{ route('accounts.create') }}"
                                    size="sm"
                                    icon-trailing="plus"
                       ><span class="hidden lg:inline-flex">{{ __('account.index.btn.create_account') }}</span></flux:button>
                   @endcan
               </aside>

           </header>

           <flux:tab.group>
               <flux:tabs>
                   <flux:tab name="account-index-details"
                             wire:click="setSelectedTab('account-index-details')"
                   >{{ __('account.tabs.details') }}
                   </flux:tab>
                    <flux:tab name="account-index-transactions"
                              wire:click="setSelectedTab('account-index-transactions')"
                    >{{ __('account.tabs.transactions') }}
                    </flux:tab>
<flux:tab name="account-index-reports"
                              wire:click="setSelectedTab('account-index-reports')"
                    >{{ __('account.tabs.reports') }}
                    </flux:tab>
                   <flux:tab name="account-index-cashcounts"
                             wire:click="setSelectedTab('account-index-cashcounts')"
                   >{{ __('account.tabs.cash_counts') }}
                   </flux:tab>
               </flux:tabs>
               <flux:tab.panel name="account-index-details">
                   @if($account)
                       <livewire:accounting.account.create.form :account="$account"
                                                                wire:key="account-form-{{ $account->id }}"
                                                                state="new"
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
                                <flux:table.column>{{ __('account.columns.label') }}</flux:table.column>
                                <flux:table.column align="right"
                                                   class="hidden lg:table-cell"
                                >{{ __('account.columns.amount') }}
                               </flux:table.column>
                                <flux:table.column class="hidden lg:table-cell">{{ __('account.columns.type') }}</flux:table.column>
                                <flux:table.column class="hidden lg:table-cell">{{ __('account.columns.status') }}</flux:table.column>
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
                                       <flux:table.cell align="end"
                                                        class="hidden lg:table-cell"
                                       >
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
                                <flux:table.column>{{ __('reports.account.timespan') }}</flux:table.column>
                                <flux:table.column>{{ __('common.status') }}</flux:table.column>
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
                                           <flux:badge color="{{ $item->status->color() }}"
                                                       size="xs"
                                           >{{ $item->status->label() }}</flux:badge>
                                       </flux:table.cell>
                                       <flux:table.cell>
                                           <flux:button icon="printer"
                                                        href="{{ route('accounts.report.print',$item->id) }}"
                                                        target="_blank"
                                                        size="xs"
                                           >{{ __('account.dashboard.reports.btn.print') }}</flux:button>
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
                                <flux:table.column>{{ __('cash_count.columns.label') }}</flux:table.column>
                                <flux:table.column>{{ __('cash_count.columns.counted_at') }}</flux:table.column>
                                <flux:table.column>{{ __('cash_count.columns.sum') }}</flux:table.column>
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

    @else
        <div class="h-screen flex items-center justify-center -mt-16">
            <div class="flex flex-col gap-3 max-w-xl mx-auto">
                <flux:heading size="xl" class="flex-none">{{ __('account.index.title_no_state') }}</flux:heading>
                <flux:spacer/>
               <div class="flex flex-col gap-3 w-full justify-between">
                    <flux:select wire:model="selectedAccount"
                                 variant="listbox"
                                 searchable
                                 placeholder="{{ __('account.select_placeholder') }}"
                    >

                       @foreach($this->accounts as $item)
                           <flux:select.option wire:key="{{ $item->id }}"
                                               value="{{ $item->id }}"
                           >{{ $item->name }}</flux:select.option>
                       @endforeach

                   </flux:select>
                 <aside>
                     <flux:button variant="primary"
                                  wire:click="editAccount"
                                  class="shrink"
                     >{{ __('account.index.btn.fetch_data') }}</flux:button>
                     @can('create', \App\Models\Accounting\Account::class)
                         <flux:button variant="filled"
                                      href="{{ route('accounts.create') }}"
                         >{{ __('account.index.btn.create_account') }}</flux:button>
                     @endcan
                 </aside>
               </div>
            </div>
        </div>
    @endif
</div>
