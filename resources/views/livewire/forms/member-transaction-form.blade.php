<div>
    @can('create', \App\Models\Membership\MemberTransaction::class)
        <form wire:submit="addTransaction"
              x-data="checkVat"
        >
            <input type="hidden"
                   wire:model="member_id"
            >
            <section class="space-y-6">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <flux:input type="date"
                                wire:model="date"
                                label="{{ __('common.date') }}"
                    />
                    <flux:input wire:model="amount"
                                x-mask:dynamic="$money($input, ',', '.')"
                                label="{{ __('common.amount') }}"
                                @change="formatAmount"
                    />
                </div>

                <!--
                 Zahlungskonto wie Barkasse, Bankkonto oder PayPal
                 -->
                <flux:field>
                    <flux:select wire:model="account_id"
                                 size="sm"
                                  placeholder="{{ __('transaction.form.account.placeholder') }}"
                                 variant="listbox"
                                 clearable
                                 searchable
                    >

                        @foreach(\App\Models\Accounting\Account::select('id', 'name')->get() as $key => $account)
                            <flux:select.option :key
                                                value="{{ $account->id }}"
                            >{{ $account->name }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flex:flux:error name="form.account_id"/>
                </flux:field>
                <!--
    Buchungskonto nach SKR 49
    -->
                <flux:select :placeholder="__('transaction.form.booking_account.placeholder')"
                             wire:model="booking_account_id"
                             size="sm"
                             variant="listbox"
                             clearable
                             searchable
                >
                    @foreach(\App\Models\Accounting\BookingAccount::select('id', 'label', 'number')->get() as $key => $account)
                        <flux:select.option :key
                                            value="{{ $account->id }}"
                        >{{ $account->number }} - {{ $account->label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="label"
                            label="{{ __('common.text') }}"
                            required
                />

                <flux:field>
                    <flux:label>{{ __('transaction.member_transaction.assign_event_label') }}</flux:label>
                    <flux:select wire:model="event_id"
                                 variant="listbox"
                                 searchable
                                 clearable
                                 :placeholder="__('transaction.index.modal.append_event.select_placeholder')"
                    >
                        @foreach($events as $key => $event)
                            <flux:select.option value="{{ $event->id }}">{{$event->title['de']}}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>


                <flux:button variant="primary"
                             type="submit"
                >{{ __('common.submit') }}
                </flux:button>
            </section>
        </form>
    @endcan
</div>
@script


<script>
    Alpine.data('checkVat', () => {
        return {
            formatAmount() {
                let net = this.updateCents(this.$wire.amount) / 100;
                this.$wire.amount = this.maskInput(net)
            },
            updateCents(formattedValue) {
                let value = formattedValue
                    .replace(/[^\d,]/g, '')  // Remove non-numeric characters
                    .replace(',', '.');      // Convert decimal separator

                let floatValue = parseFloat(value);
                return isNaN(floatValue) ? 0 : Math.round(floatValue * 100);
            },
            maskInput(value) {
                return new Intl.NumberFormat('de-DE', {
                    style: 'decimal',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value);
            }
        }
    })
</script>
@endscript
