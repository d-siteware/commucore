<div class="space-y-6">

    {{-- Upload-Bereich --}}
    @if($canUpload)
        <div>
            @if(! $showUploadForm)
                <flux:button
                        variant="primary"
                        size="sm"
                        icon="arrow-up-tray"
                        wire:click="$set('showUploadForm', true)"
                >{{ __('members.documents.btn.upload') }}</flux:button>
            @else
                <flux:card class="space-y-4">
                    <flux:heading size="md">{{ __('members.documents.upload.title') }}</flux:heading>

                    <flux:select
                            wire:model="category"
                            :label="__('members.documents.category.label')"
                            :placeholder="__('members.documents.category.placeholder')"
                    >
                        @foreach($categories as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error for="category" />

                    <flux:input
                            type="file"
                            wire:model="file"
                            :label="__('members.documents.upload.file_label')"
                            accept=".pdf,.jpg,.jpeg,.png,.tif,.tiff"
                    />
                    <flux:error for="file" />

                    <flux:textarea
                            wire:model="notes"
                            rows="2"
                            :label="__('members.documents.upload.notes_label')"
                    />

                    <div class="flex gap-2">
                        <flux:button
                                variant="primary"
                                wire:click="storeDocument"
                                wire:loading.attr="disabled"
                                wire:target="upload"
                        >
                            <span wire:loading.remove wire:target="upload">
                                {{ __('members.documents.btn.save') }}
                            </span>
                            <span wire:loading wire:target="upload">
                                {{ __('members.documents.btn.upload') }}
                            </span>
                        </flux:button>

                        <flux:button
                                variant="ghost"
                                wire:click="$set('showUploadForm', false)"
                        >{{ __('members.documents.btn.cancel') }}</flux:button>
                    </div>
                </flux:card>
            @endif
        </div>
    @endif

    {{-- Dokumentenliste --}}
    @if($documents->isEmpty())
        <flux:card class="text-center py-10 space-y-2">
            <flux:icon.document-text class="mx-auto text-zinc-400" />
            <flux:text>{{ __('members.documents.empty') }}</flux:text>
        </flux:card>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('members.documents.table.name') }}</flux:table.column>
                <flux:table.column class="hidden md:table-cell">
                    {{ __('members.documents.table.category') }}
                </flux:table.column>
                <flux:table.column class="hidden lg:table-cell">
                    {{ __('members.documents.table.size') }}
                </flux:table.column>
                <flux:table.column class="hidden lg:table-cell">
                    {{ __('members.documents.table.uploaded_by') }}
                </flux:table.column>
                <flux:table.column class="hidden xl:table-cell">
                    {{ __('members.documents.table.last_accessed') }}
                </flux:table.column>
                <flux:table.column>{{ __('members.documents.table.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($documents as $document)
                    <flux:table.row :key="$document->id">

                        <flux:table.cell variant="strong">
                            <div class="flex flex-col">
                                <span>{{ $document->original_name }}</span>
                                @if($document->notes)
                                    <flux:text size="xs" class="text-zinc-400">
                                        {{ $document->notes }}
                                    </flux:text>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="hidden md:table-cell">
                            <flux:badge size="sm" color="zinc">
                                {{ $document->category->label() }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell class="hidden lg:table-cell">
                            {{ $document->fileSizeForHumans() }}
                        </flux:table.cell>

                        <flux:table.cell class="hidden lg:table-cell">
                            <flux:tooltip :content="$document->created_at->format('d.m.Y H:i')" position="top">
                                <span>{{ $document->uploadedBy->name }}</span>
                            </flux:tooltip>
                        </flux:table.cell>

                        <flux:table.cell class="hidden xl:table-cell">
                            @if($document->last_accessed_at)
                                <flux:tooltip :content="$document->lastAccessedBy?->name" position="top">
                                    <span>{{ $document->last_accessed_at->diffForHumans() }}</span>
                                </flux:tooltip>
                            @else
                                <flux:text class="text-zinc-400">–</flux:text>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button
                                        size="xs"
                                        icon="arrow-down-tray"
                                        wire:click="download({{ $document->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="download({{ $document->id }})"
                                >
                                    <span class="hidden lg:flex">{{ __('members.documents.btn.download') }}</span>
                                </flux:button>

                                @if($canDelete)
                                    <flux:button
                                            size="xs"
                                            variant="danger"
                                            icon="trash"
                                            wire:click="delete({{ $document->id }})"

                                    />
                                @endif
                            </div>
                        </flux:table.cell>

                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif

</div>