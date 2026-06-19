@props(['compact' => false])

@php
    use App\Models\Accounting\Account;
    $accounts = Account::select('id', 'name', 'starting_amount', 'updated_at')->get();
    $total = 0;
@endphp

<section class="{{ $compact ? 'space-y-1' : 'space-y-4 my-3' }}">

    @forelse ($accounts as $account)
        @php
            $accountBalance = $account->accountBalance();
            $total += $accountBalance;
        @endphp

        @if ($compact)
            {{-- Kompakte Zeile ohne innere Card --}}
            <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                <div class="min-w-0">
                    <flux:text class="font-medium text-sm truncate">{{ $account->name }}</flux:text>
                    <flux:text class="text-xs text-zinc-400">{{ $account->updated_at->diffForHumans() }}</flux:text>
                </div>
                <aside class="font-semibold tabular-nums ml-3 shrink-0">
                    <span class="{{ $accountBalance > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">
                        {{ $accountBalance > 0 ? '+' : '' }}{{ \App\Helpers\MoneyHelper::formatCents($accountBalance, withSymbol: false) }}
                    </span>
                    <span class="text-xs text-zinc-400 ml-1">EUR</span>
                </aside>
            </div>
        @else
            {{-- Volle Card-Ansicht (Buchhaltungsübersicht) --}}
            <flux:card class="flex items-center justify-between">
                <div class="flex-1">
                    <flux:heading>{{ $account->name }}</flux:heading>
                    <flux:text>{{ __('account.balance_sheet.dated') }}: {{ $account->updated_at->diffForHumans() }}</flux:text>
                </div>
                <aside class="font-bold">
                    <span class="{{ $accountBalance > 0 ? 'positive' : 'negative' }}">
                        {{ $accountBalance > 0 ? '+' : '' }}{{ \App\Helpers\MoneyHelper::formatCents($accountBalance, withSymbol: false) }}
                    </span>
                    <span class="text-sm ml-2.5">EUR</span>
                </aside>
            </flux:card>
        @endif

    @empty
        <flux:text class="text-zinc-400 text-sm">{{ __('account.balance_sheet.empty') }}</flux:text>
    @endforelse

    {{-- Gesamtzeile --}}
    <div class="{{ $compact ? 'flex items-center justify-between px-3 pt-2 border-t border-zinc-200 dark:border-zinc-700' : 'flex pt-3 items-center border-t-2 border-dashed' }}">
        <span class="{{ $compact ? 'text-sm font-medium text-zinc-600 dark:text-zinc-300' : '' }}">
            {{ __('account.balance_sheet.total') }}
        </span>
        <flux:spacer />
        <span class="{{ $compact ? '' : 'text-sm mr-2.5' }}">EUR</span>
        <span class="font-bold {{ $total > 0 ? ($compact ? 'text-emerald-600 dark:text-emerald-400' : 'positive') : ($compact ? 'text-red-500' : 'negative') }} {{ $compact ? 'tabular-nums ml-2' : '' }}">
            {{ $total > 0 ? '+' : '' }}{{ \App\Helpers\MoneyHelper::formatCents($total, withSymbol: false) }}
        </span>
    </div>

</section>