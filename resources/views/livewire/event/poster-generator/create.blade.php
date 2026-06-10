<div class="grid gap-6 grid-cols-1 lg:grid-cols-2 ">

    <section class="space-y-6">
    {{-- Options --}}
    <flux:card class="p-4">
        <div class="flex flex-wrap gap-6 items-start">

            {{-- With image --}}
            <div class="flex flex-col gap-1">
                <flux:label>{{ __('event.poster.option.image') }}</flux:label>
                <flux:switch wire:model.live="withImage" />
            </div>

            {{-- Text mode --}}
            <div class="flex flex-col gap-1">
                <flux:label>{{ __('event.poster.option.text') }}</flux:label>
                <flux:select wire:model.live="textMode" class="w-40" size="sm">
                    <flux:select.option value="excerpt">{{ __('event.poster.option.text_excerpt') }}</flux:select.option>
                    <flux:select.option value="full">{{ __('event.poster.option.text_full') }}</flux:select.option>
                </flux:select>
            </div>

            @if(\App\Models\Locale::isMultiLanguage())
            {{-- Preview locale --}}
            <div class="flex flex-col gap-1">
                <flux:label>{{ __('event.poster.option.preview_locale') }}</flux:label>
                <flux:select wire:model.live="previewLocale" class="w-24" size="sm">
                    @foreach(\App\Models\Locale::getNames() as $locale)
                        <flux:select.option value="{{ $locale }}">{{ strtoupper($locale) }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            @endif

            <flux:button wire:click="generatePosters" wire:loading.attr="disabled" icon="photo">
                {{ __('event.poster.generate') }}
            </flux:button>

        </div>
    </flux:card>

    {{-- Preview iframe --}}
    <flux:card class="p-0 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-2 border-b border-zinc-200 dark:border-zinc-700">
            <span class="text-sm font-medium text-zinc-500">{{ __('event.poster.preview') }}</span>
        </div>
        <iframe
                src="{{ route('backend.events.poster.preview', [$event, $previewLocale]) }}?image={{ $withImage ? 1 : 0 }}&text={{ $textMode }}"
                class="w-full"
                style="height: 680px;"
                wire:key="preview-{{ $previewLocale }}-{{ $withImage }}-{{ $textMode }}"
        ></iframe>
    </flux:card>
    </section>

<aside class="space-y-6">
    {{-- JPEG previews with delete --}}
    @php $hasAnyJpeg = collect(\App\Enums\Locale::cases())->some(fn($l) => $event->hasPoster($l->value, 'jpg')); @endphp

    @if($hasAnyJpeg)
        <section>
            <flux:heading size="sm" class="mb-3">{{ __('event.poster.jpeg_files') }}</flux:heading>
            <div class="flex flex-wrap gap-4">
                @foreach(\App\Models\Locale::getNames() as $locale)
                    @if($event->hasPoster($locale, 'jpg'))
                        <div class="relative w-56">
                            <img
                                    src="{{ $event->getPoster($locale) }}"
                                    alt="Poster {{ $locale }}"
                                    class="w-full rounded border border-zinc-200 dark:border-zinc-700 shadow-sm"
                            >
                            <div class="absolute top-2 right-2 flex gap-1">
                                <flux:badge color="teal" size="sm">{{ strtoupper($locale) }}</flux:badge>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <flux:button
                                        wire:click="deletePoster('{{ $locale }}', 'jpg')"
                                        wire:confirm="{{ __('event.poster.confirm_delete') }}"
                                        icon="trash"
                                        variant="danger"
                                        size="sm"
                                />
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    {{-- PDF downloads with delete --}}
    @php $hasAnyPdf = collect(\App\Enums\Locale::cases())->some(fn($l) => $event->hasPoster($l->value, 'pdf')); @endphp

    @if($hasAnyPdf)
        <section>
            <flux:heading size="sm" class="mb-3">{{ __('event.poster.pdf_files') }}</flux:heading>
            <div class="flex flex-wrap gap-3">
                @foreach(\App\Models\Locale::getNames() as $locale)
                    @if($event->hasPoster($locale, 'pdf'))
                        <div class="flex items-center gap-2">
                            <flux:button
                                    icon-trailing="document-arrow-down"
                                    variant="filled"
                                    href="{{ $event->getPoster($locale, 'pdf') }}"
                                    download=""
                            >
                                PDF – {{ strtoupper($locale) }}
                            </flux:button>
                            <flux:button
                                    wire:click="deletePoster('{{ $locale }}', 'pdf')"
                                    wire:confirm="{{ __('event.poster.confirm_delete') }}"
                                    icon="trash"
                                    variant="danger"
                                    size="sm"
                            />
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
    </aside>
</div>