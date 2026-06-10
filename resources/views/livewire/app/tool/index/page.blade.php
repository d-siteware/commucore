<div>
    <flux:heading size="xl">{{ __('mails.page_title') }}</flux:heading>

    <flux:tab.group class="mt-6">
        <flux:tabs>
            <flux:tab wire:click="setSelectedTab('create-mail-tab')" name="create-mail-tab" icon="pencil">{{ __('mails.tab.create') }}</flux:tab>
            <flux:tab wire:click="setSelectedTab('history-mail-tab')" name="history-mail-tab" icon="archive-box">{{ __('mails.tab.history') }}</flux:tab>
            <flux:tab wire:click="setSelectedTab('mailing-list-tab')" name="mailing-list-tab" icon="users">{{ __('mails.tab.external_list') }}</flux:tab>
        </flux:tabs>
        <flux:tab.panel name="create-mail-tab">

                <flux:heading size="lg"
                >{{ __('mails.members.heading') }}</flux:heading>
                <flux:text>{{ __('mails.members.content') }}</flux:text>
                <form class="mt-4 lg:mt-6"
                >

                    <flux:accordion transition>
                        <flux:accordion.item expanded>
                            <flux:accordion.heading>{{ __('mails.member.separator.text') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                @isMultiLang
                                <flux:tab.group>
                                    <flux:tabs>
                                        @foreach($activeLocales as $locale)
                                            <flux:tab name="setText{{ $locale }}">{{ $locale }}</flux:tab>
                                        @endforeach
                                    </flux:tabs>
                                    @foreach($activeLocales  as $locale)
                                        <flux:tab.panel name="setText{{ $locale }}">
                                           <x-mails.mails-locale-section :locale="$locale" />
                                        </flux:tab.panel>
                                    @endforeach
                                </flux:tab.group>
                                @else
                                    <x-mails.mails-locale-section :locale="$activeLocales[0]" :multiLang="false"/>
                                @endIsMultiLang
                            </flux:accordion.content>
                        </flux:accordion.item>
                        <flux:accordion.item>

                            <flux:accordion.heading>{{ __('mails.tool.options_heading') }}</flux:accordion.heading>
                            <flux:accordion.content>
                                <flux:field x-show="$wire.setLink">
                                    <flux:label>{{ __('mails.members.url') }}</flux:label>
                                    <flux:input wire:model="url" placeholder="{{ setting('organization.web') }}" />
                                    <flux:error name="url" />
                                </flux:field>
                                <aside class="my-6 space-y-6">
                                    <div x-show="$wire.include_mailing_list"
                                         x-transition
                                    >
                                        <flux:radio.group label="{{ __('mails.tool.reason') }}"
                                                          variant="cards"
                                                          wire:model="target_type"
                                        >
                                            <flux:radio icon="calendar-days"
                                                        value="standard"
                                                        :label="__('mails.tool.new_event')"
                                            />
                                            <flux:radio icon="document-text"
                                                        value="fast"
                                                        :label="__('mails.tool.new_article')"
                                            />
                                            <flux:radio icon="cloud-arrow-up"
                                                        value="next-day"
                                                        :label="__('mails.tool.change')"
                                            />
                                        </flux:radio.group>
                                    </div>
                                </aside>
                                <flux:checkbox.group>
                                    @if($this->mailingList->count() >0)
                                        <flux:checkbox wire:model.live="include_mailing_list"
                                                       :label="__('mails.tool.include_external_list')"
                                                       :description="__('mails.tool.include_external_list_desc')"
                                        />

                                    @endif
                                    <flux:checkbox wire:model="setLink"
                                                   :label="__('mails.tool.create_link')"
                                                   :description="__('mails.tool.create_link_desc')"
                                    />
                                    <flux:checkbox wire:model="setPersonalGreeting"
                                                   :label="__('mails.tool.personal_greeting')"
                                                   :description="__('mails.tool.personal_greeting_desc')"
                                    />
                                    <flux:checkbox wire:model="setAttachment"
                                                   :label="__('mails.tool.attachments')"
                                                   :description="__('mails.tool.attachments_desc')"
                                    />
                                </flux:checkbox.group>
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>


                </form>

                <aside class="my-6">
                    @if(!app()->isProduction())
                        <flux:button wire:click="addDummyData">dummy</flux:button>
                    @endif

                    <flux:button href="{{ route('test-mail-preview', [
            'name' => 'Daniel',
            'subject' => $this->subject['de'] ?? 'Testbetreff',
            'message' => $this->message['de'] ?? 'Kein Inhalt???',
            'locale' => 'de',
            'url' => $this->url ?? 'www-popo',
            'urlLabel' => $this->urlLabel['de'] ?? 'nix label',
        ]) }}"
                                 target="_blank"
                    >{{ __('mails.members.btn.preview') }}</flux:button>

                    <flux:button variant="outline"
                                 wire:click="sendTestMailToSelf"
                    >{{ __('mails.members.btn.test_mail') }}
                    </flux:button>

                    <flux:modal.trigger name="confirm-sen-mass-mails">
                        <flux:button variant="primary">{{ __('mails.members.btn.submit') }}</flux:button>
                    </flux:modal.trigger>

                    <flux:modal name="confirm-sen-mass-mails"
                                class="min-w-88"
                    >
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">{{ __('mails.members.confirm.header') }}</flux:heading>

                                <flux:subheading>
                                    <p>{{ __('mails.members.confirm.warning') }}</p>
                                    <p>{{ __('mails.members.confirm.info') }}</p>
                                </flux:subheading>
                            </div>

                            <div class="flex gap-2">
                                <flux:spacer/>

                                <flux:modal.close>
                                    <flux:button variant="ghost">{{ __('mails.members.btn.cancel') }}</flux:button>
                                </flux:modal.close>

                                <flux:button wire:click="sendMembersMail"
                                             variant="danger"
                                             icon-trailing="envelope"
                                >{{ __('mails.members.btn.final') }}
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>
                </aside>




        </flux:tab.panel>
        <flux:tab.panel name="history-mail-tab">
            <livewire:app.global.mailing-history-widget />
        </flux:tab.panel>
        <flux:tab.panel name="mailing-list-tab">

                <section class="grid gap-6 lg:grid-cols-2">
                    <figure>
                        @if(count($monthlySubscriptions) > 1)
                            <div wire:loading
                                 class="text-center text-gray-500"
                            >
                                Loading chart...
                            </div>
                            <flux:heading>
                                <flux:badge color="lime"
                                            size="sm"
                                >{{ count($monthlySubscriptions) }}</flux:badge>
                                {{ __('mails.mailing_list_subscriptions.new_in_month', ['month' => \Carbon\Carbon::today()->locale('de')->isoFormat('MMMM')]) }}</flux:heading>

                            <flux:chart wire:model="monthlySubscriptions"
                                        class="aspect-3/1"
                                        wire:loading.remove
                            >
                                <flux:chart.svg>
                                    <flux:chart.line field="visitors"
                                                     class="text-pink-500 dark:text-pink-400"
                                    />

                                    <flux:chart.axis axis="x"
                                                     field="date"
                                    >
                                        <flux:chart.axis.line/>
                                        <flux:chart.axis.tick/>
                                    </flux:chart.axis>

                                    <flux:chart.axis axis="y">
                                        <flux:chart.axis.grid/>
                                        <flux:chart.axis.tick/>
                                    </flux:chart.axis>

                                    <flux:chart.cursor/>
                                </flux:chart.svg>

                                <flux:chart.tooltip>
                                    <flux:chart.tooltip.heading field="date"
                                                                :format="['year' => 'numeric', 'month' => 'numeric', 'day' => 'numeric']"
                                    />
                                    <flux:chart.tooltip.value field="visitors"
                                                              label="Visitors"
                                    />
                                </flux:chart.tooltip>
                            </flux:chart>

                        @elseif(count($monthlySubscriptions)=== 1)
                            <div class="rounded-md bg-teal-50 p-4">
                                <div class="flex">
                                    <div class="shrink-0">
                                        <svg class="size-5 text-teal-400"
                                             viewBox="0 0 20 20"
                                             fill="currentColor"
                                             aria-hidden="true"
                                             data-slot="icon"
                                        >
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z"
                                                  clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1 md:flex md:justify-between">
                                        <p class="text-sm text-teal-700">{{ __('mails.mailing_list_subscriptions.one_in_month', ['month' => \Carbon\Carbon::today()->locale('de')->isoFormat('MMMM')]) }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-md bg-zinc-50 p-4">
                                <div class="flex">
                                    <div class="shrink-0">
                                        <svg class="size-5 text-zinc-400"
                                             viewBox="0 0 20 20"
                                             fill="currentColor"
                                             aria-hidden="true"
                                             data-slot="icon"
                                        >
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z"
                                                  clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1 md:flex md:justify-between">
                                        <p class="text-sm text-zinc-700">{{ __('mails.mailing_list_subscriptions.none_in_month', ['month' => \Carbon\Carbon::today()->locale('de')->isoFormat('MMMM')]) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </figure>
                    <figure>
                        @if($totalSubscriptionsThisYear>1)
                            <div wire:loading
                                 class="text-center text-gray-500"
                            >
                                Loading chart...
                            </div>
                            <flux:heading>
                                <flux:badge color="lime"
                                            size="sm"
                                >{{ $totalSubscriptionsThisYear }}</flux:badge>
                                {{ __('mails.mailing_list_subscriptions.new_in_year', ['year' => \Carbon\Carbon::today()->year]) }}</flux:heading>
                            <flux:chart wire:model="yearlySubscriptions"
                                        class="aspect-3/1"
                                        wire:loading.remove
                            >
                                <flux:chart.svg>
                                    <flux:chart.line field="visitors"
                                                     class="text-pink-500 dark:text-pink-400"
                                    />

                                    <flux:chart.axis axis="x"
                                                     field="month"
                                    >
                                        <flux:chart.axis.line/>
                                        <flux:chart.axis.tick/>
                                    </flux:chart.axis>

                                    <flux:chart.axis axis="y">
                                        <flux:chart.axis.grid/>
                                        <flux:chart.axis.tick/>
                                    </flux:chart.axis>

                                    <flux:chart.cursor/>
                                </flux:chart.svg>

                                <flux:chart.tooltip>
                                    <flux:chart.tooltip.heading field="month"
                                                                :format="['year' => 'numeric', 'month' => 'numeric', 'day' => 'numeric']"
                                    />
                                    <flux:chart.tooltip.value field="visitors"
                                                              label="Visitors"
                                    />
                                </flux:chart.tooltip>
                            </flux:chart>
                        @elseif($totalSubscriptionsThisYear === 1)
                            <div class="rounded-md bg-teal-50 p-4">
                                <div class="flex">
                                    <div class="shrink-0">
                                        <svg class="size-5 text-teal-400"
                                             viewBox="0 0 20 20"
                                             fill="currentColor"
                                             aria-hidden="true"
                                             data-slot="icon"
                                        >
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z"
                                                  clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1 md:flex md:justify-between">
                                        <p class="text-sm text-teal-700">{{ __('mails.mailing_list_subscriptions.one_in_year', ['year' => \Carbon\Carbon::today()->year]) }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-md bg-zinc-50 p-4">
                                <div class="flex">
                                    <div class="shrink-0">
                                        <svg class="size-5 text-zinc-400"
                                             viewBox="0 0 20 20"
                                             fill="currentColor"
                                             aria-hidden="true"
                                             data-slot="icon"
                                        >
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z"
                                                  clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1 md:flex md:justify-between">
                                        <p class="text-sm text-zinc-700">{{ __('mails.mailing_list_subscriptions.none_in_year', ['year' => \Carbon\Carbon::today()->year]) }}</p>
                                    </div>
                                </div>
                            </div>

                        @endif
                    </figure>

                </section>


                <flux:heading size="lg"
                              class="my-10"
                >{{ __('mails.mailing_list.verified_emails') }}</flux:heading>
                @if($this->mailingList->count() >0)
                    <flux:table :paginate="$this->mailingList">
                        <flux:table.columns>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'mail'"
                                               :direction="$sortDirection"
                                               wire:click="sort('email')"
                            >{{ __('mails.mailing_list.label.email') }}
                            </flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'events'"
                                               :direction="$sortDirection"
                                               wire:click="sort('update_on_events')"
                            >
                                <flux:icon.calendar-days class="size-4"/>
                            </flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'posts'"
                                               :direction="$sortDirection"
                                               wire:click="sort('update_on_articles')"
                            >
                                <flux:icon.document-text class="size-4"/>
                            </flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'updates'"
                                               :direction="$sortDirection"
                                               wire:click="sort('update_on_notifications')"
                            >
                                <flux:icon.cloud-arrow-up class="size-4"/>
                            </flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'locale'"
                                               :direction="$sortDirection"
                                               wire:click="sort('locale')"
                            >
                                <flux:icon.language class="size-4"/>
                            </flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($this->mailingList as $entry)
                                <flux:table.row :key="$entry->id">
                                    <flux:table.cell class="flex items-center gap-3">
                                        <span class="text-wrap hyphens-auto">{{ $entry->email }}</span>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if($entry->update_on_events)
                                            <flux:icon.check-circle color="green"
                                                                    class="size-4"
                                            />
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if($entry->update_on_articles)
                                            <flux:icon.check-circle color="green"
                                                                    class="size-4"
                                            />
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if($entry->update_on_notifications)
                                            <flux:icon.check-circle color="green"
                                                                    class="size-4"
                                            />
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge>{{ $entry->locale }}</flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>

                @else
                    <flux:text>{{ __('mails.empty_mailing_list') }}</flux:text>
                @endif

        </flux:tab.panel>
    </flux:tab.group>

</div>
