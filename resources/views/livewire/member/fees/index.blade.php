<div>
    <div class="mb-6">
        <flux:heading size="lg">Übersicht Mitgliedsbeiträge</flux:heading>
        <flux:subheading>Jahr {{ $selectedYear }}</flux:subheading>
    </div>


    {{-- Filter & Suche --}}
    <div class="mb-6 flex gap-4 items-end">
        <flux:select wire:model.live="selectedYear" label="Jahr" class="w-32">
            @foreach($this->availableYears as $year)
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </flux:select>

        <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Mitglied suchen..."
                icon="magnifying-glass"
                class="flex-1"
        />

        <flux:checkbox variant="buttons" wire:model.live="showInactive" label="Inaktive anzeigen" />
        <flux:button wire:click="exportPdf" variant="outline" icon="document-arrow-down">
            PDF Export
        </flux:button>
        <flux:button wire:click="exportCsv" variant="outline" icon="document-arrow-down">
            CSV Export
        </flux:button>
    </div>

    {{-- Zusammenfassung --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <flux:card>
            <flux:heading size="sm" class="text-zinc-500">Mitglieder</flux:heading>
            <div class="text-2xl font-bold mt-2">{{ $this->summary['total_members'] }}</div>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="text-zinc-500">Bezahlt</flux:heading>
            <div class="text-2xl font-bold text-green-600 mt-2">
                {{ number_format($this->summary['total_paid'] / 100, 2, ',', '.') }} €
            </div>
            <div class="text-sm text-zinc-500">{{ $this->summary['paid_count'] }} Zahlungen</div>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="text-zinc-500">Offen</flux:heading>
            <div class="text-2xl font-bold text-orange-600 mt-2">
                {{ number_format($this->summary['total_pending'] / 100, 2, ',', '.') }} €
            </div>
            <div class="text-sm text-zinc-500">{{ $this->summary['pending_count'] }} Zahlungen</div>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="text-zinc-500">Transaktionen</flux:heading>
            <div class="text-2xl font-bold mt-2">{{ $this->summary['total_transactions'] }}</div>
        </flux:card>
    </div>

    {{-- Konsolidierte Tabelle: 1 Zeile pro Mitglied --}}
    <flux:table :paginate="$this->member_payments">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'member.name' ? $sortDirection : null">
                Mitglied
            </flux:table.column>
            <flux:table.column>Typ</flux:table.column>
            <flux:table.column>Datum</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'total_paid' ? $sortDirection : null">
                Bezahlt
            </flux:table.column>
            <flux:table.column>Offen</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Beleg</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($this->member_payments as $item)
                <flux:table.row :key="$item->member_id">
                    <flux:table.cell>
                        <a href="{{ route('backend.members.show', $item->member) }}" class="text-blue-600 hover:underline font-medium">
                            {{ $item->member->fullName() }}
                        </a>
                        @if($item->transaction_count > 1)
                            <div class="text-xs text-zinc-500">
                                {{ $item->transaction_count }} Zahlungen
                            </div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge>{{ $item->member->type }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $item->latest_transaction?->transaction?->date?->format('d.m.Y') ?? '-' }}
                    </flux:table.cell>

                    <flux:table.cell class="font-medium text-green-600">
                        {{ number_format($item->total_paid / 100, 2, ',', '.') }} €
                    </flux:table.cell>

                    <flux:table.cell class="text-orange-600">
                        @if($item->total_pending > 0)
                            {{ number_format($item->total_pending / 100, 2, ',', '.') }} €
                        @else
                            -
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($item->has_paid)
                            <flux:badge color="lime">gebucht</flux:badge>
                        @else
                            <flux:badge color="gray">eingereicht</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($item->latest_transaction?->receipt_sent_timestamp)
                            <span class="text-green-600 text-sm">
                                ✓ {{ $item->latest_transaction->receipt_sent_timestamp->format('d.m.Y') }}
                            </span>
                        @else
                            <flux:button size="sm" variant="ghost">
                                Senden
                            </flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>