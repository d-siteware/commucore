<div>
    <form wire:submit="updateBookingStatus"
          class="space-y-6"
    >
        <input type="hidden"
               wire:model.blur="form.id"
        >
        <flux:heading size="lg">{{ __('transaction.booking.heading') }}</flux:heading>
        <flux:radio.group wire:model="form.status"
                          label="Status setzen"
                          variant="segmented"
        >
            @foreach(\App\Enums\TransactionStatus::cases() as $key => $type)
                <flux:radio :key
                            value="{{ $type->value }}"
                >{{ $type->value }}</flux:radio>
            @endforeach
        </flux:radio.group>

        <flux:select :placeholder="__('transaction.form.booking_account.placeholder')"
                     :label="__('transaction.booking.label')"
                     wire:model.live="form.booking_account_id"
                     size="sm"
                     variant="listbox"
                     clearable
                     searchable
        >
            @can('create', \App\Models\Accounting\Account::class)
                <flux:select.option value="new">{{ __('transaction.booking.new_booking_account') }}</flux:select.option>
            @endcan
            @foreach($bookingAccountList as $key => $account)
                <flux:select.option :key
                                    value="{{ $account->id }}"
                >{{ $account->number }} - {{ $account->label }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:button type="submit"
                     variant="primary"
        >{{ __('transaction.booking.submit') }}
        </flux:button>


    </form>

</div>
