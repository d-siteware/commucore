<div class="space-y-6">

    <flux:heading size="lg">
        {{ $isEditing ? __('projects.link_funding.heading.edit') : __('projects.link_funding.heading.new') }}
    </flux:heading>

    <form wire:submit="{{ $isEditing ? 'updatePivot' : 'attach' }}">
        <div class="space-y-6">

            @if(!$isEditing)
                <flux:select wire:model="funding_id"
                             variant="listbox"
                             searchable
                             label="{{ __('projects.link_funding.form.funding') }}"
                             placeholder="{{ __('projects.link_funding.form.funding_placeholder') }}"
                >
                    @foreach($availableFundings as $funding)
                        <flux:select.option value="{{ $funding->id }}">
                            <div>{{ $funding->title }}</div>
                            @if($funding->funder)
                                <div class="text-xs text-gray-400">{{ $funding->funder }}</div>
                            @endif
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="funding_id"/>
            @else
                <flux:text class="text-sm text-gray-500">
                    {{ __('projects.link_funding.form.editing_hint') }}
                </flux:text>
            @endif

            <flux:field>
                <flux:label>{{ __('projects.link_funding.form.allocated_amount') }}</flux:label>
                <flux:description>{{ __('projects.link_funding.form.allocated_amount_hint') }}</flux:description>
                <flux:input.group>
                    <flux:input wire:model="allocated_amount"
                                placeholder="0,00"
                                x-mask:dynamic="$money($input, ',', '.')"
                    />
                    <flux:input.group.suffix>{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</flux:input.group.suffix>
                </flux:input.group>
                <flux:error name="allocated_amount"/>
            </flux:field>

            <div class="flex gap-3">
                <flux:button type="submit" variant="primary" class="flex-1">
                    {{ $isEditing ? __('projects.link_funding.form.btn.update') : __('projects.link_funding.form.btn.attach') }}
                </flux:button>

                @if($isEditing)
                    <flux:button type="button"
                                 variant="ghost"
                                 wire:click="$set('isEditing', false)"
                    >{{ __('app.btn.cancel') }}</flux:button>
                @endif
            </div>

        </div>
    </form>

</div>