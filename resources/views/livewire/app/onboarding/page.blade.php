<div class="max-w-4xl mx-auto py-10 px-4">
    {{-- Step Navigation --}}
    <nav aria-label="Progress"
         class="mb-10"
    >
        <ol role="list"
            class="divide-y divide-gray-300 dark:divide-white/15 rounded-xl border border-gray-300 dark:border-white/15 md:flex md:divide-y-0 overflow-hidden"
        >

            <li class="relative md:flex md:flex-1">
                @if($step > 1)
                    <x-steps.completed :item="1"
                                       step="01"
                                       :label="__('onboarding.step.01')"
                    />
                @elseif($step === 1)
                    <x-steps.current :item="1"
                                     step="01"
                                     :label="__('onboarding.step.01')"
                    />
                @else
                    <x-steps.upcomming :item="1"
                                       step="01"
                                       :label="__('onboarding.step.01')"
                    />
                @endif
            </li>

            <li class="relative md:flex md:flex-1">
                @if($step > 2)
                    <x-steps.completed :item="2"
                                       step="02"
                                       :label="__('onboarding.step.02')"
                    />
                @elseif($step === 2)
                    <x-steps.current :item="2"
                                     step="02"
                                     :label="__('onboarding.step.02')"
                    />
                @else
                    <x-steps.upcomming :item="2"
                                       step="02"
                                       :label="__('onboarding.step.02')"
                    />
                @endif
            </li>

            <li class="relative md:flex md:flex-1">
                @if($step > 3)
                    <x-steps.completed :item="3"
                                       step="03"
                                       :label="__('onboarding.step.03')"
                    />
                @elseif($step === 3)
                    <x-steps.current :item="3"
                                     step="03"
                                     :label="__('onboarding.step.03')"
                    />
                @else
                    <x-steps.upcomming :item="3"
                                       step="03"
                                       :label="__('onboarding.step.03')"
                    />
                @endif
            </li>

            <li class="relative md:flex md:flex-1">
                @if($step === 4)
                    <x-steps.current :item="4"
                                     step="04"
                                     :label="__('onboarding.step.04')"
                                     :last="true"
                    />
                @else
                    <x-steps.upcomming :item="4"
                                       step="04"
                                       :label="__('onboarding.step.04')"
                                       :last="true"
                    />
                @endif
            </li>

        </ol>
    </nav>

    {{-- Step 1: Organisation & Rechtliches --}}
    @if($step === 1)
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg">{{ __('onboarding.org.heading') }}</flux:heading>
                <flux:subheading>{{ __('onboarding.org.subheading') }}</flux:subheading>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <flux:input wire:model="org_name"
                                    :label="__('onboarding.org.org_name')"
                                    required
                        />
                    </div>
                    <flux:input wire:model="org_email"
                                :label="__('onboarding.org.email')"
                                type="email"
                    />
                    <flux:input wire:model="org_web"
                                :label="__('onboarding.org.website')"
                                type="url"
                                placeholder="https://"
                    />
                    <flux:input wire:model="org_address"
                                :label="__('onboarding.org.address')"
                    />
                    <div class="grid grid-cols-3 gap-2">
                        <flux:input wire:model="org_zip"
                                    :label="__('onboarding.org.zip')"
                        />
                        <div class="col-span-2">
                            <flux:input wire:model="org_city"
                                        :label="__('onboarding.org.city')"
                            />
                        </div>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">{{ __('onboarding.org.legal_heading') }}</flux:heading>
                <flux:subheading>{{ __('onboarding.org.legal_subheading') }}</flux:subheading>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="org_register_id"
                                :label="__('onboarding.org.register_id')"
                                placeholder="VR 12345"
                    />
                    <flux:input wire:model="org_registered_date"
                                :label="__('onboarding.org.registered_date')"
                                type="date"
                    />
                    <flux:input wire:model="org_court"
                                :label="__('onboarding.org.court')"
                    />
                    <flux:input wire:model="org_tax_id"
                                :label="__('onboarding.org.tax_id')"
                    />
                    <flux:input wire:model="org_vat_id"
                                :label="__('onboarding.org.vat_id')"
                                placeholder="DE123456789"
                    />
                </div>
            </flux:card>

            <div class="flex justify-end">
                <flux:button variant="primary"
                             wire:click="nextStep"
                >{{ __('onboarding.btn.next') }}
                </flux:button>
            </div>
        </div>
    @endif

    {{-- Step 2: Einstellungen --}}
    @if($step === 2)
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg">{{ __('onboarding.settings.fy_heading') }}</flux:heading>
                <flux:subheading>{{ __('onboarding.settings.fy_subheading') }}</flux:subheading>
                <div class="mt-6">
                    <flux:input wire:model="fiscal_year"
                                :label="__('onboarding.settings.fy_label')"
                                type="number"
                                min="2000"
                                max="2100"
                    />
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">{{ __('onboarding.settings.locales_heading') }}</flux:heading>
                <flux:subheading>{{ __('onboarding.settings.locales_subheading') }}</flux:subheading>
                <flux:separator class="my-4" :text="__('onboarding.settings.locales_available')"/>
                <flux:checkbox.group wire:model.live="active_locales">
                    @foreach(\App\Models\Locale::all() as $locale)
                        <flux:checkbox
                                       value="{{ $locale->name }}"
                                       label="{{ $locale->label() }}"
                                       description="{{ $locale->description() }}"
                        />
                    @endforeach
                </flux:checkbox.group>
                <flux:error name="active_locales" class="mt-2" />
            </flux:card>

            <div class="flex justify-between">
                <flux:button wire:click="prevStep">{{ __('onboarding.btn.back') }}</flux:button>
                <flux:button variant="primary"
                             wire:click="nextStep"
                >{{ __('onboarding.btn.next') }}
                </flux:button>
            </div>
        </div>
    @endif

    {{-- Step 3: Profil & Team einladen --}}
    @if($step === 3)
        <div class="space-y-6">

            <flux:card>
                <flux:heading size="lg">{{ __('onboarding.team.profile_heading') }}</flux:heading>
                <flux:subheading>{{ __('onboarding.team.profile_subheading') }}</flux:subheading>
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <flux:input wire:model="user_name"
                                :label="__('onboarding.team.surname')"
                                required
                    />
                    <flux:input wire:model="user_first_name"
                                :label="__('onboarding.team.firstname')"
                    />
                    <flux:input wire:model="user_username"
                                :label="__('onboarding.team.username')"
                    />
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">{{ __('onboarding.team.invite_heading') }}</flux:heading>
                <flux:subheading>
                    {{ __('onboarding.team.invite_subheading') }}
                </flux:subheading>

                <div class="mt-6 space-y-4">
                    @foreach($invites as $index => $invite)
                        <div class="flex gap-2 items-start">
                            <div class="grid grid-cols-3 gap-2 flex-1">
                                <flux:input
                                        wire:model="invites.{{ $index }}.name"
                                        :placeholder="__('onboarding.team.surname')"
                                        required
                                />
                                <flux:input
                                        wire:model="invites.{{ $index }}.first_name"
                                        :placeholder="__('onboarding.team.firstname')"
                                />
                                <flux:input
                                        wire:model="invites.{{ $index }}.email"
                                        type="email"
                                        placeholder="email@beispiel.de"
                                />
                            </div>
                            @if(count($invites) > 1)
                                <flux:button variant="ghost"
                                             wire:click="removeInvite({{ $index }})"
                                             icon="trash"
                                />
                            @endif
                        </div>
                    @endforeach

                    <flux:button variant="ghost"
                                 wire:click="addInvite"
                                 icon="plus"
                    >
                        {{ __('onboarding.team.add_more_btn') }}
                    </flux:button>
                </div>
            </flux:card>

            @if(! $smtpConfigured)
                <flux:callout variant="warning" class="my-6">
                    <flux:callout.heading icon="envelope">{{ __('onboarding.team.smtp_warning_heading') }}</flux:callout.heading>

                    <flux:callout.text>{{ __('onboarding.team.smtp_warning_text') }}</flux:callout.text>
                </flux:callout>
            @endif

            <div class="flex justify-between">
                <flux:button wire:click="prevStep">{{ __('onboarding.btn.back') }}</flux:button>
                <flux:button variant="primary"
                             wire:click="nextStep"
                >{{ __('onboarding.btn.next') }}
                </flux:button>
            </div>
        </div>
    @endif

    {{-- Step 4: Fertig --}}
    @if($step === 4)
        <div class="space-y-6">
            <flux:card>
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-teal-100 dark:bg-teal-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <flux:icon.check class="w-8 h-8 text-teal-600 dark:text-teal-400"/>
                    </div>
                    <flux:heading size="xl">{{ __('onboarding.finish.heading') }}</flux:heading>
                    <flux:subheading class="mt-2">
                        {{ __('onboarding.finish.subheading') }}
                    </flux:subheading>

                    <div class="mt-8 text-left max-w-sm mx-auto space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <div class="flex justify-between items-center">
                            <strong>{{ $org_name }}</strong>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-teal-800">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            </div>
                        <div class="flex justify-between items-center">
                            <span>{{ __('onboarding.finish.fiscal_year', ['year' => $fiscal_year]) }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-teal-800">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                       </div>
                        <div class="flex justify-between items-center">
                            @if (count($active_locales) > 1)

                                <div>{{ __('onboarding.finish.locales_plural') }}</div>
                                <aside>
                                @for($i = 0; $i < count($active_locales); $i++)
                                        <flux:badge color="teal" size="sm">{{ $active_locales[$i] }}</flux:badge>
                                @endfor
                                </aside>
                            @else

                                <span>{{ __('onboarding.finish.locales_singular') }}</span>
                                <flux:badge color="teal" size="sm">{{ $active_locales[0] }}</flux:badge>

                            @endif
                        </div>

                        @php $validInvites = array_filter($invites, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)) @endphp
                        @if(count($validInvites) > 0)
                            <p class="flex justify-between items-center">


                                {{ __('onboarding.finish.invites_sent', ['count' => count($validInvites)]) }}</p>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        @endif
                    </div>
                </div>
            </flux:card>

            <div class="flex justify-between">
                <flux:button wire:click="prevStep">{{ __('onboarding.btn.back') }}</flux:button>
                <flux:button variant="primary"
                             wire:click="finish"
                >
                    {{ __('onboarding.finish.dashboard_btn') }}
                </flux:button>
            </div>
        </div>
    @endif

</div>