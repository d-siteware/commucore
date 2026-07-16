<div class="lg:pt-6">
    <x-debug/>
    <div x-data="checkVat">
        <input type="hidden"
               wire:model.blur="form.id"
        >
        <flux:card>
            <section class="space-y-6">
                <section class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <flux:radio.group wire:model.blur="form.type" size="sm"
                                      :label="__('transaction.form.type')"
                                      variant="segmented"
                    >
                        @foreach(\App\Enums\TransactionType::cases() as $key => $type)
                            <flux:radio :key
                                        value="{{ $type->value }}"
                            >{{ $type->label() }}</flux:radio>
                        @endforeach
                    </flux:radio.group>
                    @can('book-item', \App\Models\Accounting\Account::class)
                    <flux:radio.group wire:model.blur="form.status"  size="sm"
                                      :label="__('transaction.form.status')"
                                          variant="segmented"
                        >
                            @foreach(\App\Enums\TransactionStatus::cases() as $key => $status)
                                <flux:radio :key
                                            value="{{ $status->value }}"
                                >{{ $status->label() }}</flux:radio>
                            @endforeach
                        </flux:radio.group>
                    @endcan
                </section>

                <flux:separator :text="__('transaction.form.separator.accounts')"/>

                <section class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <flux:field>
                        <!--
                        Zahlungskonto wie Barkasse, Bankkonto oder PayPal
                        -->
                        <flux:button.group>
                            <flux:select wire:model.blur="form.account_id"
                                         size="sm"
                                         :placeholder="__('transaction.form.account.placeholder')"
                                         variant="listbox"
                                         clearable
                                         searchable
                            >
                                @can('create', \App\Models\Accounting\Account::class)
                                    <flux:select.option value="new">{{ __('transaction.form.account.new') }}</flux:select.option>
                                @endcan
                                @foreach($this->accounts as $key => $account)
                                    <flux:select.option :key
                                                        value="{{ $account->id }}"
                                    >{{ $account->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            @can('create', \App\Models\Accounting\Account::class)
                                <flux:modal.trigger name="add-account-modal"
                                                    x-cloak
                                                    x-show="$wire.form.account_id === 'new'"
                                >
                                    <flux:button size="sm"
                                                 variant="primary"
                                                 icon-trailing="plus"
                                    >{{ __('common.save') }}
                                    </flux:button>
                                </flux:modal.trigger>
                            @endcan

                        </flux:button.group>
                        <flux:error name="form.account_id"/>
                    </flux:field>
                    <flux:button.group>
                        <flux:select :placeholder="__('transaction.form.booking_account.placeholder')"
                                     wire:model.blur="form.booking_account_id"
                                     size="sm"
                                     variant="listbox"
                                     clearable
                                     searchable
                        >
                            @can('create', \App\Models\Accounting\Account::class)
                                <flux:select.option value="new">{{ __('transaction.form.booking_account.new') }}</flux:select.option>
                            @endcan
                            @foreach($this->booking_accounts as $key => $account)
                                <flux:select.option :key
                                                    value="{{ $account->id }}"
                                >{{ $account->number }} - {{ $account->label }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        @can('create', \App\Models\Accounting\Account::class)
                            <flux:modal.trigger name="add-booking-account-modal"
                                                x-cloak
                                                x-show="$wire.form.booking_account_id === 'new'"
                            >
                                <flux:button size="sm"
                                             variant="primary"
                                             icon="plus"
                                />
                            </flux:modal.trigger>
                        @endcan

                    </flux:button.group>
                    <flux:field>
                        <flux:select wire:model.blur="form.area"
                                     size="sm"
                                     variant="listbox"
                                     clearable
                                     :placeholder="__('transaction.form.area.placeholder')"
                        >
                            @foreach(\App\Enums\BookingAccountArea::cases() as $area)
                                <flux:select.option value="{{ $area->value }}">{{ $area->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="form.area"/>
                    </flux:field>
                </section>


                <flux:separator :text="__('transaction.form.separator.amounts')"/>

                <section class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                    <flux:input wire:model.blur="form.amount_gross"
                                x-mask:dynamic="$money($input, ',', '.')"
                                :label="__('transaction.form.amount_gross')"
                                @change="updateValuesFromGross"
                    />

                    <flux:input wire:model.blur="form.vat"
                                :label="__('transaction.form.vat_percent')"
                                @change="updateValuesFromGross"
                    />

                    <flux:input wire:model.blur="form.tax"
                                x-mask:dynamic="$money($input, ',', '.')"
                                @changed="updateValuesFromGross"
                                :label="__('transaction.form.vat_amount')"
                                variant="filled"
                    />

                    <flux:input wire:model.blur="form.amount_net"
                                x-mask:dynamic="$money($input, ',', '.')"
                                :label="__('transaction.form.amount_net')"
                                @change="updateValuesFromNet"
                    />
                </section>

                <flux:separator :text="__('transaction.form.separator.texts')"/>
                <section class="grid grid-cols-1 lg:grid-cols-4 gap-3">

                    <div class="lg:col-span-2">
                        <flux:input :label="__('transaction.form.label')"
                                    wire:model.blur="form.label"
                        />
                    </div>
                    <div class="lg:col-span-2">
                        <flux:input :label="__('transaction.form.reference')"
                                    wire:model.live.blur="form.reference"
                        />
                    </div>

                    <div class="lg:col-span-1">
                        <flux:date-picker locale="{{ app()->getLocale() }}" :label="__('transaction.form.date')"
                                          class="lg:col-span-1"
                                          wire:model.blur="form.date"
                                          start-day="1"
                                          week-numbers
                        />
                    </div>
                    <div class="lg:col-span-1">
                        <flux:select wire:model.blur="form.fiscal_year_id"
                                     variant="listbox"
                                     :label="__('fiscal_year.fiscal_year')"
                                     placeholder="-"
                        >
                            @foreach($this->fiscalYears as $fy)
                                <flux:select.option value="{{ $fy->id }}">
                                    {{ $fy->year }}
                                    @if($fy->isClosed())
                                        <flux:badge size="xs" color="red">{{ __('fiscal_year.closed') }}</flux:badge>
                                    @endif
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        @php
                            $today = \Carbon\Carbon::today();
                            $day = (int) $today->format('d');
                            $month = (int) $today->format('m');
                            $isNearYearEnd = ($month === 12 && $day >= 22)
                                          || ($month === 1 && $day <= 10);
                        @endphp
                        @if($isNearYearEnd)
                            <flux:text size="xs" color="amber" class="mt-1">
                                {{ __('fiscal_year.hint_near_year_end') }}
                            </flux:text>
                        @endif
                    </div>
                    <div class="lg:col-span-2">
                        <flux:input :label="__('transaction.form.description')"
                                    wire:model.blur="form.description"
                        />
                    </div>
                </section>


                <flux:separator text="{{ __('transaction.documents.heading') }}"/>

                <section class="space-y-6">

                    {{-- Kategorie --}}
                    <flux:select wire:model.blur="documentCategory"
                                 variant="listbox"
                                 :label="__('documents.category.label')"
                                 :placeholder="__('documents.category.placeholder')"
                                 clearable
                    >
                        @foreach($this->documentCategories as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    {{-- Optionale Bezeichnung --}}
                    <flux:input wire:model.blur="documentLabel"
                                :label="__('documents.upload.label_field')"
                                :placeholder="__('documents.upload.label_placeholder')"
                    />

                    {{-- Datei-Upload (mehrere, Drag & Drop) --}}
                    <flux:field>
                        <flux:label>{{ __('documents.upload.file_label') }}</flux:label>
                        <flux:description>{{ __('documents.upload.file_hint') }}</flux:description>

                        <div x-data="{ dragOver: false }"
                             x-on:dragover.prevent="dragOver = true"
                             x-on:dragleave="dragOver = false"
                             x-on:drop.prevent="
                dragOver = false;
                $wire.upload('documentFiles', $event.dataTransfer.files)
             "
                             class="relative block w-full rounded-lg border-2 border-dashed p-8 text-center transition-colors"
                             :class="dragOver
                 ? 'border-emerald-400 bg-emerald-50'
                 : 'border-gray-300 hover:border-gray-400'"
                        >
                            {{-- Desktop --}}
                            <input type="file"
                                   wire:model="documentFiles"
                                   multiple
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.tif,.tiff"
                                   class="hidden sm:block absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            />

                            {{-- Mobile (Kamera) --}}
                            <input type="file"
                                   wire:model="documentFiles"
                                   multiple
                                   accept=".pdf,.jpg,.jpeg,.png,.tif,.tiff"
                                   capture="environment"
                                   class="sm:hidden absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            />

                            <div class="pointer-events-none space-y-1">
                                <flux:icon.arrow-up-tray class="mx-auto size-8 text-gray-400"/>
                                <flux:text class="text-sm text-gray-500">
                                    {{ __('documents.upload.drag_hint') }}
                                </flux:text>
                            </div>
                        </div>

                        <flux:error name="documentFiles"/>
                        <flux:error name="documentFiles.*"/>
                    </flux:field>

                    {{-- Ladeindikator --}}
                    <div wire:loading
                         wire:target="documentFiles"
                         class="text-sm text-gray-500 flex items-center gap-2"
                    >
                        <flux:icon.arrow-path class="size-4 animate-spin"/>
                        {{ __('documents.upload.loading') }}
                    </div>

                    {{-- Vorschau gewählter Dateien --}}
                    @if(!empty($documentFiles))
                        <div class="space-y-1">
                            @foreach($documentFiles as $file)
                                <div class="flex items-center gap-2 text-sm text-gray-700">
                                    <flux:icon.paper-clip class="size-4 text-gray-400 shrink-0"/>
                                    <span class="truncate">{{ $file->getClientOriginalName() }}</span>
                                    <span class="text-gray-400 shrink-0">
                                        ({{ number_format($file->getSize() / 1024, 1) }} KB)
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <div class="flex gap-3">

                    <flux:spacer/>
                    <flux:error name="transaction.id"/>
                    <flux:button wire:click="resetTransactionForm">{{ __('transaction.form.btn.new') }}</flux:button>
                    @if(isset($event))
                        <flux:button wire:click="submitEventTransaction"
                                     variant="primary"
                        >{{ __('transaction.form.btn.save_event') }}
                        </flux:button>
                    @elseif(isset($member))
                        <flux:button wire:click="submitMemberTransaction"
                                     variant="primary"
                        >{{ __('transaction.form.btn.save_member') }}
                        </flux:button>
                    @else
                        <flux:button wire:click="submitTransaction"
                                     variant="primary"
                        >{{ __('transaction.form.btn.save') }}
                        </flux:button>
                    @endif

                </div>
            </section>
        </flux:card>

        @script
        <script>

            Alpine.data('checkVat', () => {
                return {
                    updateValuesFromGross() {
                        // Parse gross amount (in formatted string, e.g., "11,96") to cents
                        let grossCents = this.updateCents(this.$wire.form.amount_gross);
                        let vat = parseFloat(this.$wire.form.vat) || 0; // VAT percentage, e.g., 19

                        // Calculate tax (VAT amount) in cents: tax = gross * vat / (100 + vat)
                        let taxCents = Math.round((grossCents * vat) / (100 + vat));
                        // Calculate net amount in cents: net = gross - tax
                        let netCents = grossCents - taxCents;

                        // Format back to decimal strings for display
                        this.$wire.form.tax = this.maskInput(taxCents / 100);
                        this.$wire.form.amount_net = this.maskInput(netCents / 100);
                        this.$wire.form.amount_gross = this.maskInput(grossCents / 100);
                    },

                    updateValuesFromNet() {
                        // Parse net amount (in formatted string, e.g., "9,69") to cents
                        let netCents = this.updateCents(this.$wire.form.amount_net);
                        let vat = parseFloat(this.$wire.form.vat) || 0; // VAT percentage, e.g., 19

                        // Calculate gross amount in cents: gross = net * (1 + vat/100)
                        let grossCents = Math.round(netCents * (100 + vat) / 100);
                        // Calculate tax in cents: tax = gross - net
                        let taxCents = grossCents - netCents;

                        // Format back to decimal strings for display
                        this.$wire.form.tax = this.maskInput(taxCents / 100);
                        this.$wire.form.amount_gross = this.maskInput(grossCents / 100);
                        this.$wire.form.amount_net = this.maskInput(netCents / 100);
                    },

                    updateCents(formattedValue) {
                        // Convert formatted string (e.g., "11,96") to cents
                        let value = (formattedValue || '0')
                            .replace(/[^\d,]/g, '')  // Remove non-numeric characters except comma
                            .replace(',', '.');      // Convert comma to dot for decimal
                        let floatValue = parseFloat(value) || 0;
                        return Math.round(floatValue * 100); // Convert to cents and round
                    },

                    maskInput(value) {
                        // Format number to German decimal format (e.g., 11.96 -> "11,96")
                        return new Intl.NumberFormat('de-DE', {
                            style: 'decimal',
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(value);
                    }
                }
            })

            function handleFileDrop(event) {
                let file = event.dataTransfer.files[0];
                if (file) {
                    Livewire.emit('fileDropped', file);
                }
            }

        </script>
        @endscript
    </div>

    <aside>
        <flux:modal name="add-account-modal"
                    variant="flyout"
                    class="space-y-6"
                    position="left"
        >
            <div>
                <flux:heading size="lg">{{ __('transaction.modal.account.heading') }}</flux:heading>
            </div>

            <form wire:submit="addAccount"
                  class="space-y-2"
            >

                <flux:field>
                    <flux:select :placeholder="__('transaction.modal.account.type_placeholder')"
                                 wire:model.blur="account.type"
                                 size="sm"
                                 variant="listbox"
                    >
                        @foreach(\App\Enums\AccountType::cases() as $type)
                            <flux:select.option value="{{ $type->value }}"
                            >{{ $type->value }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="account.type"/>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('transaction.modal.account.name') }}</flux:label>
                    <flux:input wire:model.blur="account.name"
                                required
                    />
                    <flux:error for="account.name"/>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('transaction.modal.account.number') }}</flux:label>
                    <flux:input wire:model.blur="account.number"
                                required
                    />
                    <flux:error for="account.number"/>
                </flux:field>

                <flux:input wire:model.blur="account.starting_amount"
                            x-mask:dynamic="$money($input, ',', '.')"
                            :label="__('transaction.modal.account.starting_amount')"
                />

                <flux:input :label="__('transaction.modal.account.institute')"
                            wire:model.blur="account.institute"
                />

                <flux:input :label="__('transaction.modal.account.iban')"
                            wire:model.blur="account.iban"
                />

                <flux:input :label="__('transaction.modal.account.bic')"
                            wire:model.blur="account.bic"
                />

                <div class="flex justify-between items-center flex-col sm:flex-row gap-3">

                    <flux:button wire:click="createAccount">{{ __('transaction.modal.account.btn.save_and_continue') }}</flux:button>

                    <flux:button type="submit"
                                 variant="primary"
                    >{{ __('transaction.modal.account.btn.save_and_select') }}
                    </flux:button>

                </div>
            </form>
        </flux:modal>

        <flux:modal name="add-booking-account-modal"
                    variant="flyout"
                    class="space-y-6"
                    position="left"
        >
            <div>
                <flux:heading size="lg">{{ __('transaction.modal.booking.heading') }}</flux:heading>
            </div>

            <form wire:submit="addBookingAccount"
                  class="space-y-2"
            >
                {{-- Angezeigter Kontenrahmen (wird unsichtbar zugeordnet) --}}
                @php $activeType = \App\Models\Accounting\FiscalYear::contextFiscalYear()?->bookingAccountType; @endphp
                @if ($activeType)
                    <flux:field>
                        <flux:label>{{ __('transaction.modal.booking.type_label') }}</flux:label>
                        <flux:input value="{{ $activeType->name }}"
                                    readonly
                        />
                    </flux:field>
                @endif

                {{-- Kontenart --}}
                <flux:field>
                    <flux:label>{{ __('transaction.modal.booking.category_label') }}</flux:label>
                    <flux:select :placeholder="__('transaction.modal.booking.category_placeholder')"
                                 wire:model.blur="booking.category"
                                 variant="listbox"
                    >
                        @foreach(\App\Enums\AccountCategory::cases() as $cat)
                            <flux:select.option value="{{ $cat->value }}">{{ $cat->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error for="booking.category"/>
                </flux:field>

                {{-- Steuerliche Sphäre --}}
                <flux:field>
                    <flux:label>{{ __('transaction.modal.booking.area_label') }}</flux:label>
                    <flux:select :placeholder="__('transaction.modal.booking.area_placeholder')"
                                 wire:model.blur="booking.area"
                                 variant="listbox"
                    >
                        @foreach(\App\Enums\BookingAccountArea::cases() as $area)
                            <flux:select.option value="{{ $area->value }}">{{ $area->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error for="booking.area"/>
                </flux:field>

                {{-- Untertyp (optional, nur für Zahlungsmittel/Forderungen/Verbindlichkeiten) --}}
                <flux:field>
                    <flux:label>{{ __('transaction.modal.booking.subtype_label') }}
                        <flux:badge size="sm"
                                    variant="pill"
                        >optional
                        </flux:badge>
                    </flux:label>
                    <flux:select :placeholder="__('transaction.modal.booking.subtype_placeholder')"
                                 wire:model.blur="booking.subtype"
                                 variant="listbox"
                                 clearable
                    >
                        @foreach(\App\Enums\AccountSubtype::cases() as $sub)
                            <flux:select.option value="{{ $sub->value }}">{{ $sub->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error for="booking.subtype"/>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('transaction.modal.booking.label') }}</flux:label>
                    <flux:input wire:model.blur="booking.label"
                                required
                    />
                    <flux:error for="booking.label"/>
                </flux:field>

                <flux:field>
                    <flux:input :label="__('transaction.modal.booking.number')"
                                wire:model.blur="booking.number"
                    />
                    <flux:error name="booking.number"/>
                </flux:field>

                <div class="flex justify-between items-center flex-col sm:flex-row gap-3">
                    <flux:button wire:click="createBookingAccount">{{ __('transaction.modal.booking.btn.save_and_continue') }}</flux:button>
                    <flux:button type="submit"
                                 variant="primary"
                    >{{ __('transaction.modal.booking.btn.save_and_select') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>

        <flux:modal name="missing-transaction-modal"
                    class="md:w-96 space-y-6"
        >
            <div>
                <flux:heading size="lg">{{ __('transaction.modal.missing.heading') }}</flux:heading>
                <flux:subheading>{{ __('transaction.modal.missing.text') }}</flux:subheading>
            </div>
        </flux:modal>
    </aside>

</div>
