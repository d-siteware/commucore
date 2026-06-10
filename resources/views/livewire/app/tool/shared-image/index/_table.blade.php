<flux:table :paginate="$images">
    <flux:table.columns>
        <flux:table.column>{{ __('shared_image.table.preview') }}</flux:table.column>
        <flux:table.column>{{ __('shared_image.table.description') }}</flux:table.column>
        <flux:table.column class="hidden lg:table-cell">{{ __('shared_image.table.author') }}</flux:table.column>
        <flux:table.column class="hidden lg:table-cell">{{ __('shared_image.table.uploaded_at') }}</flux:table.column>
        <flux:table.column>{{ __('shared_image.table.status') }}</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @forelse($images as $image)
            <flux:table.row :key="$image->id">
                <flux:table.cell>
                    @if($image->thumbnail_path)
                        <img
                                src="{{ route('secure-image.category', ['filename' => basename($image->thumbnail_path), 'category' => 'shared-images/thumbs']) }}"
                                alt="Thumbnail"
                                class="w-16 h-16 object-cover rounded"
                        />
                    @else
                        <span class="text-sm text-gray-400">{{ __('shared_image.table.no_preview') }}</span>
                    @endif
                </flux:table.cell>

                <flux:table.cell>
                    <span class="hidden lg:inline">{{ $image->label }}</span>
                    <aside class="lg:hidden">
                        <flux:text class="text-wrap">{{ $image->label }}</flux:text>
                        <flux:text>{{ $image->author }}</flux:text>
                        <flux:text>{{ $image->created_at->format('d.m.Y H:i') }}</flux:text>
                    </aside>
                </flux:table.cell>

                <flux:table.cell class="hidden lg:table-cell">{{ $image->author }}</flux:table.cell>
                <flux:table.cell class="hidden lg:table-cell">{{ $image->created_at->format('d.m.Y H:i') }}</flux:table.cell>

                <flux:table.cell>
                    @if(auth()->user()->isBoardMember())
                        <flux:dropdown>
                            <flux:button
                                    icon="ellipsis-horizontal"
                                    size="sm"
                                    variant="subtle"
                                    inset="top bottom"
                                    aria-label="{{ __('shared_image.table.options_menu') }}"
                            />
                            <flux:menu>
                                @if(! $image->is_approved)
                                    @can('update', $image)
                                        <flux:menu.item wire:click="approveImage({{ $image->id }})">
                                            {{ __('shared_image.table.approve') }}
                                        </flux:menu.item>
                                    @endcan
                                @else
                                    <flux:menu.item icon="check-circle" disabled>
                                        <span class="text-green-700">{{ __('shared_image.table.approved') }}</span>
                                    </flux:menu.item>
                                @endif

                                <flux:menu.item icon="arrow-down-tray" wire:click="downloadImage({{ $image->id }})">
                                    {{ __('common.download') }}
                                </flux:menu.item>

                                <flux:menu.separator/>

                                @can('delete', $image)
                                    <flux:menu.item
                                            variant="danger"
                                            icon="trash"
                                            wire:click="deleteImage({{ $image->id }})"
                                    >{{ __('common.delete') }}</flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    @else
                        @if(! $image->is_approved)
                            <span class="text-xs text-amber-600">{{ __('shared_image.table.pending') }}</span>
                        @else
                            <span class="text-xs text-green-600">{{ __('shared_image.table.approved') }}</span>
                        @endif
                    @endif
                </flux:table.cell>
            </flux:table.row>
        @empty
            <flux:table.row>
                <flux:table.cell colspan="5">{{ __('shared_image.table.empty') }}</flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>