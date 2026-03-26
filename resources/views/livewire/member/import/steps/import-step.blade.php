<div class="space-y-6">

    <flux:card>
        <flux:heading size="lg">{{ __('members.import.import.title') }}</flux:heading>
        <flux:subheading>{{ __('members.import.import.description', ['count' => count($mappedRows)]) }}</flux:subheading>

        <div class="mt-6 space-y-6">

            @if(! $importStarted)

                <flux:callout icon="exclamation-triangle" color="yellow">
                    <flux:callout.heading>{{ __('members.import.import.warning_heading') }}</flux:callout.heading>
                    <flux:callout.text>{{ __('members.import.import.warning_text') }}</flux:callout.text>
                </flux:callout>

                <flux:button
                        wire:click="startImport"
                        wire:confirm="{{ __('members.import.import.confirm') }}"
                        variant="primary"
                        icon="arrow-down-tray"
                >
                    {{ __('members.import.import.btn_start', ['count' => count($mappedRows)]) }}
                </flux:button>

            @elseif(! $importFinished)

                <div class="flex items-center gap-4 py-8 justify-center">
                    <flux:icon.arrow-path class="w-8 h-8 text-indigo-600 animate-spin" />
                    <span class="text-slate-600 dark:text-slate-300">{{ __('members.import.import.in_progress') }}</span>
                </div>

            @else

                {{-- Protokoll --}}
                <flux:callout icon="check-circle" color="green">
                    <flux:callout.heading>{{ __('members.import.import.success_heading') }}</flux:callout.heading>
                </flux:callout>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <flux:card class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-yellow-400">{{ $protocol['imported'] }}</div>
                        <div class="text-sm text-slate-500 dark:text-shadow-slate-300">{{ __('members.import.mail.imported') }}</div>
                    </flux:card>
                    <flux:card class="text-center">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $protocol['skipped'] }}</div>
                        <div class="text-sm text-slate-500 dark:text-shadow-slate-300">{{ __('members.import.mail.skipped') }}</div>
                    </flux:card>
                    <flux:card class="text-center">
                        <div class="text-2xl font-bold text-red-600 dark:text-amber-600">{{ count($protocol['errors']) }}</div>
                        <div class="text-sm text-slate-500 dark:text-shadow-slate-300">{{ __('members.import.mail.errors') }}</div>
                    </flux:card>
                    <flux:card class="text-center">
                        <div class="text-2xl font-bold text-slate-600 dark:text-slate-400">{{ $protocol['duration_ms'] }}ms</div>
                        <div class="text-sm text-slate-500 dark:text-shadow-slate-300">{{ __('members.import.mail.duration') }}</div>
                    </flux:card>
                </div>

                {{-- Fehlerdetails --}}
                @if(count($protocol['errors']) > 0)
                    <flux:card>
                        <flux:heading size="sm">{{ __('members.import.mail.error_details') }}</flux:heading>
                        <div class="mt-3 space-y-2">
                            @foreach($protocol['errors'] as $error)
                                <div class="flex items-start gap-3 text-sm">
                                    <flux:badge color="red">{{ __('members.import.mail.error_row', ['row' => $error['row']]) }}</flux:badge>
                                    <span class="text-slate-600 dark:text-slate-300">{{ $error['reason'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </flux:card>
                @endif

                {{-- Aktionen --}}
                <div class="flex flex-wrap gap-3">
                    <flux:button
                            wire:click="$dispatch('import-complete')"
                            variant="primary"
                            icon="check"
                    >
                        {{ __('members.import.import.btn_finish') }}
                    </flux:button>

                    @if($this->canRollback())
                        <flux:button
                                wire:click="rollback"
                                wire:confirm="{{ __('members.import.import.rollback_confirm') }}"
                                wire:loading.attr="disabled"
                                variant="danger"
                                icon="arrow-uturn-left"
                        >
                            <span wire:loading.remove wire:target="rollback">
                                {{ __('members.import.import.btn_rollback') }}
                            </span>
                            <span wire:loading wire:target="rollback">
                                {{ __('members.import.import.btn_rolling_back') }}
                            </span>
                        </flux:button>
                    @endif
                </div>

            @endif

        </div>
    </flux:card>

</div>