<section class="space-y-5">
    <header>
        <flux:heading size="lg">{{ __('transaction.create.steps.values') }}</flux:heading>
        <flux:text class="mt-1">{{ __('transaction.create.steps.values_intro') }}</flux:text>
    </header>

    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-5">
        <aside class="space-y-3 xl:sticky xl:top-6 xl:col-span-2">
            <flux:heading size="sm">{{ __('transaction.create.steps.document_preview') }}</flux:heading>

            <template x-if="files.length > 0">
                <div class="space-y-3">
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(file, index) in files" :key="file.name + index">
                            <button
                                type="button"
                                x-on:click="activeFile = index"
                                x-text="file.name"
                                :aria-pressed="activeFile === index"
                                class="max-w-full truncate rounded-full border px-3 py-1.5 text-xs"
                                :class="activeFile === index ? 'border-teal-600 bg-teal-50 text-teal-800 dark:border-teal-400 dark:bg-teal-950 dark:text-teal-100' : 'border-zinc-300 text-zinc-600 dark:border-zinc-700 dark:text-zinc-300'"
                            ></button>
                        </template>
                    </div>

                    <div class="flex aspect-[4/5] items-center justify-center overflow-hidden rounded-lg border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                        <template x-if="files[activeFile]?.type === 'application/pdf'">
                            <iframe :src="files[activeFile].url" :title="files[activeFile].name" class="h-full w-full"></iframe>
                        </template>
                        <template x-if="['image/jpeg', 'image/png', 'image/webp'].includes(files[activeFile]?.type)">
                            <img :src="files[activeFile].url" :alt="files[activeFile].name" class="max-h-full max-w-full object-contain" />
                        </template>
                        <template x-if="files[activeFile] && !['application/pdf', 'image/jpeg', 'image/png', 'image/webp'].includes(files[activeFile].type)">
                            <div class="space-y-3 p-6 text-center">
                                <flux:icon.document class="mx-auto size-10 text-zinc-400" />
                                <flux:text x-text="files[activeFile].name"></flux:text>
                            </div>
                        </template>
                    </div>

                    <a
                        x-show="files[activeFile]"
                        :href="files[activeFile]?.url"
                        :download="files[activeFile]?.name"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex text-sm font-medium text-teal-700 underline underline-offset-2 dark:text-teal-300"
                    >{{ __('transaction.create.steps.download_original') }}</a>
                </div>
            </template>

            <div x-show="files.length === 0" x-cloak class="rounded-lg border border-dashed border-zinc-300 p-6 text-sm text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                {{ __('transaction.create.steps.no_document_values') }}
            </div>
        </aside>

        <div class="space-y-6 xl:col-span-3">
            <div class="sm:hidden">
                <flux:select
                    wire:model.live="form.type"
                    :label="__('transaction.form.type')"
                    :placeholder="__('transaction.form.type')"
                    variant="listbox"
                >
                    @foreach (\App\Enums\TransactionType::cases() as $type)
                        <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="hidden sm:block">
                <flux:radio.group
                    wire:model.live="form.type"
                    size="sm"
                    :label="__('transaction.form.type')"
                    variant="segmented"
                >
                    @foreach (\App\Enums\TransactionType::cases() as $type)
                        <flux:radio :key="$type->value" value="{{ $type->value }}">{{ $type->label() }}</flux:radio>
                    @endforeach
                </flux:radio.group>
            </div>
            <flux:error name="form.type" />

            <flux:separator :text="__('transaction.form.separator.amounts')" />
            <section class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <flux:input
                    wire:model.blur="form.amount_gross"
                    x-mask:dynamic="$money($input, ',', '.')"
                    :label="__('transaction.form.amount_gross')"
                    @change="updateValuesFromGross"
                />
                <flux:input
                    wire:model.blur="form.vat"
                    :label="__('transaction.form.vat_percent')"
                    @change="updateValuesFromGross"
                />
                <flux:input
                    wire:model.blur="form.tax"
                    x-mask:dynamic="$money($input, ',', '.')"
                    @change="updateValuesFromGross"
                    :label="__('transaction.form.vat_amount')"
                    variant="filled"
                />
                <flux:input
                    wire:model.blur="form.amount_net"
                    x-mask:dynamic="$money($input, ',', '.')"
                    :label="__('transaction.form.amount_net')"
                    @change="updateValuesFromNet"
                />
            </section>
            <flux:error name="form.amount_gross" />
            <flux:error name="form.amount_net" />
            <flux:error name="form.vat" />

            <flux:separator :text="__('transaction.form.separator.texts')" />
            <section class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <flux:input :label="__('transaction.form.label')" wire:model.blur="form.label" />
                <flux:date-picker
                    locale="{{ app()->getLocale() }}"
                    :label="__('transaction.form.date')"
                    wire:model.blur="form.date"
                    start-day="1"
                />
                <flux:input :label="__('transaction.form.reference')" wire:model.blur="form.reference" />
                <flux:input :label="__('transaction.form.description')" wire:model.blur="form.description" />
            </section>
            <flux:error name="form.label" />
            <flux:error name="form.date" />
        </div>
    </div>
</section>
