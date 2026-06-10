@props([
    'locale'=>'de',
    'multiLang' => true
    ])
<section class="space-y-3">
    <flux:field>
        @if($multiLang)
            <flux:label badge="{{ $locale }}">{{ __('mails.members.subject') }}</flux:label>
        @else
            <flux:label>{{ __('mails.members.subject') }}</flux:label>
        @endif
        <flux:input wire:model="subject.{{ $locale }}"/>
        <flux:error name="subject.{{ $locale }}"/>
    </flux:field>

    <flux:field>
        @if($multiLang)
        <flux:label badge="{{ $locale }}">{{ __('mails.members.message') }}</flux:label>
        @else
        <flux:label>{{ __('mails.members.message') }}</flux:label>
        @endif
        <flux:textarea rows="auto"
                       wire:model="message.{{ $locale }}"
        />
        <flux:error name="message.{{ $locale }}" />
    </flux:field>

    <flux:field x-show="$wire.setLink">
        @if($multiLang)
        <flux:label badge="{{ $locale }}">{{ __('mails.members.label') }}</flux:label>
        @else
        <flux:label>{{ __('mails.members.label') }}</flux:label>
        @endif
            <flux:input wire:model="urlLabel.{{ $locale }}"/>
        <flux:error name="urlLabel.{{ $locale }}" />
    </flux:field>


    <flux:field class="flex-col flex"
                x-show="$wire.setAttachment"
    >
        <flux:label>Angehängte Datei</flux:label>
        <input type="file"
               wire:model="attachments.{{ $locale }}"
               accept=".pdf,.jpg,.jpeg,.png,.tif"
               class="border border-zinc-300 p-1.5 rounded shadow-sm"
        >
        <flux:error name="attachments.de"/>

    </flux:field>

</section>