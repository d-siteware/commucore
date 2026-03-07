<div>
    <flux:heading size="xl"
                  class="mb-6"
    >
        {{ __('fiscal_year.title') }}
    </flux:heading>

    @if($this->fiscalYears->isEmpty())
        <flux:card>
            <div class="text-center py-12 space-y-6">
                <flux:icon.calendar-days class="mx-auto h-12 w-12 text-gray-400"/>
                <flux:heading size="lg"
                              class="mt-4"
                >
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                            {{ __('fiscal_year.opened_at') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                            {{ __('fiscal_year.closed_at') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                            {{ __('fiscal_year.status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                            {{ __('fiscal_year.transactions') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                            {{ __('fiscal_year.balance') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                            {{ __('fiscal_year.actions') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($this->fiscalYears as $fiscalYear)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg lg:text-sm font-medium text-gray-900">
                                    {{ $fiscalYear->year }}
                                </div>

                                <div class="lg:hidden flex flex-col gap-1 mt-2">
                                    <div class="text-sm text-gray-900">
                                        {{ __('fiscal_year.opened_at') }}:
                                        {{ $fiscalYear->opened_at->format('d.m.Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $fiscalYear->openedBy?->name ?? '-' }}
                                    </div>

                                    @if($fiscalYear->isClosed())
                                        <flux:badge color="green"
                                                    size="sm"
                                        >
                                            {{ __('fiscal_year.closed') }}
                                        </flux:badge>
                                    @else
                                        <flux:badge color="yellow"
                                                    size="sm"
                                        >
                                            {{ __('fiscal_year.open') }}
                                        </flux:badge>
                                    @endif

                                    @if($fiscalYear->closed_at)
                                        <div class="text-sm text-gray-900">
                                            {{ __('fiscal_year.closed_at') }}:
                                            {{ $fiscalYear->closed_at->format('d.m.Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $fiscalYear->closedBy?->name ?? '-' }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif

                                    <div class="flex space-x-2 mt-2">
                                        <flux:button
                                                size="sm"
                                                variant="filled"
                                                wire:click="showDetails({{ $fiscalYear->year }})"

                                        >{{ __('fiscal_year.details') }}</flux:button>

                                        @if(!$fiscalYear->isClosed() )
                                            @can('close', \App\Models\Accounting\FiscalYear::class)
                                                <flux:button
                                                        size="sm"
                                                        variant="primary"
                                                        wire:click="navigateToClose({{ $fiscalYear->year }})"
                                                >{{ __('fiscal_year.close_year') }}</flux:button>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                <div class="text-sm text-gray-900">
                                    {{ $fiscalYear->opened_at->format('d.m.Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $fiscalYear->openedBy?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
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
                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                @if($fiscalYear->isClosed())
                                    <flux:badge color="green"
                                                size="sm"
                                    >
                                        {{ __('fiscal_year.closed') }}
                                    </flux:badge>
                                @else
                                    <flux:badge color="yellow"
                                                size="sm"
                                    >
                                        {{ __('fiscal_year.open') }}
                                    </flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                {{ $fiscalYear->transactions_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                @if($fiscalYear->isClosed())
                                    @php
                                        $balance= $fiscalYear->balance();
                                    @endphp

                                    <span @class([ 'text-green-700'=>$balance>0,'text-zinc-400'=>$balance===0,'text-amber-500'=>$balance<0 ])>
                               {{ $balance>0? '+':'-' }}    {{ number_format($balance/100,2,',','.')}}
                               </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium hidden lg:table-cell">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-vertical"/>
                                    <flux:menu>
                                        @if(!$fiscalYear->isClosed() )
                                            @can('close', \App\Models\Accounting\FiscalYear::class)
                                                <flux:menu.item wire:click="navigateToClose({{ $fiscalYear->year }})"
                                                icon="document-currency-euro"
                                                >{{ __('fiscal_year.close_year') }}</flux:menu.item>
                                            @endcan
                                        @endif
                                        <flux:menu.item wire:click="showDetails({{ $fiscalYear->year }})"
                                                        icon="eye"
                                        >{{ __('fiscal_year.details') }}</flux:menu.item>
                                        @if($fiscalYear->isClosed() )
                                            {{-- Reopen --}}
                                            @can('reopen', \App\Models\Accounting\FiscalYear::class)
                                                <flux:menu.item wire:click="reopenFiscalYear({{ $fiscalYear }})"
                                                                wire:confirm="{{ __('fiscal_year.confirm_reopen', ['year' => $fiscalYear->year]) }}"
                                                                icon="arrow-path-rounded-square"
                                                >{{ __('fiscal_year.reopen') }}</flux:menu.item>

                                            @endcan

                                            {{-- DATEV CSV Download --}}
                                            @can('create', \App\Models\Accounting\FiscalYear::class)
                                                <flux:menu.item
                                                        wire:click="downloadDatevCsv({{ $fiscalYear->year }})"
                                                        wire:loading.attr="disabled"
                                                        icon="table-cells"
                                                >
                                                    DATEV CSV
                                                </flux:menu.item>
                                            @endcan

                                            {{-- PDF Jahresabschluss --}}
                                            <flux:menu.item wire:click="downloadFiscalYearPdf({{ $fiscalYear->year }})"
                                                            wire:loading.attr="disabled"
                                                            icon="document-arrow-down"
                                            >
                                                {{ __('fiscal_year.export') }} PDF
                                            </flux:menu.item>

                                        @endif
                                        {{-- Delete --}}
                                        @can('delete', \App\Policies\FiscalYear::class)
                                            <flux:menu.item variant="danger"
                                                            wire:click="deleteFY({{ $fiscalYear->year }})"
                                                            size="sm"
                                                            icon="trash"
                                            >
                                                {{ __('fiscal_year.delete.title') }}
                                            </flux:menu.item>
                                        @endcan


                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="w-72 place-content-center mx-auto my-6">
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
    @endif

    <flux:modal name="make-new-fiscal-year-modal"
                class="max-w-md"
    >
        <div>
            <flux:heading size="lg"> {{ __('fiscal_year.create.heading') }}</flux:heading>
        </div>

        <livewire:accounting.fiscal-year.create.form/>
    </flux:modal>


    @if($snapshotData)

        {{--
          Ersetze den bestehenden Modal-Inhalt in deiner fiscal-year-detail-modal Komponente.
          Die neuen Buttons ergänzen: DATEV CSV Download + PDF Jahresabschluss Download.
      --}}

        <flux:modal name="fiscal-year-detail-modal"
                    class="md:max-w-xl"
        >
            <div class="space-y-6">

                {{-- Titel --}}
                <div>
                    <flux:heading size="lg">
                        {{ __('fiscal_year.details_title', ['year' => $selectedYear]) }}
                    </flux:heading>
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

            </div>
        </flux:modal>

    @endif

    <flux:modal name="delete-fiscal-year-modal"
                class="md:max-w-xl"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('fiscal_year.delete.heading', ['year' =>  $selectedYear]) }}</flux:heading>
                <flux:text class="mt-2 lg:my-6">{{ __('fiscal_year.delete.has_transactions') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer/>
                <flux:button variant="ghost"
                             wire:click="closeDeleteModal"
                >Abbruch
                </flux:button>
                <flux:button type="submit"
                             variant="danger"
                >{{ __('fiscal_year.delete.confirm') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>