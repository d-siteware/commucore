
<div class="space-y-8">

    <div>
        <flux:heading size="xl">{{ __('members.export.title') }}</flux:heading>
        <flux:subheading>{{ __('members.export.description') }}</flux:subheading>
    </div>

    {{-- Export Typ --}}
    <flux:radio.group
            wire:model.live="exportType"
            :label="__('members.export.type_label')"
            variant="cards"
    >
        @foreach($this->exportTypes() as $type)
            <flux:radio
                    value="{{ $type->value }}"
                    :label="$type->label()"
                    :description="$type->description()"
            />
        @endforeach
    </flux:radio.group>

    {{-- Filter --}}
    <div class="grid md:grid-cols-2 gap-6">

        <flux:fieldset>
            <flux:legend>{{ __('members.export.filter_label') }}</flux:legend>
            <div class="space-y-3">
                <flux:checkbox
                        wire:model.live="onlyActive"
                        :label="__('members.export.filter.only_active')"
                />
                <flux:checkbox
                        wire:model.live="includePs"
                        :label="__('members.export.filter.include_pseudonymized')"
                />
            </div>
        </flux:fieldset>

        <flux:fieldset>
            <flux:legend>{{ __('members.export.filter.member_types') }}</flux:legend>
            <flux:checkbox.group wire:model.live="memberTypes">
                @foreach($this->memberTypeOptions() as $type)
                    <flux:checkbox
                            value="{{ $type->value }}"
                            :label="$type->label()"
                    />
                @endforeach
            </flux:checkbox.group>
        </flux:fieldset>

    </div>

    {{-- Preview Count --}}
    <flux:callout
            icon="users"
            :color="$previewCount > 0 ? 'green' : 'yellow'"
    >
        <flux:callout.heading>
            {{ $previewCount }} {{ __('members.export.preview_count') }}
        </flux:callout.heading>
    </flux:callout>

    {{-- Download --}}
    @if($previewCount > 0)
        <a href="{{ route('backend.members.export.download', $this->exportParams()) }}">
            <flux:button variant="primary" icon="arrow-down-tray">
                {{ __('members.export.btn_download') }}
                ({{ $this->currentExportTypeLabel() }})
            </flux:button>
        </a>
    @else
        <flux:button variant="primary" icon="arrow-down-tray" disabled>
            {{ __('members.export.btn_download_empty') }}
        </flux:button>
    @endif

</div>