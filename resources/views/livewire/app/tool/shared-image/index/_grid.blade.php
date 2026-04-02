<section>
    @if($images->total() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($images as $image)
                <flux:card class="p-2" wire:key="image-{{ $image->id }}">
                    <div class="aspect-video overflow-hidden rounded-xl">
                        @if($image->thumbnail_path)
                            <img
                                    src="{{ route('secure-image.category', ['category' => 'shared-images/thumbs', 'filename' => basename($image->thumbnail_path)]) }}"
                                    alt="{{ $image->label }}"
                                    class="w-full h-full object-contain"
                            />
                        @else
                            <div class="w-full h-full flex items-center justify-center text-sm text-gray-400">
                                Keine Vorschau
                            </div>
                        @endif
                    </div>

                    <div class="mt-2 space-y-1">
                        <div class="font-medium text-sm truncate">{{ $image->label }}</div>
                        <div class="text-xs text-gray-500">von {{ $image->author }}</div>
                        <div class="text-xs text-gray-500">{{ $image->created_at->format('d.m.Y H:i') }}</div>

                        @if(! $image->is_approved)
                            <div class="text-xs text-amber-600">Ausstehend</div>
                        @endif

                        @if(auth()->user()->isBoardMember())
                            <aside class="flex justify-between items-center pt-1">
                                @if(! $image->is_approved)
                                    @can('update', $image)
                                        <flux:button
                                                wire:click="approveImage({{ $image->id }})"
                                                variant="primary"
                                                size="xs"
                                        >Freigeben</flux:button>
                                    @endcan
                                @else
                                    <span class="text-green-600 text-xs font-semibold">Freigegeben</span>
                                @endif

                                <flux:button
                                        size="xs"
                                        variant="subtle"
                                        icon-trailing="arrow-down-tray"
                                        wire:click="downloadImage({{ $image->id }})"
                                >Download</flux:button>

                                @can('delete', $image)
                                    <flux:button
                                            size="xs"
                                            variant="danger"
                                            wire:click="deleteImage({{ $image->id }})"
                                    >Löschen</flux:button>
                                @endcan
                            </aside>
                        @endif
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $images->links() }}
        </div>
    @else
        <flux:card class="p-2">
            <flux:button href="{{ route('shared-image.create') }}" variant="ghost" class="p-12 w-full">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto size-12 text-gray-400 my-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z"/>
                    </svg>
                    <flux:text class="mb-3">Neues Bild hochladen</flux:text>
                </span>
            </flux:button>
        </flux:card>
    @endif
</section>