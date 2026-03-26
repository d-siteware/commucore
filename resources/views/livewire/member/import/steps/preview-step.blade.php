{{-- resources/views/livewire/member/import/steps/preview-step.blade.php --}}
<div class="space-y-6">

    <flux:card>
        <flux:heading size="lg">{{ __('members.import.preview.title') }}</flux:heading>
        <flux:subheading>
            {{ __('members.import.preview.description', ['total' => $totalRows, 'duplicates' => count($duplicates)]) }}
        </flux:subheading>

        <div class="mt-6 space-y-6">

            {{-- Statistik --}}
            <div class="grid grid-cols-3 gap-4">
                <flux:card class="text-center">
                    <div class="text-2xl font-bold text-sky-600 dark:text-sky-400">{{ $totalRows }}</div>
                    <div class="text-sm text-slate-500">{{ __('members.import.preview.total_rows') }}</div>
                </flux:card>
                <flux:card class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totalRows - count($duplicates) }}</div>
                    <div class="text-sm text-slate-500">{{ __('members.import.preview.new_rows') }}</div>
                </flux:card>
                <flux:card class="text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ count($duplicates) }}</div>
                    <div class="text-sm text-slate-500">{{ __('members.import.preview.duplicate_rows') }}</div>
                </flux:card>
            </div>

            {{-- Vorschau Tabelle --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        @foreach(array_keys($this->previewRows()[0] ?? []) as $col)
                            <th class="text-left py-2 px-3 font-medium text-slate-500">{{ $col }}</th>
                        @endforeach
                        <th class="text-left py-2 px-3 font-medium text-slate-500">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->previewRows() as $index => $row)
                        @php $isDuplicate = in_array($row, $duplicates); @endphp
                        <tr @class([
                                'border-b border-slate-100 dark:border-slate-800',
                                'bg-yellow-50 dark:bg-yellow-900/10' => $isDuplicate,
                            ])>
                            @foreach($row as $value)
                                <td class="py-2 px-3 text-slate-700 dark:text-slate-300">
                                    {{ $value ?: '—' }}
                                </td>
                            @endforeach
                            <td class="py-2 px-3">
                                @if($isDuplicate)
                                    <flux:badge color="yellow">{{ __('members.import.preview.duplicate') }}</flux:badge>
                                @else
                                    <flux:badge color="green">{{ __('members.import.preview.new') }}</flux:badge>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if($totalRows > 10)
                <flux:text class="text-slate-500 text-sm">
                    {{ __('members.import.preview.more_rows', ['count' => $totalRows - 10]) }}
                </flux:text>
            @endif

            {{-- Backup --}}
            @if(! $backupCreated)
                <flux:callout icon="shield-exclamation" color="yellow">
                    <flux:callout.heading>{{ __('members.import.preview.backup_required') }}</flux:callout.heading>
                    <flux:callout.text>{{ __('members.import.preview.backup_description') }}</flux:callout.text>
                </flux:callout>

                <flux:button
                        wire:click="createBackup"
                        wire:loading.attr="disabled"
                        variant="primary"
                        icon="shield-check"
                >
                    <span wire:loading.remove>{{ __('members.import.preview.btn_backup') }}</span>
                    <span wire:loading>{{ __('members.import.preview.btn_backup_loading') }}</span>
                </flux:button>
            @else
                <flux:callout icon="shield-check" color="green">
                    <flux:callout.heading>{{ __('members.import.preview.backup_created') }}</flux:callout.heading>
                    <flux:callout.text>
                        <a href="{{ $this->backupDownloadUrl() }}" class="underline">
                            {{ __('members.import.preview.backup_download') }}
                        </a>
                    </flux:callout.text>
                </flux:callout>

                <div class="flex gap-3">
                    <flux:button wire:click="$dispatch('previous-step')" variant="ghost" icon="arrow-left">
                        {{ __('members.import.btn_back') }}
                    </flux:button>
                    <flux:button
                            wire:click="$dispatch('backup-complete', { backupPath: '{{ $backupPath }}' })"
                            variant="primary"
                            icon="arrow-right"
                            icon-trailing
                    >
                        {{ __('members.import.preview.btn_continue') }}
                    </flux:button>
                </div>
            @endif

        </div>
    </flux:card>

</div>