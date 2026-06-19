@props(['receipt'])

<flux:card wire:key="{{ $receipt->id }}">
    <div x-data="{ loaded: false }" class="flex flex-col gap-2">

        {{-- Vorschau nur für Bilder und PDFs --}}
        @if(str_contains($receipt->mime_type, 'image') || $receipt->mime_type === 'application/pdf')
            <div class="w-full rounded-t-lg relative min-h-[200px]">

                <div x-show="!loaded"
                     class="absolute inset-0 flex items-center justify-center bg-gray-100 animate-pulse z-10"
                >
                    <svg class="w-8 h-8 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                </div>

                <img src="{{ route('document.preview', $receipt->uuid) }}"
                     alt="{{ $receipt->original_name }}"
                     class="w-full h-full object-cover transition-opacity duration-300 ease-in-out"
                     loading="lazy"
                     @load="loaded = true"
                     :class="{ 'opacity-0': !loaded, 'opacity-100': loaded }"
                />
            </div>
        @else
            {{-- Kein Bild-Preview möglich – Icon anzeigen --}}
            <div class="w-full rounded-t-lg min-h-[120px] flex items-center justify-center bg-gray-50">
                <flux:icon :name="$receipt->icon()" class="size-12 text-gray-400"/>
            </div>
        @endif

        <div class="flex flex-col space-y-1.5 p-1">

            {{-- Zugehörige Transaction --}}
            @if($receipt->documentable)
                <flux:text size="xs" class="text-zinc-400 truncate">
                    {{ $receipt->documentable->label ?? __('transactions.documents.no_label') }}
                    · {{ \App\Helpers\DateHelper::formatDate($receipt->documentable->date) }}
                </flux:text>
            @endif

            {{-- Kategorie-Badge --}}
            @if($receipt->category)
                <flux:badge size="sm" color="zinc">
                    {{ \App\Enums\TransactionDocumentCategory::from($receipt->category)->label() }}
                </flux:badge>
            @endif

            {{-- Label / Dateiname --}}
            <flux:text class="truncate font-medium">
                {{ $receipt->label ?: $receipt->original_name }}
            </flux:text>

            @if($receipt->label)
                <flux:text size="xs" class="text-zinc-400 truncate">
                    {{ $receipt->original_name }}
                </flux:text>
            @endif

            <flux:text size="xs" class="text-zinc-400">
                {{ $receipt->fileSizeForHumans() }}
                · {{ \App\Helpers\DateHelper::formatDate($receipt->created_at) }}
            </flux:text>

            <flux:button icon-trailing="arrow-down-tray"
                         variant="primary"
                         size="sm"
                         :href="route('document.download', $receipt->uuid)"
            >
                {{ __('documents.btn.download') }}
            </flux:button>
        </div>
    </div>
</flux:card>