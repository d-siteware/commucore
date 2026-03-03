<div x-data="{ maxLength: {{ $maxLength }} }">
    @if($badge)
    <flux:label badge="{{ $badge }}">{{ $label??'' }}</flux:label>
    @else
    <flux:label>{{ $label??'' }}</flux:label>
    @endif
    <flux:input
            wire:model="{{ $model }}"
            size="{{ $size }}"
            x-bind:maxlength="maxLength"
    />
    <flux:text
            class="ml-2"
            size="sm"
            x-text="`${maxLength - ($wire.{{ $model }} || '').length}/${maxLength}`"
            x-bind:class="{
            'text-emerald-600': (maxLength - ($wire.{{ $model }} || '').length) > 50,
            'text-yellow-500': (maxLength - ($wire.{{ $model }} || '').length) <= 50 && (maxLength - ($wire.{{ $model }} || '').length) > 0,
            'text-red-500': (maxLength - ($wire.{{ $model }} || '').length) <= 0
        }"
    ></flux:text>
</div>
