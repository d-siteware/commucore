<aside>
    <flux:modal name="add-account-modal" variant="flyout" class="space-y-6" position="left">
        <div>
            <flux:heading size="lg">{{ __('transaction.modal.account.heading') }}</flux:heading>
        </div>

        <form wire:submit="addAccount" class="space-y-2">
            <flux:field>
                <flux:select :placeholder="__('transaction.modal.account.type_placeholder')" wire:model="account.type" size="sm" variant="listbox">
                    @foreach (\App\Enums\AccountType::cases() as $type)
                        <flux:select.option value="{{ $type->value }}">{{ $type->value }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="account.type" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('transaction.modal.account.name') }}</flux:label>
                <flux:input wire:model.blur="account.name" required />
                <flux:error for="account.name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('transaction.modal.account.number') }}</flux:label>
                <flux:input wire:model.blur="account.number" required />
                <flux:error for="account.number" />
            </flux:field>

            <flux:input wire:model.blur="account.starting_amount" x-mask:dynamic="$money($input, ',', '.')" :label="__('transaction.modal.account.starting_amount')" />
            <flux:input :label="__('transaction.modal.account.institute')" wire:model.blur="account.institute" />
            <flux:input :label="__('transaction.modal.account.iban')" wire:model.blur="account.iban" />
            <flux:input :label="__('transaction.modal.account.bic')" wire:model.blur="account.bic" />

            <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
                <flux:button wire:click="createAccount">{{ __('transaction.modal.account.btn.save_and_continue') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('transaction.modal.account.btn.save_and_select') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="add-booking-account-modal" variant="flyout" class="space-y-6" position="left">
        <div>
            <flux:heading size="lg">{{ __('transaction.modal.booking.heading') }}</flux:heading>
        </div>

        <form wire:submit="addBookingAccount" class="space-y-2">
            @php($activeType = \App\Models\Accounting\FiscalYear::contextFiscalYear()?->bookingAccountType)
            @if ($activeType)
                <flux:field>
                    <flux:label>{{ __('transaction.modal.booking.type_label') }}</flux:label>
                    <flux:input value="{{ $activeType->name }}" readonly />
                </flux:field>
            @endif

            <flux:field>
                <flux:label>{{ __('transaction.modal.booking.category_label') }}</flux:label>
                <flux:select :placeholder="__('transaction.modal.booking.category_placeholder')" wire:model="booking.category" variant="listbox">
                    @foreach (\App\Enums\AccountCategory::cases() as $category)
                        <flux:select.option value="{{ $category->value }}">{{ $category->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error for="booking.category" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('transaction.modal.booking.area_label') }}</flux:label>
                <flux:select :placeholder="__('transaction.modal.booking.area_placeholder')" wire:model="booking.area" variant="listbox">
                    @foreach (\App\Enums\BookingAccountArea::cases() as $area)
                        <flux:select.option value="{{ $area->value }}">{{ $area->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error for="booking.area" />
            </flux:field>

            <flux:field>
                <flux:label>
                    {{ __('transaction.modal.booking.subtype_label') }}
                    <flux:badge size="sm" variant="pill">{{ __('transaction.modal.booking.subtype_optional') }}</flux:badge>
                </flux:label>
                <flux:select :placeholder="__('transaction.modal.booking.subtype_placeholder')" wire:model="booking.subtype" variant="listbox" clearable>
                    @foreach (\App\Enums\AccountSubtype::cases() as $subtype)
                        <flux:select.option value="{{ $subtype->value }}">{{ $subtype->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error for="booking.subtype" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('transaction.modal.booking.label') }}</flux:label>
                <flux:input wire:model.blur="booking.label" required />
                <flux:error for="booking.label" />
            </flux:field>

            <flux:field>
                <flux:input :label="__('transaction.modal.booking.number')" wire:model.blur="booking.number" />
                <flux:error name="booking.number" />
            </flux:field>

            <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
                <flux:button wire:click="createBookingAccount">{{ __('transaction.modal.booking.btn.save_and_continue') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('transaction.modal.booking.btn.save_and_select') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="missing-transaction-modal" class="space-y-6 md:w-96">
        <div>
            <flux:heading size="lg">{{ __('transaction.modal.missing.heading') }}</flux:heading>
            <flux:subheading>{{ __('transaction.modal.missing.text') }}</flux:subheading>
        </div>
    </flux:modal>
</aside>
