<section class="space-y-6 my-3 ">

    @forelse(\App\Models\Accounting\CashCount::query()->whereYear('cash_counts.counted_at',session('financialYear',date('Y')))->latest()->take(2)->get() as $counting)
        <flux:card class="space-y-3">
            <section class="flex items-center justify-between">
                <div class="flex-1">
                    <flux:heading>{{ $counting->label }}</flux:heading>
                    <flux:text>{{ $counting->account->name }}</flux:text>
                    <flux:text>{{ __('account.cashcount.dated') }}: {{ $counting->counted_at->format('Y-m-d') }}</flux:text>
                </div>
                <aside class="font-bold">  {{ $counting->sumString() }}<span class="text-sm ml-2.5">EUR</span></aside>
            </section>
            <flux:accordion transition>
                <flux:accordion.item>
                    <flux:accordion.heading>{{ __('account.cashcount.heading') }}</flux:accordion.heading>
                    <flux:accordion.content>
                    <x-cash-count-report :counting="$counting" />

                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            @can('create',\App\Models\Accounting\Account::class)

                    <flux:separator />
                <aside class="flex justify-between items-center">
                    <flux:button variant="danger" size="xs" icon="trash" wire:click="initCashContDeletion({{ $counting->id }})">{{ __('account.cashcount.btn.delete') }}</flux:button>
                    <flux:button variant="primary" size="xs" icon="pencil-square" wire:click="editCashCount({{ $counting->id }})">{{ __('account.cashcount.btn.edit') }}</flux:button>
                </aside>

            @endcan

        </flux:card>
    @empty
       {{ __('account.cashcount.empty_state') }}
    @endforelse


</section>

