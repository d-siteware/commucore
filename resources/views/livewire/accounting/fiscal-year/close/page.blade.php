<div>
    <flux:heading size="xl"
                  class="mb-6"
    >
        {{ __('Jahresabschluss') }} {{ $year }}
    </flux:heading>

    @if($this->transactionCount === 0)
        <flux:card>
            <flux:text>
                {{ __('Keine offenen Transaktionen für das Jahr :year gefunden.', ['year' => $year]) }}
            </flux:text>
            <flux:button variant="ghost"
                         wire:click="cancel"
                         class="mt-4"
            >
                {{ __('Zurück') }}
            </flux:button>
        </flux:card>
    @else
        <flux:card class="mb-6">
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <flux:heading size="lg">{{ __('Transaktionen auswählen') }}</flux:heading>
                    <div class="text-sm text-gray-600">
                        {{ $this->selectedCount }} {{ __('von') }} {{ $this->transactionCount }} {{ __('ausgewählt') }}
                    </div>
                </div>

                @if($this->selectedCount > 0)
                    <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm text-gray-600">{{ __('Einnahmen') }}</div>
                            <div class="text-lg font-semibold text-green-600">
                                {{ number_format($this->totalIncome, 2, ',', '.') }} €
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('Ausgaben') }}</div>
                            <div class="text-lg font-semibold text-red-600">
                                {{ number_format($this->totalExpense, 2, ',', '.') }} €
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">{{ __('Saldo') }}</div>
                            <div class="text-lg font-semibold {{ $this->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($this->balance, 2, ',', '.') }} €
                            </div>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input
                                        type="checkbox"
                                        wire:model.live="selectAll"
                                        class="rounded border-gray-300"
                                />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('Datum') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('Beschreibung') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('Konto') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('Typ') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                {{ __('Betrag') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $index =>  $transaction)
                            <tr
                                    class="cursor-pointer transition-colors"
                                    :class="{
                                        'bg-accent/50 hover:bg-accent/10': @js(in_array($transaction->id, $selectedTransactions)),
                                        '': @js(!in_array($transaction->id, $selectedTransactions))
                                    }"
                                    @click="(event) => {
                                        // Verhindere doppeltes Toggle wenn auf Checkbox geklickt wurde
                                        if (event.target.type === 'checkbox') return;

                                        $wire.toggleTransaction(
                                            {{ $transaction->id }},
                                            {{ $index }},
                                            event.shiftKey
                                        );
                                    }"
                            >
                                <td class="px-4 py-3">
                                    <input
                                            type="checkbox"
                                            value="{{ $transaction->id }}"
                                            @checked(in_array($transaction->id, $selectedTransactions))
                                            class="rounded border-gray-300"
                                            @change="(event) => {
                                                $wire.toggleTransaction(
                                                    {{ $transaction->id }},
                                                    {{ $index }},
                                                    event.shiftKey
                                                );
                                            }"
                                    />
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $transaction->date->format('d.m.Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $transaction->label }}
                                    @if($transaction->member_transaction?->member)
                                        <span class="text-xs text-gray-500">
                                                ({{ $transaction->member_transaction->member->fullName() }})
                                            </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $transaction->account?->name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <flux:badge
                                            color="{{ \App\Enums\TransactionType::badgeColor($transaction->type) }}"
                                            size="sm"
                                    >
                                        {{ __($transaction->type) }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-mono">
                                        <span class="{{ \App\Enums\TransactionType::color($transaction->type) }}">
                                            {{ $transaction->type === \App\Enums\TransactionType::Deposit->value ? '+' : '-' }}{{ number_format($transaction->amount_net / 100, 2, ',', '.') }} €
                                        </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </flux:card>

        <div class="flex justify-between items-center">
            <flux:button variant="ghost"
                         wire:click="cancel"
            >
                {{ __('Abbrechen') }}
            </flux:button>

            <flux:button
                    variant="primary"
                    wire:click="showConfirmationModal"
                    :disabled="$this->selectedCount === 0"
            >
                {{ __('Jahresabschluss durchführen') }}
            </flux:button>
        </div>
    @endif

    {{-- Confirmation Modal --}}
    @if($showConfirmation)
        <flux:modal wire:model="showConfirmation">
            <flux:heading size="lg"
                          class="mb-6"
            >
                {{ __('Jahresabschluss bestätigen') }}
            </flux:heading>

            <div class="space-y-6">
                <flux:text>
                    {{ __('Sie sind dabei, das Geschäftsjahr :year mit :count Transaktionen abzuschließen.', [
                        'year' => $year,
                        'count' => $this->selectedCount
                    ]) }}
                </flux:text>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400"
                                 viewBox="0 0 20 20"
                                 fill="currentColor"
                            >
                                <path fill-rule="evenodd"
                                      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                      clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                {{ __('Wichtige Hinweise') }}
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>{{ __('Die ausgewählten Transaktionen werden gesperrt') }}</li>
                                    <li>{{ __('Das Geschäftsjahr :year wird geschlossen', ['year' => $year]) }}</li>
                                    <li>{{ __('Ein neues Geschäftsjahr :year wird angelegt', ['year' => $nextYear]) }}</li>
                                    <li class="font-semibold">{{ __('Dieser Vorgang kann nur von Administratoren rückgängig gemacht werden') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <flux:checkbox wire:model.live="confirmClose"
                               label="__('Ich bestätige, dass ich den Jahresabschluss durchführen möchte')"
                />

                @error('confirm')
                <flux:error>{{ $message }}</flux:error>
                @enderror


                <flux:button variant="ghost"
                             wire:click="$set('showConfirmation', false)"
                >
                    {{ __('Abbrechen') }}
                </flux:button>
                <flux:button
                        variant="danger"
                        wire:click="close"
                        :disabled="!$confirmClose"
                >
                    {{ __('Jetzt abschließen') }}
                </flux:button>
            </div>
        </flux:modal>
    @endif
</div>