<div>
    @if(! app()->isProduction())
        <x-debug/>
{{--        @dump($isExternalMemberApplication)--}}
{{--        @dump($turnstile)--}}
    @endif
    <form wire:submit="store">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
            <flux:card class="space-y-6 mb-3 lg:mb-6">
                <flux:separator text="{{ __('members.section.person') }}"/>
                <flux:input wire:model="form.name"
                            label="{{ __('members.name') }}*"
                            autocomplete="family-name"
                />

                <flux:input wire:model="form.first_name"
                            label="{{ __('members.first_name') }}"
                            autocomplete="given-name"
                />

                <flux:accordion transition>
                    <flux:accordion.item heading="{{ __('members.accordion.optionals.label') }}"
                                         expanded
                    >

                        <section class="space-y-6 mt-3">
                            <flux:text>{{ __('members.optional-data.text') }}</flux:text>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                                <flux:date-picker selectable-header
                                                  with-today
                                                  start-day="1"
                                                  wire:model="form.birth_date"
                                                  wire:blur="checkBirthDate"
                                                  label="{{ __('members.birth_date') }}"
                                                  autocomplete="bday"
                                                  placeholder=""
                                />
                                <flux:input wire:model="form.birth_place"
                                            label="{{ __('members.birth_place') }}"
                                            autocomplete="address-level1"
                                />
                            </div>


                            <flux:radio.group wire:model="form.locale"
                                              label="{{ __('members.locale') }}"
                                              variant="segmented"
                                              size="sm"
                            >
                                @foreach(\App\Enums\Locale::toArray() as $key => $locale)
                                    <flux:radio :key
                                                value="{{ $locale }}"
                                                label="{{ \App\Enums\Locale::value($locale)  }}"
                                    />
                                @endforeach
                            </flux:radio.group>

                            <flux:radio.group wire:model="form.gender"
                                              label="{{ __('members.gender') }}"
                                              variant="segmented"
                                              size="sm"
                            >
                                @foreach(\App\Enums\Gender::toArray() as $key => $gender)
                                    <flux:radio :key
                                                value="{{ $gender }}"
                                    >{{ \App\Enums\Gender::value($gender) }}</flux:radio>
                                @endforeach
                            </flux:radio.group>

                            <div class="hidden lg:block">
                                <flux:radio.group wire:model="form.family_status"
                                                  label="{{ __('members.familystatus.label') }}"
                                                  variant="segmented"
                                                  size="sm"
                                >
                                    @foreach(\App\Enums\MemberFamilyStatus::cases() as $key => $status)
                                        <flux:radio :key
                                                    value="{{ $status->value }}"
                                        >{{ \App\Enums\MemberFamilyStatus::value($status->value) }}</flux:radio>
                                    @endforeach
                                </flux:radio.group>
                            </div>
                            <div class="lg:hidden">
                                <flux:select wire:model="form.family_status"
                                             label="{{ __('members.familystatus.label') }}"
                                >
                                    @foreach(\App\Enums\MemberFamilyStatus::cases() as $key => $status)
                                        <flux:select.option :key
                                                            value="{{ $status->value }}"
                                        >{{ \App\Enums\MemberFamilyStatus::value($status->value) }}</flux:select.option>
                                    @endforeach

                                </flux:select>
                            </div>

                        </section>

                    </flux:accordion.item>
                </flux:accordion>
                <flux:separator text="{{ __('members.section.address') }}"/>

                <flux:textarea wire:model="form.address"
                               rows="auto"
                               label="{{ __('members.address') }}"
                               autocomplete="street-address"
                />

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-3">
                    <flux:input wire:model="form.zip"
                                label="{{ __('members.zip') }}"
                                autocomplete="postal-code"
                    />
                    <div class="lg:col-span-3">
                        <flux:input wire:model="form.city"
                                    label="{{ __('members.city') }}"
                                    autocomplete="address-level1"

                        />
                    </div>

                    <div class="col-span-1">
                        <flux:input wire:model="form.country"
                                    label="{{ __('members.country') }}"
                                    autocomplete="country-name"
                        />
                    </div>
                </div>


            </flux:card>

            <flux:card class="space-y-6 mb-3 lg:mb-6">

                <flux:separator text="{{ __('members.section.phone') }}"/>

                <flux:input wire:model="form.phone"
                            label="{{ __('members.phone') }}"
                            mask="+99 99 99999999"
                            placeholder="+49 30 12345678"
                            autocomplete="tel"
                />

                <flux:input wire:model="form.mobile"
                            label="{{ __('members.mobile') }}"
                            mask="+99 999 99999999"
                            placeholder="+49 173 12345678"
                            autocomplete="tel"
                />

                @if($isExternalMemberApplication)
                    <flux:separator text="{{ __('members.section.fees') }}"/>

                    <flux:text>{{ __('members.apply.full_fee.label', ['sum' => \App\Enums\MembershipFee::FULL->value/100 ]) }}</flux:text>
                    <flux:text>{{ __('members.apply.free_fee.label', ['sum' => \App\Enums\MembershipFee::FREE->value/100 , 'age' =>  App\Models\Membership\Member::$age_free]) }}</flux:text>
                    <flux:separator text="{{ __('members.section.payments') }}"/>

                    @if($bankAccounts->count() >1)
                        <flux:text>{{ __('members.apply.fee.payment.banktts') }} <br/>
                            @foreach($bankAccounts as $account)
                                {{ $account->iban }}
                            @endforeach
                        </flux:text>
                    @elseif($bankAccounts->count() === 1)
                        <flux:text>{{ __('members.apply.fee.payment.banktt') }}</flux:text>
                        <flux:text>Konto: {{ setting('organization.name') }}<br>
                            IBAN: {{ $bankAccounts->first()->iban }}<br>
                            BIC/SWIF:T {{ $bankAccounts->first()->bic }}</flux:text>
                    @endif

                    @if($payPalAccounts->count() >1)
                        <flux:text>{{ __('members.apply.fee.payment.paypals') }}</flux:text>
                        <ul class="list-none">
                            @foreach($payPalAccounts as $account)
                                <li>{{ $account->iban }}</li>
                            @endforeach
                        </ul>
                    @elseif($payPalAccounts->count() === 1)
                        <flux:text>{{ __('members.apply.fee.payment.paypal', ['iban' => $payPalAccounts->first()->iban]) }}</flux:text>
                    @endif
                @endif

                <flux:separator text="{{ __('members.section.deduction') }}"/>
                <flux:text>{{ __('members.apply.discounted_fee.label', ['sum' => \App\Enums\MembershipFee::DISCOUNTED->value/100]) }}</flux:text>

                <flux:checkbox wire:model="form.is_deducted"
                               label="{{ __('members.apply.discount.label') }}"
                />
                <flux:textarea wire:model="form.deduction_reason"
                               rows="auto"
                               label="{{ __('members.apply.discount.reason.label') }}"
                />

                <flux:separator text="{{ __('members.section.email') }}"/>

                <flux:text>{{ __('members.apply.email.benefits') }}</flux:text>

                <flux:input wire:model="form.email"
                            wire:blur="checkEmail"
                            label="{{ __('members.email') }}"
                            autocomplete="email"
                />

                <flux:checkbox wire:model.live="nomail"
                               label="{{ __('members.apply.email.none') }}"
                />

                <flux:text x-show="$wire.nomail">{{ __('members.apply.email.without.text') }}</flux:text>

                @can('create', \App\Models\Membership\Member::class)

                    <flux:separator text="{{ __('members.section.admins') }}"/>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <flux:date-picker selectable-header
                                          with-today
                                          wire:model="form.applied_at"
                                          label="{{ __('members.date.applied_at') }}"
                        />

                        <flux:date-picker selectable-header
                                          with-today
                                          wire:model="form.entered_at"
                                          label="{{ __('members.date.entered_at') }}"
                        />

                    </div>
                    <div class="hidden lg:block">
                        <flux:radio.group wire:model="form.type"
                                          label="{{ __('members.type') }}"
                                          variant="segmented"
                        >
                            @foreach(\App\Enums\MemberType::cases() as $key => $type)
                                <flux:radio :key
                                            value="{{ $type->value }}"
                                >{{ \App\Enums\MemberType::value($type->value) }}</flux:radio>
                            @endforeach

                        </flux:radio.group>
                    </div>

                    <div class="lg:hidden">
                        <flux:select wire:model="form.type"
                                     label="{{ __('members.type') }}"
                        >
                            @foreach(\App\Enums\MemberType::cases() as $key => $type)
                                <flux:select.option :key
                                                    value="{{ $type->value }}"
                                >{{ \App\Enums\MemberType::value($type->value) }}</flux:select.option>
                            @endforeach

                        </flux:select>
                    </div>

                @else
                    <input type="hidden"
                           wire:model="form.applied_at"
                    >
                @endcan

            </flux:card>


        </div>


        @if($isExternalMemberApplication)
            @section('head')
                <x-turnstile.scripts/>
            @endsection

            <x-turnstile wire:model="turnstile"/>

            <flux:button type="submit"
                         variant="primary"
                         icon="printer"
                         x-show="$wire.nomail"
            >{{ __('members.apply.printAndSubmit') }}</flux:button>

            <flux:button type="submit"
                         variant="primary"
                         icon="paper-airplane"
                         x-show="! $wire.nomail"
            >{{ __('members.apply.checkAndSubmit') }}</flux:button>

        @else

            <flux:button variant="primary"
                         type="submit"
            >{{ __('members.backend.create.btn.submit') }}
            </flux:button>

        @endif

        @if(! app()->isProduction())
            <flux:button variant="ghost"
                         wire:click="addDummyData"
            >Dummy
            </flux:button>
        @endif
    </form>
</div>





