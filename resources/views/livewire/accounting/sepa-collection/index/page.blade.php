<div class="space-y-6">
    <flux:heading size="lg">{{ __('sepa.collection.heading') }}</flux:heading>
    <flux:subheading>{{ __('sepa.collection.subheading') }}</flux:subheading>

    <div class="flex items-center justify-between">
        <flux:tabs wire:model="selectedTab" class="w-full">
            <flux:tab name="pending" wire:click="setSelectedTab('pending')">
                {{ __('sepa.collection.tabs.pending') }}
                @if($this->pendingCollections->isNotEmpty())
                    <flux:badge size="sm" color="blue">{{ $this->pendingCollections->count() }}</flux:badge>
                @endif
            </flux:tab>
            <flux:tab name="history" wire:click="setSelectedTab('history')">
                {{ __('sepa.collection.tabs.history') }}
            </flux:tab>
            <flux:tab name="returns" wire:click="setSelectedTab('returns')">
                {{ __('sepa.collection.tabs.returns') }}
                @if(!empty($this->returns))
                    <flux:badge size="sm" color="red">{{ count($this->returns) }}</flux:badge>
                @endif
            </flux:tab>
        </flux:tabs>

        <div class="flex items-center gap-2">
            <flux:select wire:model.live="selectedYear" class="w-32">
                @foreach($this->availableYears as $year)
                    <flux:select.option :value="$year">{{ $year }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:dropdown align="end">
                <flux:button icon-trailing="chevron-down" variant="primary">
                    {{ __('sepa.collection.actions.generate_xml') }}
                </flux:button>

                <flux:menu>
                    <flux:menu.item wire:click="createTransactions" icon="document-plus">
                        {{ __('sepa.collection.actions.create_transactions') }}
                    </flux:menu.item>

                    <flux:menu.item wire:click="generateXml" icon="arrow-down-tray">
                        {{ __('sepa.collection.actions.download_xml') }}
                    </flux:menu.item>

                    <flux:menu.separator />

                    <flux:menu.item wire:click="generateWithTransactions" icon="document-arrow-down">
                        {{ __('sepa.collection.actions.generate_and_download') }}
                    </flux:menu.item>

                    <flux:menu.item wire:click="uploadAndBook" icon="cloud-arrow-up">
                        {{ __('sepa.collection.actions.upload_ebics') }}
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    @if($selectedTab === 'pending')
        <flux:card>
            @if($this->pendingCollections->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    {{ __('sepa.collection.pending_none') }}
                </div>
            @else
                <flux:table>
                    <flux:columns>
                        <flux:column>{{ __('sepa.collection.columns.member') }}</flux:column>
                        <flux:column>{{ __('sepa.collection.columns.mandate') }}</flux:column>
                        <flux:column>{{ __('sepa.collection.columns.amount') }}</flux:column>
                        <flux:column>{{ __('sepa.collection.columns.fee_year') }}</flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach($this->pendingCollections as $item)
                            <flux:row>
                                <flux:cell class="font-medium">
                                    <a href="{{ route('backend.members.show', $item['member']->id) }}" class="hover:underline">
                                        {{ $item['member']->fullName() }}
                                    </a>
                                </flux:cell>
                                <flux:cell>
                                    <code class="text-xs">{{ $item['mandate']?->mandate_reference }}</code>
                                </flux:cell>
                                <flux:cell>
                                    {{ number_format($item['amount'] / 100, 2, ',', '.') }} €
                                </flux:cell>
                                <flux:cell>{{ $item['fee_year'] }}</flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>

                <div class="mt-4 text-right font-semibold text-gray-700">
                    {{ __('branding.form.summary_total') }}:
                    {{ number_format($this->pendingCollections->sum('amount') / 100, 2, ',', '.') }} €
                </div>
            @endif
        </flux:card>

    @elseif($selectedTab === 'history')
        <flux:card>
            <div class="text-center py-8 text-gray-500">
                {{ __('sepa.collection.no_history') }}
            </div>
        </flux:card>

    @elseif($selectedTab === 'returns')
        <flux:card>
            @if(empty($this->returns))
                <div class="text-center py-8 text-gray-500">
                    {{ __('sepa.return_debit.no_returns') }}
                </div>
            @else
                <flux:table>
                    <flux:columns>
                        <flux:column>{{ __('sepa.return_debit.columns.date') }}</flux:column>
                        <flux:column>{{ __('sepa.return_debit.columns.member') }}</flux:column>
                        <flux:column>{{ __('sepa.return_debit.columns.amount') }}</flux:column>
                        <flux:column>{{ __('sepa.return_debit.columns.reason') }}</flux:column>
                        <flux:column>{{ __('sepa.return_debit.columns.actions') }}</flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach($this->returns as $return)
                            <flux:row>
                                <flux:cell>{{ $return['returned_at']->format('d.m.Y') }}</flux:cell>
                                <flux:cell class="font-medium">
                                    <a href="{{ route('backend.members.show', $return['member']?->id) }}" class="hover:underline">
                                        {{ $return['member']?->fullName() ?? '—' }}
                                    </a>
                                </flux:cell>
                                <flux:cell>{{ number_format($return['amount'] / 100, 2, ',', '.') }} €</flux:cell>
                                <flux:cell class="text-sm text-gray-600 max-w-xs truncate">
                                    {{ $return['reason'] }}
                                </flux:cell>
                                <flux:cell>
                                    @if($return['can_recollect'])
                                        <flux:button wire:click="recollect({{ $return['transaction']->id }})" size="sm">
                                            {{ __('sepa.return_debit.actions.recollect') }}
                                        </flux:button>
                                    @else
                                        <flux:badge size="sm" color="red">{{ __('sepa.return_debit.errors.no_active_mandate') }}</flux:badge>
                                    @endif
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @endif
        </flux:card>
    @endif
</div>
