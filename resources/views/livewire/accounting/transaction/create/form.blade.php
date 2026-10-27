<div class="lg:pt-6">
    <x-debug/>
    <div x-data="checkVat">
        <input type="hidden"
               wire:model.blur="form.id"
        >
        <flux:card>
            <section class="space-y-6">
                <section class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <flux:radio.group wire:model.live="form.type" size="sm"
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
                    <flux:radio.group wire:model.live="form.status"  size="sm"
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
<flux:select wire:model="form.account_id"
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
                                     wire:model="form.booking_account_id"
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
                        <flux:select wire:model="form.area"
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
                        <flux:select wire:model="form.fiscal_year_id"
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
                    <flux:select wire:model="documentCategory"
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

        @include('livewire.accounting.transaction.create.partials.vat-script')
    </div>

    @include('livewire.accounting.transaction.create.partials.account-modals')

</div>
