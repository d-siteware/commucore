@props([
    'locale'=>'de',
    'multiLang' => true
    ])
<section class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-3">
    <flux:card class="space-y-6">

        <flux:field>
            @if($multiLang)
                <x-input-with-counter
                        model="form.title.{{ $locale }}"
                        max-length="60"
                        label="Titel"
                        badge="{{ $locale }}"
                />
            @else
                <x-input-with-counter
                        model="form.title.{{ $locale }}"
                        max-length="60"
                        label="Titel"
                />
            @endif


            <flux:error name="form.title"/>
        </flux:field>

        <flux:field>
            @if($multiLang)
                <flux:label badge="{{ $locale }}">{{ __('event.form.content') }}</flux:label>
            @else
                <flux:label>{{ __('event.form.content') }}</flux:label>
            @endif
            <flux:editor wire:model="form.description.{{$locale}}" description="Texte mit Markdown Funktionen formatieren"/>
        </flux:field>

    </flux:card>
    <flux:card class="space-y-6">

        <flux:field>
            @if($multiLang)
                <flux:label badge="{{ $locale }}">Slug</flux:label>
            @else
                <flux:label>Slug</flux:label>
            @endif
            <flux:input wire:model="form.slug.{{$locale}}"
                        description="{{ __('event.create.slug.notice') }}"
            />
{{--            <flux:error name="form.slug.{{$locale}}"/>--}}

        </flux:field>

        <flux:field>
            @if($multiLang)
            <flux:label badge="{{ $locale }}">Text Auszug</flux:label>
            @else
            <flux:label>Text Auszug</flux:label>
            @endif
            <flux:editor class="**:data-[slot=content]:min-h-[100px]"
                         wire:model="form.excerpt.{{$locale}}"
                         description="Wird für die Vorschau verwendet. Bitte max 200 Zeichen"
            />
        </flux:field>

    </flux:card>
</section>