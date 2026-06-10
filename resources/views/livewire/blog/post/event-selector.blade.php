<div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
    <flux:select :placeholder="__('event.select.placeholder')"
        variant="combobox"
                 wire:model="eventlist"
    >
        <x-slot name="input">
            <flux:select.input wire:model.live="search" :invalid="$errors->has('...')" />
        </x-slot>

        @forelse($this->events as $event)
            <flux:select.option value="{{ $event->id }}">{{ $event->name }}</flux:select.option>
        @empty
            <flux:select.option>{{ __('event.select.empty') }}</flux:select.option>
        @endforelse
    </flux:select>

    <flux:button variant="primary"
                 wire:click="connectPostToEvent"
    >{{ __('post.show.tab.main.event.btn_connect_now') }}</flux:button>

</div>
