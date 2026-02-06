<section>
    <div class="gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-hidden focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-6 lg:grid-cols-12 lg:gap-8 lg:px-8 items-center">
            <h2 class="max-w-xl font-semibold tracking-tight text-balance lg:col-span-7 text-lg sm:text-xl lg:2-xl" >{{ __('mails.mailing_list.header',['name'=>setting('organization.name')]) }}</h2>
            <form class="w-full max-w-md lg:col-span-5 lg:pt-2 space-y-6 lg:mt-10"
                  wire:submit="addMailingListEntry"
            >
                <div class="grid lg:grid-cols-2 gap-4">
                    <flux:fieldset>
                        <flux:field>
                            <flux:label class="sr-only">{{ __('mails.mailing_list.label.email') }}</flux:label>
                            <flux:input type="email"
                                        placeholder="E-Mail Adresse"
                                        wire:model="form.email"
                                        autocomplete="email"
                            />
                            <flux:error name="form.email"/>
                        </flux:field>
                    </flux:fieldset>

                    <flux:button type="submit"
                                 variant="primary"
                    >{{ __('mails.mailing_list.btn_subscribe.label') }}</flux:button>
                </div>
                <flux:field class="flex gap-3">

                    <flux:checkbox wire:model="form.terms_accepted"/>

                    <div>
                        <flux:label class="text-xs">{{ __('mails.mailing_list.text.privacy') }}</flux:label>
                        <flux:error name="form.terms_accepted"/>
                    </div>

                    <div class="hidden lg:flex">
                        <flux:modal.trigger name="show-options"
                                            x-show="!$wire.form.terms_accepted"
                        >
                            <flux:button size="xs"
                                         variant="ghost"
                            >Optionen
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:modal.trigger name="show-options"
                                            x-show="$wire.form.terms_accepted"
                        >
                            <flux:button size="xs"
                                         variant="primary"
                            >Optionen
                            </flux:button>
                        </flux:modal.trigger>
                    </div>
                </flux:field>
                <div class="lg:hidden">
                    <flux:modal.trigger name="show-options"
                                        x-show="!$wire.form.terms_accepted"
                    >
                        <flux:button size="xs"
                                     variant="ghost"
                        >Optionen
                        </flux:button>
                    </flux:modal.trigger>

                    <flux:modal.trigger name="show-options"
                                        x-show="$wire.form.terms_accepted"
                    >
                        <flux:button size="xs"
                                     variant="primary"
                        >Optionen
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </form>
        </div>
        @if(!app()->isProduction())
            <x-debug/>
        @endif
    </div>

    <flux:modal name="show-options"
                variant="flyout"
                class="w-full md:w-60"
    >
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('mails.mailing_list.options_header') }}</flux:heading>
            <flux:text>{{ __('mails.mailing_list.text.privacy_full') }}</flux:text>
            <flux:checkbox.group label="{{ __('mails.mailing_list.options_group_header') }}">
                <flux:checkbox.all label="{{ __('mails.mailing_list.options.all_label') }}"
                                   description="{{ __('mails.mailing_list.options.all_description') }}"
                />
                <flux:checkbox wire:model="form.update_on_events"
                               label="{{ __('mails.mailing_list.options.events_label') }}"
                               description="{{ __('mails.mailing_list.options.events_description') }}"
                               checked
                />
                <flux:checkbox wire:model="form.update_on_articles"
                               label="{{ __('mails.mailing_list.options.posts_label') }}"
                               description="{{ __('mails.mailing_list.options.posts_description') }}"
                               checked
                />
                <flux:checkbox wire:model="form.update_on_notifications"
                               label="{{ __('mails.mailing_list.options.update_notifications_label') }}"
                               description="{{ __('mails.mailing_list.options.update_notifications_description') }}"
                               checked
                />
            </flux:checkbox.group>
            <flux:radio.group wire:model="form.locale"
                              label="Bevorzugte Sprache"
                              variant="segmented"
                              size="sm"
            >
                @foreach(\App\Enums\Locale::cases() as $locale)
                    <flux:radio value="{{ $locale->value }}"
                                label="{{ $locale->value }}"
                    />
                @endforeach
            </flux:radio.group>
        </div>
    </flux:modal>
</section>

