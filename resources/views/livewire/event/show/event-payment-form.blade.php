
<div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form wire:submit="addEventPayment">

        <section class="space-y-3 mb-6">

            <flux:input type="date"
                        wire:model.blur="transactionForm.date"
                        :label="__('event.payment.date')"
                        size="sm"
            />

            <flux:radio.group wire:model.live="transactionForm.type"
                              :label="__('event.payment.type')"
                              variant="segmented"
            >
                @foreach(App\Enums\TransactionType::cases() as $key => $type)
                    <flux:radio :key
                                value="{{ $type->value }}"
                    >{{ $type->value }}</flux:radio>
                @endforeach
            </flux:radio.group>

            <flux:field>
                <flux:select wire:model="transactionForm.account_id"
                             size="sm"
                             :placeholder="__('event.payment.account_placeholder')"
                             variant="listbox"
                             clearable
                             searchable
                >
                    @foreach($this->accounts as $key => $account)
                        <flux:select.option :key
                                            value="{{ $account->id }}"
                        >{{ $account->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flex:flux:error name="form.account_id"/>
            </flux:field>

            <flux:select :placeholder="__('event.payment.booking_account_placeholder')"
                         wire:model.blur="transactionForm.booking_account_id"
                         size="sm"
                         variant="listbox"
                         clearable
                         searchable
            >
                @foreach($this->booking_accounts as $key => $account)
                    <flux:select.option :key
                                        value="{{ $account->id }}"
                    >{{ $account->number }} - {{ $account->label }}</flux:select.option>
                @endforeach
            </flux:select>


            <flux:input :label="__('event.payment.label')"
                        wire:model.blur="transactionForm.label"
            />

            <flux:input wire:model.blur="transactionForm.amount_gross"
                        x-mask:dynamic="$money($input, ',', '.')"
                        :label="__('event.payment.entry_fee')"
                        @change="updateValuesFromGross"
            />

            <flux:switch wire:model.live="setEntryFee"
                         :label="__('event.payment.entry_fee_discounted')"
            />


            <flux:field>
                <flux:select wire:model="member_id"
                             variant="listbox"
                             searchable
                             :placeholder="__('event.payment.member_list_placeholder')"
                >
                    <flux:select.option value="extern">{{ __('event.payment.external') }}</flux:select.option>
                    @foreach($this->members as $member)
                        <flux:select.option value="{{ $member->id }}"
                                            wire:key="{{ $member->id }}"
                        >{{ $member->fullName() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
        </section>

        <flux:button variant="primary"
                     wire:click="storePayment"
        >{{ __('event.payment.btn_store') }}
        </flux:button>
    </form>
</div>
