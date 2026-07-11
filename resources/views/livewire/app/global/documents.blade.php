<div class="space-y-6">

    {{-- Upload-Bereich --}}
    @if($canUpload)
        <div>
            @if(!$showUploadForm)
                <flux:button variant="primary"
                             size="sm"
                             icon="arrow-up-tray"
                             wire:click="$set('showUploadForm', true)"
                >{{ __('documents.btn.upload') }}</flux:button>
            @else
                <flux:card class="space-y-4">
                    <flux:heading size="md">{{ __('documents.upload.title') }}</flux:heading>

                    {{-- Kategorie --}}
                    <flux:select wire:model="category"
                                 variant="listbox"
                                 :label="__('documents.category.label')"
                                 :placeholder="__('documents.category.placeholder')"
                                 clearable
                    >
                        @foreach($categories as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category"/>

                    {{-- Optionale Bezeichnung --}}
                    <flux:input wire:model="label"
                                :label="__('documents.upload.label_field')"
                                :placeholder="__('documents.upload.label_placeholder')"
                    />

                    {{-- Notiz --}}
                    <flux:textarea wire:model="notes"
                                   rows="2"
                                   :label="__('documents.upload.notes_label')"
                    />

                    {{-- Datei-Upload (mehrere) --}}
                    <flux:field>
                        <flux:label>{{ __('documents.upload.file_label') }}</flux:label>
                        <flux:description>{{ __('documents.upload.file_hint') }}</flux:description>
                        <input type="file"
                               wire:model="files"
                               multiple
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.tif,.tiff,.eml"
                               class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-emerald-50 file:text-emerald-700
                                      hover:file:bg-emerald-100"
                        />
                        <flux:error name="files"/>
                        <flux:error name="files.*"/>
                    </flux:field>

                    {{-- Ladeindikator --}}
                    <div wire:loading wire:target="files" class="text-sm text-gray-500">
                        {{ __('documents.upload.loading') }}
                    </div>

                    <div class="flex gap-2">
                        <flux:button variant="primary"
                                     wire:click="storeDocuments"
                                     wire:loading.attr="disabled"
                                     wire:target="storeDocuments"
                        >
                            <span wire:loading.remove wire:target="storeDocuments">
                                {{ __('documents.btn.save') }}
                            </span>
                            <span wire:loading wire:target="storeDocuments">
                                {{ __('documents.btn.saving') }}
                            </span>
                        </flux:button>

                        <flux:button variant="ghost"
                                     wire:click="$set('showUploadForm', false)"
                        >{{ __('documents.btn.cancel') }}</flux:button>
                    </div>
                </flux:card>
            @endif
        </div>
    @endif

    {{-- Dokumentenliste --}}
    @if($documents->isEmpty())
        <flux:card class="text-center py-10 space-y-2">
            <flux:icon.document-text class="mx-auto text-zinc-400"/>
            <flux:text>{{ __('documents.empty') }}</flux:text>
        </flux:card>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('documents.table.name') }}</flux:table.column>
                <flux:table.column class="hidden md:table-cell">{{ __('documents.table.category') }}</flux:table.column>
                <flux:table.column class="hidden lg:table-cell">{{ __('documents.table.size') }}</flux:table.column>
                <flux:table.column class="hidden lg:table-cell">{{ __('documents.table.uploaded_by') }}</flux:table.column>
                <flux:table.column class="hidden xl:table-cell">{{ __('documents.table.last_accessed') }}</flux:table.column>
                <flux:table.column>{{ __('documents.table.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($documents as $document)
                    <flux:table.row :key="$document->id">

                        <flux:table.cell variant="strong">
                            <div class="flex items-center gap-2 w-96">
                                <flux:icon :name="$document->icon()" class="size-4 text-gray-400 shrink-0"/>
                                <div class="flex flex-col min-w-0">
                                    <span class="truncate break-all">
                                        {{ $document->label ?: $document->original_name }}
                                    </span>
                                    @if($document->label)
                                        <flux:text size="xs" class="text-zinc-400 truncate">
                                            {{ $document->original_name }}
                                        </flux:text>
                                    @endif
                                    @if($document->notes)
                                        <flux:text size="xs" class="text-zinc-400 truncate">
                                            {{ $document->notes }}
                                        </flux:text>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="hidden md:table-cell">
                            @if($document->category)
                                <flux:badge size="sm" color="zinc">
                                    {{ $document->category }}
                                </flux:badge>
                            @else
                                <span class="text-gray-400">–</span>
                            @endif
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
                                <flux:button size="xs"
                                             icon="arrow-down-tray"
                                             wire:click="download({{ $document->id }})"
                                             wire:loading.attr="disabled"
                                             wire:target="download({{ $document->id }})"
                                >
                                    <span class="hidden lg:flex">{{ __('documents.btn.download') }}</span>
                                </flux:button>

                                @if($canDelete)
                                    <flux:button size="xs"
                                                 variant="danger"
                                                 icon="trash"
                                                 wire:click="delete({{ $document->id }})"
                                                 wire:confirm="{{ __('documents.confirm.delete') }}"
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