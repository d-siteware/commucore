<div class="space-y-6">
    <flux:card>
        <flux:heading size="lg">{{ __('members.import.mapping.title') }}</flux:heading>
        <flux:subheading>{{ __('members.import.mapping.description') }}</flux:subheading>

        <div class="mt-6 space-y-4">

            {{-- Feld-Zuordnung --}}
            <div class="grid grid-cols-2 gap-4 font-medium text-sm text-slate-500 px-2">
                <span>{{ __('members.import.mapping.col_csv') }}</span>
                <span>{{ __('members.import.mapping.col_commucore') }}</span>
            </div>

            @foreach($csvHeaders as $header)
                <div class="grid grid-cols-2 gap-4 items-center p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">

                    {{-- CSV Header --}}
                    <div class="flex items-center gap-2">
                        <flux:badge color="{{ isset($fieldMap[$header]) && $fieldMap[$header] !== '' ? 'green' : 'yellow' }}">
                            {{ $header }}
                        </flux:badge>
                    </div>

                    {{-- CommuCore Feld Select --}}
                    <flux:select
                            wire:model.live="fieldMap.{{ $header }}"
                            size="sm"
                    >
                        @foreach($this->commuCoreFieldOptions() as $value => $label)
                            <flux:select.option
                                    value="{{ $value }}"
                                    :disabled="$value !== '' && $value !== ($fieldMap[$header] ?? '') && in_array($value, array_values($fieldMap))"
                            >
                                {{ $label }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                </div>
            @endforeach

            {{-- Fortschritt --}}
            @php
                $mapped = count(array_filter($fieldMap, fn($v) => $v !== ''));
                $total  = count($csvHeaders);
            @endphp

            <flux:callout
                    icon="{{ $mapped === $total ? 'check-circle' : 'information-circle' }}"
                    color="{{ $mapped === $total ? 'green' : 'indigo' }}"
            >
                <flux:callout.text>
                    {{ $mapped }}/{{ $total }} {{ __('members.import.mapping.fields_mapped') }}
                </flux:callout.text>
            </flux:callout>

            <div class="flex gap-3">
                <flux:button wire:click="$dispatch('previous-step')" variant="ghost" icon="arrow-left">
                    {{ __('members.import.btn_back') }}
                </flux:button>
                <flux:button
                        wire:click="confirmMapping"
                        variant="primary"
                        icon-trailing="arrow-right"
                >
                    {{ __('members.import.mapping.btn_confirm') }}
                </flux:button>
            </div>

        </div>
    </flux:card>

    {{-- Enum-Mapping Modal --}}
    <flux:modal wire:model="showEnumModal" class="max-w-2xl">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('members.import.mapping.enum_modal_title') }}</flux:heading>
            <flux:text>{{ __('members.import.mapping.enum_modal_description') }}</flux:text>

            @foreach($unknownEnumValues as $field => $unknownValues)
                <flux:fieldset>
                    <flux:legend>{{ \App\Services\Import\MemberFieldMapper::MEMBER_FIELDS[$field] ?? $field }}</flux:legend>

                    @foreach($unknownValues as $unknownValue)
                        <div class="flex items-center gap-4">
                            <flux:badge color="red" class="min-w-32">{{ $unknownValue }}</flux:badge>
                            <flux:icon.arrow-right class="w-4 h-4 text-slate-400" />
                            <flux:select wire:model="enumMap.{{ $field }}.{{ $unknownValue }}" size="sm" class="flex-1">
                                <flux:select.option value="">— {{ __('members.import.mapping.enum_skip') }} —</flux:select.option>
                                @foreach($this->enumOptions($field) as $value => $label)
                                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endforeach
                </flux:fieldset>
            @endforeach

            <div class="flex justify-end gap-3">
                <flux:button wire:click="$set('showEnumModal', false)" variant="ghost">
                    {{ __('members.import.btn_cancel') }}
                </flux:button>
                <flux:button wire:click="confirmEnumMapping" variant="primary">
                    {{ __('members.import.mapping.enum_modal_confirm') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>