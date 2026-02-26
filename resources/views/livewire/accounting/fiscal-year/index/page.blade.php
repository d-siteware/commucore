<div>
    <flux:heading size="xl" class="mb-6">
        {{ __('fiscal_year.title') }}
    </flux:heading>

    @if($this->fiscalYears->isEmpty())
        <flux:card>
            <div class="text-center py-12 space-y-6">
                <flux:icon.calendar-days class="mx-auto h-12 w-12 text-gray-400" />
                <flux:heading size="lg" class="mt-4">
                    {{ __('fiscal_year.no_years_found') }}
                </flux:heading>
                <flux:text class="mt-2 text-gray-500">
                    {{ __('fiscal_year.no_years_description') }}
                </flux:text>

                @can('create', \App\Models\Accounting\FiscalYear::class)
                    <flux:button
                            variant="primary"
                            icon="plus"
                            wire:click="openCreateFiscalYearModal"
                    >
                        {{ __('fiscal_year.index.empty_button') }}
                    </flux:button>
                @endcan
            </div>
        </flux:card>
    @else
        <flux:card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('fiscal_year.year') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('fiscal_year.opened_at') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('fiscal_year.closed_at') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('fiscal_year.status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('fiscal_year.transactions') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('fiscal_year.actions') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->fiscalYears as $fiscalYear)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $fiscalYear->year }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $fiscalYear->opened_at->format('d.m.Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $fiscalYear->openedBy?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($fiscalYear->closed_at)
                                    <div class="text-sm text-gray-900">
                                        {{ $fiscalYear->closed_at->format('d.m.Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $fiscalYear->closedBy?->name ?? '-' }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($fiscalYear->isClosed())
                                    <flux:badge color="green" size="sm">
                                        {{ __('fiscal_year.closed') }}
                                    </flux:badge>
                                @else
                                    <flux:badge color="yellow" size="sm">
                                        {{ __('fiscal_year.open') }}
                                    </flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $fiscalYear->transactions_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <flux:button
                                            size="sm"
                                            variant="ghost"
                                            wire:click="showDetails({{ $fiscalYear->year }})"
                                    >
                                        {{ __('fiscal_year.details') }}
                                    </flux:button>

                                    @if(!$fiscalYear->isClosed())
                                        <flux:button
                                                size="sm"
                                                variant="primary"
                                                wire:click="navigateToClose({{ $fiscalYear->year }})"
                                        >
                                            {{ __('fiscal_year.close_year') }}
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </flux:card>
    @endif

    <flux:modal name="make-new-fiscal-year-modal" class="max-w-md">
        <div>
            <flux:heading size="lg"> {{ __('fiscal_year.create.heading') }}</flux:heading>
        </div>

        <livewire:accounting.fiscal-year.create.page />
    </flux:modal>



    @if($showDetailsModal && $snapshotData)

        <flux:modal name="fiscal-year-detail-modal" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg"> {{ __('fiscal_year.details_title', ['year' => $selectedYear]) }}</flux:heading>
                </div>

                <div class="space-y-6">
                    {{-- Metadata --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:subheading>{{ __('fiscal_year.opened_at') }}</flux:subheading>
                            <flux:text>
                                {{ $snapshotData['metadata']['opened_at']?->format('d.m.Y H:i') ?? '-' }}
                            </flux:text>
                            @if($snapshotData['metadata']['opened_by'])
                                <flux:text class="text-xs text-gray-500">
                                    {{ __('fiscal_year.by') }} {{ $snapshotData['metadata']['opened_by'] }}
                                </flux:text>
                            @endif
                        </div>

                        <div>
                            <flux:subheading>{{ __('fiscal_year.closed_at') }}</flux:subheading>
                            <flux:text>
                                {{ $snapshotData['metadata']['closed_at']?->format('d.m.Y H:i') ?? '-' }}
                            </flux:text>
                            @if($snapshotData['metadata']['closed_by'])
                                <flux:text class="text-xs text-gray-500">
                                    {{ __('fiscal_year.by') }} {{ $snapshotData['metadata']['closed_by'] }}
                                </flux:text>
                            @endif
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('fiscal_year.total_income') }}</div>
                            <div class="text-lg font-semibold text-green-600">
                                {{ number_format($snapshotData['summary']['total_income'] / 100, 2, ',', '.') }} €
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('fiscal_year.total_expense') }}</div>
                            <div class="text-lg font-semibold text-red-600">
                                {{ number_format($snapshotData['summary']['total_expense'] / 100, 2, ',', '.') }} €
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('fiscal_year.balance') }}</div>
                            <div class="text-lg font-semibold {{ $snapshotData['summary']['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($snapshotData['summary']['balance'] / 100, 2, ',', '.') }} €
                            </div>
                        </div>
                    </div>

                    {{-- Transaction count --}}
                    <div>
                        <flux:text class="text-gray-600">
                            {{ __('fiscal_year.total_transactions') }}:
                            <span class="font-semibold">{{ $snapshotData['summary']['transaction_count'] }}</span>
                        </flux:text>
                    </div>
                </div>

                <div class="flex">
                    <flux:spacer />

                    @if($snapshotData['metadata']['is_closed'])
                        @can('reopen', \App\Models\Accounting\FiscalYear::class)
                            <flux:button
                                    variant="danger"
                                    wire:click="reopenFiscalYear({{ $selectedYear }})"
                                    wire:confirm="{{ __('fiscal_year.confirm_reopen', ['year' => $selectedYear]) }}"
                            >
                                {{ __('fiscal_year.reopen') }}
                            </flux:button>
                        @endcan

                        <flux:button
                                variant="ghost"
                                wire:click="exportSnapshot({{ $selectedYear }})"
                        >
                            {{ __('fiscal_year.export') }}
                        </flux:button>
                    @endif
                </div>

                <flux:button variant="ghost" wire:click="closeDetailsModal">
                    {{ __('fiscal_year.close') }}
                </flux:button>

            </div>
        </flux:modal>

    @endif
</div>