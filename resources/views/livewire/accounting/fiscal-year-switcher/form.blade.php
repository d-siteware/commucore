<div class="shrink-2">
    @if($fiscalYears->count() > 1)
    <flux:dropdown>
        <flux:button icon-trailing="chevron-down" size="sm">
            {{ $fiscalYears->firstWhere('id', $currentFiscalYearId)?->year ?? $currentFiscalYearId }}
        </flux:button>

        <flux:menu>
            <flux:menu.group heading="{{ __('app.change_fy') }}">
            @foreach($fiscalYears as $fy)
            <flux:menu.item wire:click="setFY({{ $fy->id }})">
                {{ $fy->year }}
                @if($fy->isClosed())
                    <flux:badge size="xs" color="red" class="ml-2">{{ __('fiscal_year.closed') }}</flux:badge>
                @endif
            </flux:menu.item>
            @endforeach
            </flux:menu.group>
        </flux:menu>
    </flux:dropdown>

    @endif
</div>
