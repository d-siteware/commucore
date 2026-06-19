<div class="space-y-6">
    <flux:heading size="lg">{{ __('sepa.collection.heading') }}</flux:heading>
    <flux:subheading>{{ __('sepa.collection.subheading') }}</flux:subheading>

    <div class="flex items-center justify-between">
        <flux:tabs wire:model="selectedTab" class="w-full">
            <flux:tab name="pending" wire:click="setSelectedTab('pending')">
                {{ __('sepa.collection.tabs.pending') }}
                @if($this->openCandidates->isNotEmpty())
                    <flux:badge size="sm" color="blue">{{ $this->openCandidates->count() }}</flux:badge>
                @endif
            </flux:tab>
            <flux:tab name="attempts" wire:click="setSelectedTab('attempts')">
                {{ __('sepa.collection.tabs.attempts') }}
                @if($this->unresolvedAttempts->isNotEmpty())
                    <flux:badge size="sm" color="amber">{{ $this->unresolvedAttempts->flatten(1)->count() }}</flux:badge>
                @endif
            </flux:tab>
            <flux:tab name="history" wire:click="setSelectedTab('history')">
                {{ __('sepa.collection.tabs.history') }}
            </flux:tab>
            <flux:tab name="returns" wire:click="setSelectedTab('returns')">
                {{ __('sepa.collection.tabs.returns') }}
                @if($this->returns->isNotEmpty())
                    <flux:badge size="sm" color="red">{{ $this->returns->count() }}</flux:badge>
                @endif
            </flux:tab>
        </flux:tabs>

        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 font-medium">
                {{ $this->currentPeriodLabel }}
            </span>

            <flux:dropdown align="end">
                <flux:button icon-trailing="chevron-down" variant="primary">
                    {{ __('sepa.collection.actions.generate_xml') }}
                </flux:button>

                <flux:menu>
                    <flux:menu.item wire:click="createAttempts" icon="document-plus">
                        {{ __('sepa.collection.actions.create_attempts') }}
                    </flux:menu.item>

                    <flux:menu.item wire:click="generateXml" icon="arrow-down-tray">
                        {{ __('sepa.collection.actions.download_xml') }}
                    </flux:menu.item>

                    <flux:menu.separator />

                    <flux:menu.item wire:click="uploadToEbics" icon="cloud-arrow-up">
                        {{ __('sepa.collection.actions.upload_ebics') }}
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    @if($selectedTab === 'pending')
        <flux:card>
            @if($this->openCandidates->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    {{ __('sepa.collection.pending_none') }}
                </div>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('sepa.collection.columns.member') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.collection.columns.mandate') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.collection.columns.amount') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.collection.columns.period') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($this->openCandidates as $candidate)
                            @php
                                $feeService = app(\App\Services\FeeService::class);
                                $amount = $feeService->getAmountForMember($candidate);
                            @endphp
                            <flux:table.row>
                                <flux:table.cell class="font-medium">
                                    <a href="{{ route('backend.members.show', $candidate->id) }}" class="hover:underline">
                                        {{ $candidate->fullName() }}
                                    </a>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <code class="text-xs">{{ $candidate->activeSepaMandate->first()?->mandate_reference }}</code>
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ number_format($amount / 100, 2, ',', '.') }} €
                                </flux:table.cell>
                                <flux:table.cell>{{ $this->currentPeriodLabel }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

                <div class="mt-4 text-right font-semibold text-gray-700">
                    {{ __('sepa.collection.total_pending', ['sum' => number_format($this->openCandidates->sum(fn($m) => app(\App\Services\FeeService::class)->getAmountForMember($m)) / 100, 2, ',', '.')]) }} €
                </div>
            @endif
        </flux:card>

    @elseif($selectedTab === 'attempts')
        <flux:card>
            @if($this->unresolvedAttempts->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    {{ __('sepa.collection.no_attempts') }}
                </div>
            @else
                @foreach($this->unresolvedAttempts as $batchReference => $attempts)
                    <div class="mb-6 last:mb-0">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-gray-600">
                                {{ __('sepa.collection.batch') }}: <code>{{ $batchReference }}</code>
                            </h3>
                            @if($batchReference !== 'ohne Batch')
                                <flux:button wire:click="confirmBatch('{{ $batchReference }}')" size="sm" variant="primary">
                                    {{ __('sepa.collection.actions.confirm_batch') }}
                                </flux:button>
                            @endif
                        </div>

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('sepa.collection.columns.member') }}</flux:table.column>
                                <flux:table.column>{{ __('sepa.collection.columns.mandate') }}</flux:table.column>
                                <flux:table.column>{{ __('sepa.collection.columns.amount') }}</flux:table.column>
                                <flux:table.column>{{ __('sepa.collection.columns.status') }}</flux:table.column>
                                <flux:table.column>{{ __('sepa.collection.columns.actions') }}</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @foreach($attempts as $attempt)
                                    <flux:table.row>
                                        <flux:table.cell class="font-medium">
                                            <a href="{{ route('backend.members.show', $attempt->member->id) }}" class="hover:underline">
                                                {{ $attempt->member->fullName() }}
                                            </a>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <code class="text-xs">{{ $attempt->sepaMandate?->mandate_reference }}</code>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            {{ number_format($attempt->amount / 100, 2, ',', '.') }} €
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge color="amber">{{ $attempt->status->value }}</flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:button wire:click="confirmAttempt({{ $attempt->id }})" size="sm">
                                                {{ __('sepa.collection.actions.confirm') }}
                                            </flux:button>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                @endforeach
            @endif
        </flux:card>

    @elseif($selectedTab === 'history')
        <flux:card>
            @if($this->history->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    {{ __('sepa.collection.no_history') }}
                </div>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('sepa.collection.columns.date') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.collection.columns.member') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.collection.columns.amount') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.collection.columns.period') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($this->history as $attempt)
                            <flux:table.row>
                                <flux:table.cell>{{ $attempt->resolved_at?->format('d.m.Y') }}</flux:table.cell>
                                <flux:table.cell class="font-medium">
                                    <a href="{{ route('backend.members.show', $attempt->member->id) }}" class="hover:underline">
                                        {{ $attempt->member->fullName() }}
                                    </a>
                                </flux:table.cell>
                                <flux:table.cell>{{ number_format($attempt->amount / 100, 2, ',', '.') }} €</flux:table.cell>
                                <flux:table.cell>{{ $attempt->period_key }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </flux:card>

    @elseif($selectedTab === 'returns')
        <flux:card>
            @if($this->returns->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    {{ __('sepa.return_debit.no_returns') }}
                </div>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('sepa.return_debit.columns.date') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.return_debit.columns.member') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.return_debit.columns.amount') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.return_debit.columns.reason') }}</flux:table.column>
                        <flux:table.column>{{ __('sepa.return_debit.columns.actions') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($this->returns as $attempt)
                            <flux:table.row>
                                <flux:table.cell>{{ $attempt->resolved_at?->format('d.m.Y') }}</flux:table.cell>
                                <flux:table.cell class="font-medium">
                                    <a href="{{ route('backend.members.show', $attempt->member->id) }}" class="hover:underline">
                                        {{ $attempt->member->fullName() }}
                                    </a>
                                </flux:table.cell>
                                <flux:table.cell>{{ number_format($attempt->amount / 100, 2, ',', '.') }} €</flux:table.cell>
                                <flux:table.cell class="text-sm text-gray-600 max-w-xs truncate">
                                    {{ $attempt->return_reason ?? __('sepa.return_debit.reasons.unknown') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($attempt->member->activeSepaMandate)
                                        <flux:button wire:click="recollect({{ $attempt->id }})" size="sm">
                                            {{ __('sepa.return_debit.actions.recollect') }}
                                        </flux:button>
                                    @else
                                        <flux:badge size="sm" color="red">{{ __('sepa.return_debit.errors.no_active_mandate') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </flux:card>
    @endif
</div>
