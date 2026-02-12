<div class="shrink-2">
    @if(count($fiscalYears) > 1)
    <flux:dropdown>
        <flux:button icon-trailing="chevron-down" size="sm">{{ $currentFiscalYear }}</flux:button>

        <flux:menu>
            <flux:menu.group heading="{{ __('app.change_fy') }}">
            @foreach($fiscalYears as $year)
            <flux:menu.item wire:click="setFY({{ $year }})">{{ $year }}</flux:menu.item>
            @endforeach
            </flux:menu.group>
        </flux:menu>
    </flux:dropdown>

    @endif
</div>
