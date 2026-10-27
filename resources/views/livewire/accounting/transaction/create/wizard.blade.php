<div x-data="checkVat">
    <flux:subheading class="mb-6">{{ __('transaction.create.steps.intro') }}</flux:subheading>

    <div
        wire:key="transaction-wizard"
        x-data="{
            files: [],
            activeFile: 0,
            dragOver: false,
            loadFiles(files) {
                this.files.forEach(file => URL.revokeObjectURL(file.url));
                this.files = Array.from(files).map(file => ({
                    name: file.name,
                    type: file.type,
                    url: URL.createObjectURL(file),
                }));
                this.activeFile = 0;
            },
            removePreview(index) {
                const file = this.files[index];
                if (file) URL.revokeObjectURL(file.url);
                this.files.splice(index, 1);
                this.activeFile = Math.min(this.activeFile, Math.max(0, this.files.length - 1));
                document.querySelectorAll('[data-document-upload]').forEach(input => input.value = '');
            },
            clearPreview() {
                this.files.forEach(file => URL.revokeObjectURL(file.url));
                this.files = [];
                this.activeFile = 0;
                document.querySelectorAll('[data-document-upload]').forEach(input => input.value = '');
            },
            destroy() {
                this.files.forEach(file => URL.revokeObjectURL(file.url));
            }
        }"
        x-on:document-file-removed.window="removePreview($event.detail.index)"
        x-on:document-files-cleared.window="clearPreview()"
    >
        @php
            $stepLabels = [
                1 => __('transaction.create.steps.documents'),
                2 => __('transaction.create.steps.values'),
                3 => __('transaction.create.steps.assignment'),
                4 => __('transaction.create.steps.review'),
            ];
            $stepNumbers = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
        @endphp

        <nav aria-label="{{ __('transaction.create.steps.progress') }}" class="mb-8 hidden md:block">
            <ol class="divide-y divide-gray-300 overflow-hidden rounded-xl border border-gray-300 dark:divide-white/15 dark:border-white/15 md:flex md:divide-y-0">
                @foreach ($stepLabels as $item => $label)
                    <li class="relative min-w-0 md:flex md:flex-1">
                        @if ($step > $item)
                            <x-steps.completed :item="$item" :step="$stepNumbers[$item]" :label="$label" />
                        @elseif ($step === $item)
                            <x-steps.current :item="$item" :step="$stepNumbers[$item]" :label="$label" :last="$item === 4" />
                        @else
                            <x-steps.upcomming :item="$item" :step="$stepNumbers[$item]" :label="$label" :last="$item === 4" :disabled="true" />
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <nav aria-label="{{ __('transaction.create.steps.progress') }}" class="mb-4 md:hidden">
            <ol class="flex items-center gap-1 overflow-x-auto whitespace-nowrap pb-2 text-xs">
                @foreach ($stepLabels as $item => $label)
                    <li class="flex shrink-0 items-center gap-1">
                        @if ($step > $item)
                            <button
                                type="button"
                                wire:click="goToStep({{ $item }})"
                                class="rounded px-1 py-1 font-medium text-teal-700 underline decoration-teal-300 underline-offset-4 dark:text-teal-300"
                            >{{ $label }}</button>
                        @elseif ($step === $item)
                            <span aria-current="step" class="rounded bg-teal-50 px-2 py-1 font-semibold text-teal-800 dark:bg-teal-950 dark:text-teal-100">
                                {{ $label }}
                            </span>
                        @else
                            <span class="px-1 py-1 text-zinc-400 dark:text-zinc-500">{{ $label }}</span>
                        @endif

                        @if ($item < 4)
                            <span aria-hidden="true" class="text-zinc-400 dark:text-zinc-500">></span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <flux:card>
            @if ($step === 1)
                @include('livewire.accounting.transaction.create.steps.documents')
            @elseif ($step === 2)
                @include('livewire.accounting.transaction.create.steps.values')
            @elseif ($step === 3)
                @include('livewire.accounting.transaction.create.steps.assignment')
            @else
                @include('livewire.accounting.transaction.create.steps.review')
            @endif

            <flux:error name="step" />

            <div class="mt-8 flex items-center justify-between gap-3 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                @if ($step > 1)
                    <flux:button type="button" wire:click="previousStep" variant="ghost" icon="chevron-left">
                        {{ __('pagination.btn_previous') }}
                    </flux:button>
                @else
                    <span></span>
                @endif

                @if ($step < 4)
                    <flux:button
                        type="button"
                        wire:click="nextStep"
                        wire:loading.attr="disabled"
                        wire:target="documentFiles"
                        variant="primary"
                        icon-trailing="chevron-right"
                    >
                        <span wire:loading.remove wire:target="documentFiles">
                            @if ($step === 1 && empty($documentFiles))
                                {{ __('transaction.create.steps.continue_without_document') }}
                            @elseif ($step === 1)
                                {{ __('transaction.create.steps.continue_to_values') }}
                            @elseif ($step === 3)
                                {{ __('transaction.create.steps.review_booking') }}
                            @else
                                {{ __('pagination.btn_next') }}
                            @endif
                        </span>
                        <span wire:loading wire:target="documentFiles">{{ __('documents.upload.loading') }}</span>
                    </flux:button>
                @else
                    <flux:button
                        type="button"
                        wire:click="submitTransaction"
                        wire:loading.attr="disabled"
                        wire:target="submitTransaction"
                        variant="primary"
                    >
                        <span wire:loading.remove wire:target="submitTransaction">{{ __('transaction.form.btn.save') }}</span>
                        <span wire:loading wire:target="submitTransaction">{{ __('documents.btn.saving') }}</span>
                    </flux:button>
                @endif
            </div>
        </flux:card>
    </div>

    @include('livewire.accounting.transaction.create.partials.vat-script')
    @include('livewire.accounting.transaction.create.partials.account-modals')
</div>
