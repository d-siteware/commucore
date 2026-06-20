<div>
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('venue.tool.heading') }}</flux:heading>

        <flux:button variant="primary" icon="plus" wire:click="create">
            {{ __('venue.tool.create') }}
        </flux:button>
    </div>

    <div class="mb-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            placeholder="{{ __('venue.tool.search_placeholder') }}"
            clearable
        />
    </div>

    <flux:table :paginate="$this->venues">
        <flux:table.columns>
            <flux:table.column
                sortable
                :sorted="$sortField === 'name'"
                :direction="$sortDirection"
                wire:click="sortBy('name')"
            >
                {{ __('venue.name') }}
            </flux:table.column>
            <flux:table.column>{{ __('venue.address') }}</flux:table.column>
            <flux:table.column>{{ __('venue.city') }}</flux:table.column>
            <flux:table.column>{{ __('venue.phone') }}</flux:table.column>
            <flux:table.column>{{ __('venue.tool.events_count') }}</flux:table.column>
            <flux:table.column>{{ __('venue.tool.actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->venues as $venue)
                <flux:table.row :key="$venue->id">
                    <flux:table.cell class="font-medium">{{ $venue->name }}</flux:table.cell>
                    <flux:table.cell>{{ $venue->address }}</flux:table.cell>
                    <flux:table.cell>{{ $venue->city }}</flux:table.cell>
                    <flux:table.cell>{{ $venue->phone }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($venue->events_count > 0)
                            <flux:badge size="sm" color="zinc">{{ $venue->events_count }}</flux:badge>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="pencil"
                                wire:click="edit({{ $venue->id }})"
                            >
                                {{ __('venue.tool.edit') }}
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="trash"
                                wire:click="confirmDelete({{ $venue->id }})"
                            >
                                {{ __('venue.tool.delete') }}
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center text-zinc-400 py-8">
                        {{ __('venue.tool.empty') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Löschbestätigung --}}
    <flux:modal name="venue-delete-confirm" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('venue.tool.delete_confirm.heading') }}</flux:heading>

            @if ($this->pendingDeleteEventsCount > 0)
                <flux:callout variant="warning" icon="exclamation-triangle">
                    <flux:callout.heading>
                        {{ __('venue.tool.delete_confirm.in_use_heading') }}
                    </flux:callout.heading>
                    <flux:callout.text>
                        {{ trans_choice(
                            'venue.tool.delete_confirm.in_use_text',
                            $this->pendingDeleteEventsCount,
                            ['count' => $this->pendingDeleteEventsCount, 'name' => $this->pendingDeleteVenueName]
                        ) }}
                    </flux:callout.text>
                </flux:callout>
            @else
                <p class="text-sm text-zinc-600 dark:text-zinc-300">
                    {{ __('venue.tool.delete_confirm.text', ['name' => $this->pendingDeleteVenueName]) }}
                </p>
            @endif

            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="ghost" wire:click="cancelDelete">
                    {{ __('venue.tool.delete_confirm.cancel') }}
                </flux:button>
                <flux:button variant="danger" wire:click="delete">
                    {{ __('venue.tool.delete_confirm.confirm') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Bestehendes Create/Edit-Modal wird unverändert wiederverwendet --}}
    <livewire:app.global.venue.modal />
</div>
