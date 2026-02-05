<section class="space-y-6 my-3 ">

    @forelse(App\Models\Accounting\CashCount::query()->whereYear('cash_counts.counted_at',session('financialYear'))->latest()->take(2)->get() as $counting)
        <flux:card class="space-y-3">
            <section class="flex items-center justify-between">
                <div class="flex-1">
                    <flux:heading>{{ $counting->label }}</flux:heading>
                    <flux:text>{{ $counting->account->name }}</flux:text>
                    <flux:text>vom: {{ $counting->counted_at->format('Y-m-d') }}</flux:text>
                </div>
                <aside class="font-bold">  {{ $counting->sumString() }}<span class="text-sm ml-2.5">EUR</span></aside>
            </section>
            <flux:accordion transition>
                <flux:accordion.item>
                    <flux:accordion.heading>Übersicht</flux:accordion.heading>
                    <flux:accordion.content>
                    <x-cash-count-report :counting="$counting" />

                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>



            @can('create',\App\Models\Accounting\Account::class)

                    <flux:separator />
                <aside class="flex justify-between items-center">
                    <flux:button variant="danger" size="xs" icon="trash" wire:click="initCashContDeletion({{ $counting->id }})">Löschen</flux:button>
                    <flux:button variant="primary" size="xs" icon="pencil-square" wire:click="editCashCount({{ $counting->id }})">Bearbeiten</flux:button>


                </aside>


            @endcan
        </flux:card>
    @empty
        Keine Zählungen erfasst
    @endforelse


</section>

