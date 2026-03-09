<div class="space-y-6">

    <flux:heading size="xl">{{ __('fundings.page.title') }}</flux:heading>

    <nav class="flex flex-wrap lg:flex-nowrap gap-3 items-center">

        <flux:input size="sm"
                    wire:model.live.debounce="search"
                    clearable
                    icon="magnifying-glass"
                    placeholder="{{ __('fundings.index.search_placeholder') }}"
        />

        @can('create', \App\Models\Funding\Funding::class)
            <flux:button href="{{ route('funding.create') }}"
                         variant="primary"
                         icon="plus"
                         size="sm"
            >
                <span class="hidden lg:inline">{{ __('fundings.index.btn.create') }}</span>
            </flux:button>
        @endcan

        <flux:select variant="listbox"
                     multiple
                     placeholder="{{ __('app.filter.placeholder') }}"
                     size="sm"
                     wire:model.live="filteredBy"
                     selected-suffix="{{ __('app.filter.selected') }}"
                     class="flex-1 lg:flex lg:shrink-2"
        >
            @foreach(\App\Enums\FundingStatus::options() as $value => $label)
                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

    </nav>

    <flux:table :paginate="$this->fundings">
        <flux:table.columns>
            <flux:table.column sortable
                               :sorted="$sortBy === 'title'"
                               :direction="$sortDirection"
                               wire:click="sort('title')"
            >{{ __('fundings.index.table.title') }}</flux:table.column>

            <flux:table.column sortable
                               :sorted="$sortBy === 'funder'"
                               :direction="$sortDirection"
                               wire:click="sort('funder')"
                               class="hidden sm:table-cell"
            >{{ __('fundings.index.table.funder') }}</flux:table.column>

            <flux:table.column sortable
                               :sorted="$sortBy === 'status'"
                               :direction="$sortDirection"
                               wire:click="sort('status')"
            >{{ __('fundings.index.table.status') }}</flux:table.column>

            <flux:table.column sortable
                               :sorted="$sortBy === 'approved_amount'"
                               :direction="$sortDirection"
                               wire:click="sort('approved_amount')"
                               class="hidden lg:table-cell"
            >{{ __('fundings.index.table.approved_amount') }}</flux:table.column>

            <flux:table.column sortable
                               :sorted="$sortBy === 'funding_period_start'"
                               :direction="$sortDirection"
                               wire:click="sort('funding_period_start')"
                               class="hidden lg:table-cell"
            >{{ __('fundings.index.table.period') }}</flux:table.column>

            <flux:table.column class="hidden sm:table-cell">
                {{ __('fundings.index.table.projects') }}
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($this->fundings as $funding)
                <flux:table.row :key="$funding->id">

                    <flux:table.cell variant="strong">
                        <a class="underline text-emerald-600"
                           href="{{ route('funding.show', $funding) }}"
                        >{{ \Illuminate\Support\Str::limit($funding->title, 45, preserveWords: true) }}</a>
                        @if($funding->reference)
                            <div class="text-xs text-gray-400 mt-0.5">{{ $funding->reference }}</div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="hidden sm:table-cell">
                        {{ $funding->funder }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $funding->status->color() }}"
                        >{{ $funding->status->label() }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="hidden lg:table-cell">
                        @if($funding->approved_amount)
                            <span class="font-medium text-green-700">
                                {{ number_format($funding->approved_amount / 100, 2, ',', '.') }} €
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="hidden lg:table-cell">
                        @if($funding->funding_period_start)
                            <span class="text-sm">
                                {{ $funding->funding_period_start->isoFormat('DD.MM.YY') }}
                                @if($funding->funding_period_end)
                                    – {{ $funding->funding_period_end->isoFormat('DD.MM.YY') }}
                                @else
                                    <span class="text-gray-400">– {{ __('fundings.index.ongoing') }}</span>
                                @endif
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="hidden sm:table-cell">
                        <flux:badge size="sm"
                                    color="{{ $funding->projects_count > 0 ? 'lime' : 'zinc' }}"
                        >{{ $funding->projects_count }}</flux:badge>
                    </flux:table.cell>

                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

</div>