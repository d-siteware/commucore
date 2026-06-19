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
                        label="{{ __('event.title.de') }}"
                        badge="{{ $locale }}"
                />
            @else
                <x-input-with-counter
                        model="form.title.{{ $locale }}"
                        max-length="60"
                        label="{{ __('event.title.de') }}"
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
            <flux:editor wire:model="form.description.{{$locale}}" description="{{ __('event.editor_description') }}"/>
        </flux:field>

    </flux:card>
    <flux:card class="space-y-6">

        <flux:field>
            @if($multiLang)
                <flux:label badge="{{ $locale }}">{{ __('post.slug') }}</flux:label>
            @else
                <flux:label>{{ __('post.slug') }}</flux:label>
            @endif
            <flux:input wire:model="form.slug.{{$locale}}"
                        description="{{ __('event.create.slug.notice') }}"
            />
{{--            <flux:error name="form.slug.{{$locale}}"/>--}}

        </flux:field>

        <flux:field>
            @if($multiLang)
            <flux:label badge="{{ $locale }}">{{ __('event.excerpt_label') }}</flux:label>
            @else
            <flux:label>{{ __('event.excerpt_label') }}</flux:label>
            @endif
            <flux:editor class="**:data-[slot=content]:min-h-[100px]"
                         wire:model="form.excerpt.{{$locale}}"
                         description="{{ __('event.backend.texts.excerpt_description') }}"
            />
        </flux:field>

    </flux:card>
</section>