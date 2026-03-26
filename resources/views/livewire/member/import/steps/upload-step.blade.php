<div class="space-y-6">

    <flux:card>
        <flux:heading size="lg">{{ __('members.import.upload.title') }}</flux:heading>
        <flux:subheading>{{ __('members.import.upload.description') }}</flux:subheading>

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:gap-8">
            <div class="mt-6 space-y-6">

                {{-- Import Typ --}}
                <flux:radio.group
                        wire:model.live="importType"
                        :label="__('members.import.upload.type_label')"
                        variant="cards"
                >
                    <flux:radio
                            value="{{ \App\Enums\MemberExportType::STAMMDATEN->value }}"
                            :label="__('members.export.type.stammdaten')"
                            :description="__('members.export.type.stammdaten_desc')"
                    />
                    <flux:radio
                            value="{{ \App\Enums\MemberExportType::MEMBERS_ALL->value }}"
                            :label="__('members.export.type.members_all')"
                            :description="__('members.export.type.members_all_desc')"
                    />
                    <flux:radio
                            value="{{ \App\Enums\MemberExportType::FULL->value }}"
                            :label="__('members.export.type.full')"
                            :description="__('members.export.type.full_desc')"
                    />
                </flux:radio.group>

                <flux:callout icon="document-arrow-down"
                              color="zinc"
                >
                    <flux:callout.text>
                        {{ __('members.import.upload.template_hint') }}
                        <a
                                href="{{ route('backend.members.import.template', ['type' => $importType]) }}"
                                class="underline font-medium"
                        >
                            {{ __('members.import.upload.template_download') }}
                        </a>
                    </flux:callout.text>
                </flux:callout>
            </div>

            <div class="mt-6 space-y-6">
                {{-- CSV Upload --}}
                @if($importType !== \App\Enums\MemberExportType::FULL->value)
                    <flux:file-upload
                            wire:model="file"
                            :label="__('members.import.upload.file_label_csv')"
                            accept=".csv, .zip"
                    >
                        <flux:file-upload.dropzone
                                :heading="__('members.import.upload.dropzone_heading_csv')"
                                text="CSV, max. 10MB"
                                with-progress
                                inline
                        />
                    </flux:file-upload>

                    <div class="mt-3">
                        @if($file)
                            <flux:file-item
                                    :heading="$file->getClientOriginalName()"
                                    :size="$file->getSize()"
                            >
                                <x-slot name="actions">
                                    <flux:file-item.remove
                                            wire:click="$set('file', null)"
                                            aria-label="{{ __('members.import.upload.remove_file') }}"
                                    />
                                </x-slot>
                            </flux:file-item>
                        @endif
                    </div>
                @endif

                {{-- ZIP Upload --}}
                @if($importType === \App\Enums\MemberExportType::FULL->value)
                    <flux:file-upload
                            wire:model="file"
                            :label="__('members.import.upload.file_label_zip')"
                    >
                        <flux:file-upload.dropzone
                                :heading="__('members.import.upload.dropzone_heading_zip')"
                                text="ZIP, max. 50MB"
                                with-progress
                                inline
                        />
                    </flux:file-upload>

                    <div class="mt-3">
                        @if($file)
                            <flux:file-item
                                    :heading="$file->getClientOriginalName()"
                                    :size="$file->getSize()"
                                    icon="archive-box"
                            >
                                <x-slot name="actions">
                                    <flux:file-item.remove
                                            wire:click="$set('file', null)"
                                            aria-label="{{ __('members.import.upload.remove_file') }}"
                                    />
                                </x-slot>
                            </flux:file-item>
                        @endif
                    </div>

                    <flux:callout icon="shield-check"
                                  color="indigo"
                    >
                        <flux:callout.text>{{ __('members.import.upload.zip_hint') }}</flux:callout.text>
                    </flux:callout>

                    <flux:callout icon="clock"
                                  color="yellow"
                    >
                        <flux:callout.text>{{ __('members.import.upload.zip_async_hint') }}</flux:callout.text>
                    </flux:callout>
                @endif

                {{-- Fehler --}}
                @if($errorMessage)
                    <flux:callout icon="exclamation-triangle"
                                  color="red"
                    >
                        <flux:callout.heading>{{ __('members.import.upload.error_heading') }}</flux:callout.heading>
                        <flux:callout.text>{{ $errorMessage }}</flux:callout.text>
                    </flux:callout>
                @endif

                {{-- ZIP Job gestartet --}}
                @if($zipJobDispatched)
                    <flux:callout icon="check-circle"
                                  color="green"
                    >
                        <flux:callout.heading>{{ __('members.import.upload.zip_job_dispatched') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('members.import.upload.zip_job_description') }}</flux:callout.text>
                    </flux:callout>
                @else
                    <flux:button
                            wire:click="processFile"
                            wire:loading.attr="disabled"
                            :disabled="! $file"
                            variant="primary"
                            icon="arrow-up-tray"
                    >
                        <span wire:loading.remove>{{ __('members.import.upload.btn_upload') }}</span>
                        <span wire:loading>{{ __('members.import.upload.btn_uploading') }}</span>
                    </flux:button>
                @endif

            </div>
        </section>

    </flux:card>

</div>