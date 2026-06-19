<div>
    {{-- Header --}}
    <div class="mb-6">
        <flux:heading size="xl">
            {{ __('fiscal_year.close_year_title', ['year' => $year]) }}
        </flux:heading>
        <flux:subheading class="mt-2">
            {{ __('fiscal_year.close_year_description') }}
        </flux:subheading>
    </div>

    @if($this->transactionCount === 0 && !$search )
        {{-- No transactions at all --}}
        <flux:card>
            <div class="text-center py-12">
                <flux:icon.document-text class="mx-auto h-12 w-12 text-zinc-400"/>
                <flux:heading size="lg"
                              class="mt-4"
                >
                    {{ __('fiscal_year.no_unlocked_transactions') }}
                </flux:heading>
                <flux:subheading class="mt-2">
                    {{ __('fiscal_year.no_unlocked_transactions_description', ['year' => $year]) }}
                </flux:subheading>

                <flux:button
                        variant="ghost"
                        wire:click="cancel"
                        class="mt-6"
                >
                    {{ __('fiscal_year.back_to_overview') }}
                </flux:button>
            </div>
        </flux:card>
    @else

            <flux:card class="mb-6 bg-zinc-50">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <flux:heading size="sm">
                            {{ __('fiscal_year.selected_summary') }}
                        </flux:heading>
                        <flux:badge size="lg"
                                    color="zinc"
                        >
                            {{ $this->selectedCount }} {{ __('fiscal_year.of') }} {{ $this->transactionCount }}
                        </flux:badge>
                    </div>

                    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow-sm sm:p-6 dark:bg-gray-800/75 dark:inset-ring dark:inset-ring-white/10">
                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('fiscal_year.total_income') }}</dt>
                            <dd @class([ 'mt-1 text-3xl font-semibold tracking-tight',
                                'text-gray-300 dark:text-white' => $this->totalIncome == 0,
                                'text-lime-800 dark:text-lime-400' => $this->totalIncome > 0,
                                ])
                            >{{ \App\Helpers\MoneyHelper::formatCents((int)($this->totalIncome * 100)) }}</dd>
                        </div>
                        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow-sm sm:p-6 dark:bg-gray-800/75 dark:inset-ring dark:inset-ring-white/10">
                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('fiscal_year.total_expense') }}</dt>
                            <dd @class([ 'mt-1 text-3xl font-semibold tracking-tight',
                                'text-gray-300 dark:text-white' => $this->totalExpense == 0,
                                'text-amber-800 dark:text-amber-400' => $this->totalExpense > 0,
                                ])>{{ \App\Helpers\MoneyHelper::formatCents((int)($this->totalExpense * 100)) }}</dd>
                        </div>
                        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow-sm sm:p-6 dark:bg-gray-800/75 dark:inset-ring dark:inset-ring-white/10">
                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('fiscal_year.balance') }}</dt>
                            <dd @class([ 'mt-1 text-3xl font-semibold tracking-tight',
                                'text-gray-300 dark:text-white' => $this->balance == 0,
                                'text-lime-600 dark:text-lime-400' => $this->balance > 0,
                                'text-red-600 dark:text-red-400' => $this->balance < 0,
                                ])>{{ \App\Helpers\MoneyHelper::formatCents((int)($this->balance * 100)) }}</dd>
                        </div>
                    </dl>
                </div>

            </flux:card>

        {{-- Search --}}
        <div class="mb-6 flex items-center gap-2 justify-between">
            <flux:input icon="magnifying-glass" class="grow"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('fiscal_year.search_transactions_placeholder') }}"
                        clearable
            />

            <flux:select wire:model.live="showRows" placeholder="Anzahl an Zeilen" class="ml-2 w-18">
                <flux:select.option :value="10">10</flux:select.option>
                <flux:select.option :value="25">25</flux:select.option>
                <flux:select.option :value="50">50</flux:select.option>
            </flux:select>

            {{-- Action Buttons --}}
            <div class="flex justify-between items-center">
                <flux:button
                        variant="ghost"
                        wire:click="cancel"
                >
                    {{ __('fiscal_year.cancel') }}
                </flux:button>

                <flux:button
                        variant="primary"
                        wire:click="showConfirmationModal"
                        :disabled="$this->selectedCount === 0"
                >
                    {{ __('fiscal_year.close_year_action') }}
                </flux:button>
            </div>
        </div>

        {{-- Transactions Table --}}
        @if($this->transactionCount === 0)
            {{-- No results for current filter --}}
            <flux:card>
                <div class="text-center py-12">
                    <flux:icon.magnifying-glass class="mx-auto h-12 w-12 text-zinc-400"/>
                    <flux:heading size="lg"
                                  class="mt-4"
                    >
                        {{ __('fiscal_year.no_results') }}
                    </flux:heading>
                    <flux:subheading class="mt-2">
                        {{ __('fiscal_year.no_results_description') }}
                    </flux:subheading>

                    @if($search || $filterYear)
                        <flux:button
                                size="sm"
                                variant="ghost"
                                wire:click="clearFilters"
                        >
                            {{ __('fiscal_year.clear_filters') }}
                        </flux:button>
                    @endif
                </div>
            </flux:card>
        @else
            <flux:table :paginate="$this->unlockedTransactions">
                <flux:table.columns>
                    <flux:table.columns>
                        <flux:table.column>
                            <flux:checkbox wire:model.live="selectAll" class="ml-2"/>
                        </flux:table.column>

                        <flux:table.column
                                :sortable="true"
                                :sorted="$sortBy === 'date' ? $sortDirection : false"
                                wire:click="sort('date')"
                        >
                            {{ __('fiscal_year.date') }}
                        </flux:table.column>

                        <flux:table.column
                                :sortable="true"
                                :sorted="$sortBy === 'label' ? $sortDirection : false"
                                wire:click="sort('label')"
                        >
                            {{ __('fiscal_year.description') }}
                        </flux:table.column>

                        <flux:table.column
                                :sortable="true"
                                :sorted="$sortBy === 'account' ? $sortDirection : false"
                                wire:click="sort('account')"
                        >
                            {{ __('fiscal_year.account') }}
                        </flux:table.column>

                        <flux:table.column
                                :sortable="true"
                                :sorted="$sortBy === 'type' ? $sortDirection : false"
                                wire:click="sort('type')"
                        >
                            {{ __('fiscal_year.type') }}
                        </flux:table.column>

                        <flux:table.column
                                class="text-right"
                                :sortable="true"
                                :sorted="$sortBy === 'amount_net' ? $sortDirection : false"
                                wire:click="sort('amount_net')"
                        >
                            {{ __('fiscal_year.amount') }}
                        </flux:table.column>
                    </flux:table.columns>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->unlockedTransactions as $pageIndex =>  $transaction)
                        @php
                            $isSelected = in_array($transaction->id, $selectedTransactions);
                        @endphp
                        <flux:table.row :key="$transaction->id"
                                        class="cursor-pointer {{ $isSelected ? 'bg-accent/20 hover:bg-accent/45' : 'hover:bg-zinc-50' }}"
                                        x-data="{ transactionId: {{ $transaction->id }}, pageIndex: {{ $pageIndex }} }"
                                        x-on:click="(event) => {
                                    if (event.target.type === 'checkbox') return;
                                    $wire.toggleTransaction(transactionId, pageIndex, event.shiftKey);
                                }"
                        >          <flux:table.cell>

                                <flux:checkbox class="ml-2" wire:model.live="selectedTransactions" :value="$transaction->id"
                                        x-on:change="$wire.toggleTransaction({{ $transaction->id }}, {{ $pageIndex }}, false)"
                                />
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:text class="text-sm">
                                    {{ \App\Helpers\DateHelper::formatDate($transaction->date) }}
                                </flux:text>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div>
                                    <flux:text class="text-sm font-medium">
                                        {{ $transaction->label }}
                                    </flux:text>
                                    @if($transaction->member_transaction?->member)
                                        <flux:subheading class="text-xs">
                                            {{ $transaction->member_transaction->member->fullName() }}
                                        </flux:subheading>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:text class="text-sm">
                                    {{ $transaction->account?->name }}
                                </flux:text>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge
                                        :color="$transaction->type->color()"
                                        size="sm"
                                >
                                    {{ $transaction->type->label() }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell class="text-right">
                                <flux:text class="text-sm font-mono">
                                    <span class="text-{{ $transaction->type->color() }}-600">
                                        {{ $transaction->type->isIncome() ? '+' : '-' }}{{ \App\Helpers\MoneyHelper::formatCents($transaction->amount_net) }}
                                    </span>
                                </flux:text>
                            </flux:table.cell></flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

        @endif


    @endif

    {{-- Confirmation Modal --}}
    @if($showConfirmation)
        <flux:modal wire:model="showConfirmation"
                    class="md:w-[600px]"
        >
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('fiscal_year.confirm_close_title') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('fiscal_year.confirm_close_description', [
                            'year' => $year,
                            'count' => $this->selectedCount
                        ]) }}
                    </flux:text>
                </div>

                <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                    <div class="flex items-start gap-3">
                        <flux:icon.exclamation-triangle class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"/>
                        <div>
                            <flux:heading size="sm"
                                          class="text-yellow-900"
                            >
                                {{ __('fiscal_year.important_notice') }}
                            </flux:heading>
                            <ul class="mt-2 text-sm text-yellow-700 space-y-1 list-disc list-inside">
                                <li>{{ __('fiscal_year.transactions_will_be_locked') }}</li>
                                <li>{{ __('fiscal_year.year_will_be_closed', ['year' => $year]) }}</li>
                                <li>{{ __('fiscal_year.next_year_created', ['year' => $nextYear]) }}</li>
                                <li class="font-semibold">{{ __('fiscal_year.admin_only_reopen') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <flux:checkbox
                        wire:model.live="confirmClose"
                        label="{{ __('fiscal_year.i_confirm_close') }}"
                />

                @error('confirm')
                <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                    <flux:text class="text-red-700">{{ $message }}</flux:text>
                </div>
                @enderror

                @error('selection')
                <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                    <flux:text class="text-red-700">{{ $message }}</flux:text>
                </div>
                @enderror

                <div class="flex justify-between pt-4 border-t">
                    <flux:button
                            variant="ghost"
                            wire:click="$set('showConfirmation', false)"
                    >
                        {{ __('fiscal_year.cancel') }}
                    </flux:button>

                    <flux:button
                            variant="danger"
                            wire:click="close"
                            :disabled="!$confirmClose"
                    >
                        {{ __('fiscal_year.close_now') }}
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>