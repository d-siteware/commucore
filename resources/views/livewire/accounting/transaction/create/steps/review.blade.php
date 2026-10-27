@php
    $selectedAccount = $this->accounts->firstWhere('id', $form->account_id);
    $selectedBookingAccount = $this->booking_accounts->firstWhere('id', $form->booking_account_id);
    $selectedFiscalYear = $this->fiscalYears->firstWhere('id', $form->fiscal_year_id);
    $selectedCategory = $documentCategory !== ''
        ? \App\Enums\TransactionDocumentCategory::tryFrom($documentCategory)
        : null;
@endphp

<section class="space-y-5">
    <header>
        <flux:heading size="lg">{{ __('transaction.create.steps.review') }}</flux:heading>
        <flux:text class="mt-1">{{ __('transaction.create.steps.review_intro') }}</flux:text>
    </header>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <section class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="mb-3 flex items-center justify-between gap-3">
                <flux:heading size="sm">{{ __('transaction.create.steps.documents') }}</flux:heading>
                <flux:button type="button" size="sm" variant="ghost" wire:click="goToStep(1)">{{ __('transaction.create.steps.change') }}</flux:button>
            </div>
            @if (count($documentFiles) > 0)
                <ul class="space-y-2">
                    @foreach ($documentFiles as $file)
                        <li class="flex items-start gap-2 text-sm">
                            <flux:icon.paper-clip class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                            <span class="min-w-0 flex-1 break-all">{{ $file->getClientOriginalName() }}</span>
                            <span class="shrink-0 text-zinc-500">{{ number_format($file->getSize() / 1024, 1) }} KB</span>
                        </li>
                    @endforeach
                </ul>
                @if ($selectedCategory || $documentLabel !== '')
                    <flux:text size="sm" class="mt-3 text-zinc-500">
                        {{ $selectedCategory?->label() }}{{ $selectedCategory && $documentLabel !== '' ? ' · ' : '' }}{{ $documentLabel }}
                    </flux:text>
                @endif
            @else
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-300">{{ __('transaction.create.steps.no_document_attached') }}</flux:text>
                <flux:text size="xs" class="mt-1 text-zinc-500">{{ __('transaction.create.steps.attach_document_later') }}</flux:text>
            @endif
        </section>

        <section class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="mb-3 flex items-center justify-between gap-3">
                <flux:heading size="sm">{{ __('transaction.create.steps.values') }}</flux:heading>
                <flux:button type="button" size="sm" variant="ghost" wire:click="goToStep(2)">{{ __('transaction.create.steps.change') }}</flux:button>
            </div>
            <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                <dt class="text-zinc-500">{{ __('transaction.form.type') }}</dt>
                <dd class="text-right">{{ $form->type?->label() ?? '—' }}</dd>
                <dt class="text-zinc-500">{{ __('transaction.form.date') }}</dt>
                <dd class="text-right">
                    {{ $form->date ? \Illuminate\Support\Carbon::parse($form->date)->locale(app()->getLocale())->isoFormat('LL') : '—' }}
                </dd>
                <dt class="text-zinc-500">{{ __('transaction.form.label') }}</dt>
                <dd class="break-words text-right">{{ $form->label ?: '—' }}</dd>
                <dt class="text-zinc-500">{{ __('transaction.form.amount_net') }}</dt>
                <dd class="text-right">{{ $form->amount_net ?: '—' }}</dd>
                <dt class="text-zinc-500">{{ __('transaction.form.vat_percent') }}</dt>
                <dd class="text-right">{{ $form->vat }} %</dd>
                <dt class="text-zinc-500">{{ __('transaction.form.vat_amount') }}</dt>
                <dd class="text-right">{{ $form->tax ?: '—' }}</dd>
                <dt class="font-medium">{{ __('transaction.form.amount_gross') }}</dt>
                <dd class="text-right font-semibold">{{ $form->amount_gross ?: '—' }}</dd>
                @if ($form->reference)
                    <dt class="text-zinc-500">{{ __('transaction.form.reference') }}</dt>
                    <dd class="break-words text-right">{{ $form->reference }}</dd>
                @endif
                @if ($form->description)
                    <dt class="text-zinc-500">{{ __('transaction.form.description') }}</dt>
                    <dd class="break-words text-right">{{ $form->description }}</dd>
                @endif
            </dl>
        </section>

        <section class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 lg:col-span-2">
            <div class="mb-3 flex items-center justify-between gap-3">
                <flux:heading size="sm">{{ __('transaction.create.steps.assignment') }}</flux:heading>
                <flux:button type="button" size="sm" variant="ghost" wire:click="goToStep(3)">{{ __('transaction.create.steps.change') }}</flux:button>
            </div>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-zinc-500">{{ __('transaction.form.account.placeholder') }}</dt>
                    <dd class="mt-1 font-medium">{{ $selectedAccount?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">{{ __('transaction.form.booking_account.placeholder') }}</dt>
                    <dd class="mt-1 font-medium">
                        {{ $selectedBookingAccount ? $selectedBookingAccount->number.' - '.$selectedBookingAccount->label : '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-zinc-500">{{ __('transaction.form.area.placeholder') }}</dt>
                    <dd class="mt-1 font-medium">{{ $form->area?->label() ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">{{ __('fiscal_year.fiscal_year') }}</dt>
                    <dd class="mt-1 font-medium">{{ $selectedFiscalYear?->year ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">{{ __('transaction.form.status') }}</dt>
                    <dd class="mt-1 font-medium">{{ $form->status->label() }}</dd>
                </div>
            </dl>
        </section>
    </div>
</section>
