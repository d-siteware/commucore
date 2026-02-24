<section class="space-y-6 my-3 ">
    @php use App\Models\Accounting\Account;$total = 0; @endphp
    @forelse(Account::select('id','name','starting_amount','updated_at')->get() as $account)
        @php
            $accountBalance=   $account->accountBalance();
            $total += $accountBalance;
        @endphp
        <flux:card class="flex items-center justify-between">
            <div class="flex-1">
                <flux:heading>{{ $account->name }}</flux:heading>
                <flux:text>{{ __('account.balance_sheet.dated') }}: {{ $account->updated_at->diffForHumans() }}</flux:text>
            </div>
            <aside class="font-bold"><span class="{{ $total>0 ? 'positive' : 'negative' }}"><span>{{ $total>0?'+': '' }}</span>{{ number_format(($accountBalance/100),2,',','.') }}</span><span class="text-sm ml-2.5">EUR</span></aside>
        </flux:card>
    @empty

    @endforelse
    <aside class="flex pt-3 items-center border-t-2 border-dashed">
        <span>{{ __('account.balance_sheet.total') }}</span>

        <flux:spacer/>
        <span class="text-sm mr-2.5">EUR</span>
        <span class="font-bold {{ $total>0 ? 'positive' : 'negative' }}">
            <span>{{ $total>0?'+': '' }}</span>
            {{ number_format(($total/100),2,',','.') }}
        </span>
    </aside>

</section>

