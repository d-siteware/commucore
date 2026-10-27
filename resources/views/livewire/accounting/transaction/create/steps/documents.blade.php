<section class="space-y-5">
    <header>
        <flux:heading size="lg">{{ __('transaction.create.steps.documents') }}</flux:heading>
        <flux:text class="mt-1">{{ __('transaction.create.steps.documents_intro') }}</flux:text>
    </header>

    <flux:callout icon="information-circle" variant="info">
        <flux:callout.text>{{ __('transaction.create.steps.document_optional') }}</flux:callout.text>
    </flux:callout>

    <flux:select
        wire:model="documentCategory"
        variant="listbox"
        :label="__('documents.category.label')"
        :placeholder="__('documents.category.placeholder')"
        clearable
    >
        @foreach ($this->documentCategories as $value => $label)
            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
        @endforeach
    </flux:select>

    <flux:input
        wire:model.blur="documentLabel"
        :label="__('documents.upload.label_field')"
        :placeholder="__('documents.upload.label_placeholder')"
    />

    <flux:field>
        <flux:label>{{ __('documents.upload.file_label') }}</flux:label>
        <flux:description>{{ __('documents.upload.file_hint') }}</flux:description>

        <div
            x-on:dragover.prevent="dragOver = true"
            x-on:dragleave.prevent="dragOver = false"
            x-on:drop.prevent="dragOver = false; loadFiles($event.dataTransfer.files); $wire.upload('documentFiles', $event.dataTransfer.files)"
            class="relative block w-full rounded-lg border-2 border-dashed p-8 text-center transition-colors"
            :class="dragOver ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-950' : 'border-gray-300 hover:border-gray-400 dark:border-gray-600'"
        >
            <input
                data-document-upload
                type="file"
                wire:model="documentFiles"
                x-on:change="loadFiles($event.target.files)"
                multiple
                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.tif,.tiff"
                class="absolute inset-0 hidden h-full w-full cursor-pointer opacity-0 sm:block"
            />
            <input
                data-document-upload
                type="file"
                wire:model="documentFiles"
                x-on:change="loadFiles($event.target.files)"
                multiple
                accept=".pdf,.jpg,.jpeg,.png,.tif,.tiff"
                capture="environment"
                class="absolute inset-0 h-full w-full cursor-pointer opacity-0 sm:hidden"
            />
            <div class="pointer-events-none space-y-2">
                <flux:icon.arrow-up-tray class="mx-auto size-8 text-gray-400" />
                <flux:text class="text-sm text-gray-600 dark:text-gray-300">{{ __('documents.upload.drag_hint') }}</flux:text>
            </div>
        </div>

        <flux:error name="documentFiles" />
        <flux:error name="documentFiles.*" />
        <flux:error name="documentCategory" />
    </flux:field>

    <div wire:loading wire:target="documentFiles" class="flex items-center gap-2 text-sm text-gray-500">
        <flux:icon.arrow-path class="size-4 animate-spin" />
        {{ __('documents.upload.loading') }}
    </div>

    @if (! empty($documentFiles))
        <ul class="divide-y divide-zinc-200 rounded-lg border border-zinc-200 dark:divide-zinc-700 dark:border-zinc-700">
            @foreach ($documentFiles as $index => $file)
                <li wire:key="transaction-document-{{ $index }}" class="flex items-center gap-3 p-3">
                    <flux:icon.paper-clip class="size-5 shrink-0 text-zinc-400" />
                    <span class="min-w-0 flex-1 truncate text-sm">{{ $file->getClientOriginalName() }}</span>
                    <span class="shrink-0 text-xs text-zinc-500">{{ number_format($file->getSize() / 1024, 1) }} KB</span>
                    <flux:button
                        type="button"
                        wire:click="removeDocumentFile({{ $index }})"
                        variant="ghost"
                        size="sm"
                        icon="x-mark"
                        :aria-label="__('transaction.create.steps.remove_document', ['name' => $file->getClientOriginalName()])"
                    />
                </li>
            @endforeach
        </ul>
    @endif
</section>
