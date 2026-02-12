<div>
    <div class="mb-6">
        <flux:heading size="lg">{{ __('members.fees.overview_title') }}</flux:heading>
        <flux:subheading>{{ __('members.fees.year') }} {{ $selectedYear }}</flux:subheading>
    </div>


    {{-- Filter & Suche --}}
    <div class="mb-6 flex flex-wrap gap-4 items-end">

        <flux:select wire:model.live="selectedYear" label="{{ __('members.fees.year') }}" class="w-32">
            @foreach($this->availableYears as $year)
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </flux:select>

        <flux:input wire:model.live.debounce.300ms="search"
                placeholder="{{ __('members.fees.search_member_placeholder') }}"
                icon="magnifying-glass"
                class="flex-1"
        />

        <flux:checkbox variant="buttons" wire:model.live="showInactive" label="{{ __('members.fees.show_inactive') }}" />

        <flux:button wire:click="exportPdf" variant="outline" icon="document-arrow-down">
            {{ __('members.fees.pdf_export') }}
        </flux:button>

        <flux:button wire:click="exportCsv" variant="outline" icon="document-arrow-down">
            {{ __('members.fees.csv_export') }}
        </flux:button>

    </div>

    {{-- Zusammenfassung --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <flux:card>
            <flux:heading size="sm" class="text-zinc-500">{{ __('members.fees.members') }}</flux:heading>
            <div class="text-2xl font-bold mt-2">{{ $this->summary['total_members'] }}</div>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="text-zinc-500">{{ __('members.fees.paid') }}</flux:heading>
            <div class="text-2xl font-bold text-green-600 mt-2">
                {{ number_format($this->summary['total_paid'] / 100, 2, ',', '.') }} €
            </div>
            <div class="text-sm text-zinc-500">{{ $this->summary['paid_count'] }} {{ __('members.fees.payments') }}</div>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="text-zinc-500">{{ __('members.fees.open') }}</flux:heading>
            <div class="text-2xl font-bold text-orange-600 mt-2">
                {{ number_format($this->summary['total_pending'] / 100, 2, ',', '.') }} €
            </div>
            <div class="text-sm text-zinc-500">{{ $this->summary['pending_count'] }} {{ __('members.fees.payments') }}</div>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="text-zinc-500">{{ __('members.fees.transactions') }}</flux:heading>
            <div class="text-2xl font-bold mt-2">{{ $this->summary['total_transactions'] }}</div>
        </flux:card>
    </div>

    {{-- Konsolidierte Tabelle: 1 Zeile pro Mitglied --}}
    <flux:table :paginate="$this->member_payments">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'member.name' ? $sortDirection : null">
                {{ __('members.fees.member') }}
            </flux:table.column>
            <flux:table.column class="hidden lg:table-cell">{{ __('members.fees.type') }}</flux:table.column>
            <flux:table.column class="hidden lg:table-cell">{{ __('members.fees.date') }}</flux:table.column>
            <flux:table.column class="hidden lg:table-cell" sortable :sorted="$sortBy === 'total_paid' ? $sortDirection : null">
                {{ __('members.fees.paid') }}
            </flux:table.column>
            <flux:table.column class="hidden lg:table-cell">{{ __('members.fees.open') }}</flux:table.column>
            <flux:table.column class="hidden lg:table-cell">{{ __('members.fees.status') }}</flux:table.column>
            <flux:table.column>{{ __('members.fees.receipt') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($this->member_payments as $item)
                <flux:table.row :key="$item->member_id">
                    <flux:table.cell>
                        <a href="{{ route('backend.members.show', $item->member) }}" class="text-blue-600 hover:underline font-medium ">
                            {{ $item->member->fullName() }}
                        </a>
                        @if($item->transaction_count > 1)
                            <div class="text-xs text-zinc-500">
                                {{ $item->transaction_count }} {{ __('members.fees.payments') }}
                            </div>
                        @endif
                        <aside class="lg:hidden flex flex-col gap-1 mt-2">
                            <flux:badge size="sm"
                                        color="{{ \App\Enums\MemberType::color($item->member->type) }}"
                            >{{ \App\Enums\MemberType::value($item->member->type) }}</flux:badge>
                            <flux:text class="text-sm">
                                <span class="text-positive">
                                    {{ number_format($item->total_paid / 100, 2, ',', '.') }} €
                                </span> /
                                <span class="text-negative">
                                     @if($item->total_pending > 0)
                                        {{ number_format($item->total_pending / 100, 2, ',', '.') }} €
                                    @else
                                        -
                                    @endif
                                </span>

                            </flux:text>
                        </aside>

                    </flux:table.cell>

                    <flux:table.cell class="hidden lg:table-cell">
                        <flux:badge color="{{ \App\Enums\MemberType::color($item->member->type) }}"
                        >{{ \App\Enums\MemberType::value($item->member->type) }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="hidden lg:table-cell">
                        {{ $item->latest_transaction?->transaction?->date?->format('d.m.Y') ?? '-' }}
                    </flux:table.cell>

                    <flux:table.cell class="font-medium text-positive hidden lg:table-cell">
                        {{ number_format($item->total_paid / 100, 2, ',', '.') }} €
                    </flux:table.cell>

                    <flux:table.cell class="text-negative hidden lg:table-cell">
                        @if($item->total_pending > 0)
                            {{ number_format($item->total_pending / 100, 2, ',', '.') }} €
                        @else
                            -
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="hidden lg:table-cell">
                        @if($item->has_paid)
                            <flux:badge color="{{ \App\Enums\TransactionStatus::color(\App\Enums\TransactionStatus::booked->value) }}">{{ __('transaction.status.booked') }}</flux:badge>
                        @else
                            <flux:badge color="{{ \App\Enums\TransactionStatus::color(\App\Enums\TransactionStatus::submitted->value) }}">{{ __('transaction.status.submitted') }}</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell >
                        @if($item->latest_transaction?->receipt_sent_timestamp)
                            <span class="text-accent text-sm">
                                ✓ {{ $item->latest_transaction->receipt_sent_timestamp->format('d.m.Y') }}
                            </span>
                        @else
                            <flux:button size="sm" variant="ghost">
                                {{ __('members.fees.send') }}
                            </flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>