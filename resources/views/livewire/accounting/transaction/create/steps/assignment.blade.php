<section class="space-y-5">
    <header>
        <flux:heading size="lg">{{ __('transaction.create.steps.assignment') }}</flux:heading>
        <flux:text class="mt-1">{{ __('transaction.create.steps.assignment_intro') }}</flux:text>
    </header>

    @can('book-item', \App\Models\Accounting\Account::class)
        <flux:radio.group wire:model.live="form.status" size="sm" :label="__('transaction.form.status')" variant="segmented">
            @foreach (\App\Enums\TransactionStatus::cases() as $status)
                <flux:radio :key="$status->value" value="{{ $status->value }}">{{ $status->label() }}</flux:radio>
            @endforeach
        </flux:radio.group>
    @endcan

    <flux:separator :text="__('transaction.form.separator.accounts')" />

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <flux:field>
            <flux:label>{{ __('transaction.form.account.placeholder') }}</flux:label>
            <flux:button.group>
                <flux:select
                    wire:model="form.account_id"
                    :placeholder="__('transaction.form.account.placeholder')"
                    variant="listbox"
                    clearable
                    searchable
                >
                    @can('create', \App\Models\Accounting\Account::class)
                        <flux:select.option value="new">{{ __('transaction.form.account.new') }}</flux:select.option>
                    @endcan
                    @foreach ($this->accounts as $account)
                        <flux:select.option :key="$account->id" value="{{ $account->id }}">{{ $account->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                @can('create', \App\Models\Accounting\Account::class)
                    <flux:modal.trigger name="add-account-modal" x-cloak x-show="$wire.form.account_id === 'new'">
                        <flux:button size="sm" variant="primary" icon="plus" :aria-label="__('transaction.form.account.new')" />
                    </flux:modal.trigger>
                @endcan
            </flux:button.group>
            <flux:error name="form.account_id" />
        </flux:field>

        <flux:field>
            <flux:select
                wire:model="form.area"
                :label="__('transaction.form.area.placeholder')"
                variant="listbox"
                clearable
                :placeholder="__('transaction.form.area.placeholder')"
            >
                @foreach (\App\Enums\BookingAccountArea::cases() as $area)
                    <flux:select.option value="{{ $area->value }}">{{ $area->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:description>{{ __('transaction.create.steps.area_filters_booking_accounts') }}</flux:description>
            <flux:error name="form.area" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('transaction.form.booking_account.placeholder') }}</flux:label>
            <flux:button.group>
                <flux:select
                    wire:model="form.booking_account_id"
                    :placeholder="__('transaction.form.booking_account.placeholder')"
                    variant="listbox"
                    clearable
                    searchable
                >
                    @can('create', \App\Models\Accounting\Account::class)
                        <flux:select.option value="new">{{ __('transaction.form.booking_account.new') }}</flux:select.option>
                    @endcan
                    @foreach ($this->booking_accounts as $account)
                        <flux:select.option :key="$account->id" value="{{ $account->id }}">{{ $account->number }} - {{ $account->label }}</flux:select.option>
                    @endforeach
                </flux:select>
                @can('create', \App\Models\Accounting\Account::class)
                    <flux:modal.trigger name="add-booking-account-modal" x-cloak x-show="$wire.form.booking_account_id === 'new'">
                        <flux:button size="sm" variant="primary" icon="plus" />
                    </flux:modal.trigger>
                @endcan
            </flux:button.group>
        </flux:field>

        <flux:field>
            <flux:select
                wire:model="form.fiscal_year_id"
                :label="__('fiscal_year.fiscal_year')"
                variant="listbox"
                placeholder="-"
            >
                @foreach ($this->fiscalYears as $fiscalYear)
                    <flux:select.option value="{{ $fiscalYear->id }}">
                        {{ $fiscalYear->year }}
                        @if ($fiscalYear->isClosed())
                            <flux:badge size="xs" color="red">{{ __('fiscal_year.closed') }}</flux:badge>
                        @endif
                    </flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="form.fiscal_year_id" />
        </flux:field>
    </section>
</section>
