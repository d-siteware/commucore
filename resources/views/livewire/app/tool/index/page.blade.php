<div>
    <flux:heading size="xl">{{ __('nav.tools') }}</flux:heading>
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-3 my-6">
        <flux:card>
                <flux:heading size="lg"
                >{{ __('mails.members.heading') }}</flux:heading>
                <flux:text>{{ __('mails.members.content') }}</flux:text>
            <form wire:submit="sendMembersMail"
                  class="mt-4 lg:mt-6"
            >

            <flux:accordion transition>
                <flux:accordion.item>
                    <flux:accordion.heading>MailingList einbinden</flux:accordion.heading>
                    <flux:accordion.content>
                        @if($this->mailingList->count() >0)
                            <flux:separator text="{{ __('mails.member.separator.options') }}"
                                            class="my-6"
                            />
                            <div class="grid gap-3 lg:grid-cols-2">
                                <flux:switch wire:model.live="include_mailing_list"
                                             label="Externe Mailingliste einschließen"
                                />
                                <div x-show="$wire.include_mailing_list"
                                     x-transition
                                >
                                    <flux:radio.group label="Grund des Schreibens"
                                                      variant="cards"
                                                      class="flex-col"
                                                      wire:model="target_type"
                                    >
                                        <flux:radio icon="calendar-days"
                                                    value="standard"
                                                    label="Neue Veranstaltung"
                                        />
                                        <flux:radio icon="document-text"
                                                    value="fast"
                                                    label="Neuer Artikel"
                                        />
                                        <flux:radio icon="cloud-arrow-up"
                                                    value="next-day"
                                                    label="Änderung Artikel/Veranstaltung"
                                        />
                                    </flux:radio.group>
                                </div>

                            </div>
                        @endif
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item expanded>
                    <flux:accordion.heading>{{ __('mails.member.separator.text') }}</flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:tab.group>
                            <flux:tabs>
                                @foreach(\App\Enums\Locale::cases() as $locale)
                                    <flux:tab name="setText{{ $locale }}">{{ $locale }}</flux:tab>
                                @endforeach
                            </flux:tabs>
                            @foreach(\App\Enums\Locale::cases() as $locale)
                            <flux:tab.panel name="setText{{ $locale }}">
                                <section class="space-y-3">
                                    <flux:input label="{{ __('mails.members.subject.'.$locale->value) }}"
                                                wire:model="subject.{{ $locale }}"
                                    />
                                    <flux:textarea rows="auto"
                                                   label="{{ __('mails.members.message.'.$locale->value) }}"
                                                   wire:model="message.{{ $locale }}"
                                    />

                                </section>
                            </flux:tab.panel>
                            @endforeach
                        </flux:tab.group>

                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:accordion.heading>{{ __('mails.member.separator.links') }}</flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:field>
                            <flux:input.group>
                                <flux:input wire:model="url"
                                            placeholder="{{ setting('organization.web') }}"
                                />
                            </flux:input.group>

                            <flux:error name="website"/>
                        </flux:field>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            <section class="space-y-6">
                                <flux:input label="Magyar link szöveg"
                                            wire:model="urlLabel.hu"
                                />

                            </section>
                            <section class="space-y-6">
                                <flux:input label="Deutscher Link Label"
                                            wire:model="urlLabel.de"
                                />

                            </section>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>

                    <flux:accordion.heading>{{ __('mails.member.separator.attachments') }}</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            <flux:field class="flex-col flex">
                                <flux:label>Csatolt fájl</flux:label>
                                <input type="file"
                                       wire:model="attachments.hu"
                                       accept=".pdf,.jpg,.jpeg,.png,.tif"
                                       class="border border-zinc-300 p-1.5 rounded shadow-sm"
                                >
                                <flux:error name="attachments.hu"/>
                            </flux:field>

                            <flux:field class="flex-col flex">
                                <flux:label>Angehängte Datei</flux:label>
                                <input type="file"
                                       wire:model="attachments.de"
                                       accept=".pdf,.jpg,.jpeg,.png,.tif"
                                       class="border border-zinc-300 p-1.5 rounded shadow-sm"
                                >
                                <flux:error name="attachments.de"/>
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>

                    <flux:accordion.heading>{{ __('Optionen zum E-Mail Versand') }}</flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:checkbox.group >
                            <flux:checkbox wire:model="setLink"
                                           label="Link erstellen"
                                           description="Wenn aktiviert wird ein Link am Ende der E-Mail eingefügt der zu der jeweiligen Seite führt."
                            />
                            <flux:checkbox wire:model="setPersonalGreeting"
                                           label="Persönliche Anrede"
                                           description="Wenn aktiviert, wird der Empfänger der E-Mail mit seinem Namen angesprochen. Deaktiviert, wird keine Begrüßung ersellt!"
                            />
                            <flux:checkbox wire:model="setAttachment"
                                           label="E-Mail Anhänge"
                                           description="Sollen Dateien der E-Mail angehängt werden?"
                            />
                        </flux:checkbox.group>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>


            </form>

            <aside class="my-6">
{{--          @if(!app()->isProduction())--}}
{{--              <flux:button wire:click="addDummyData">dummy</flux:button>--}}
{{--          @endif--}}

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

          <flux:button variant="primary"
                       wire:click="sendTestMailToSelf"
          >{{ __('mails.members.btn.test_mail') }}
          </flux:button>

          <flux:modal.trigger name="delete-profile">
              <flux:button variant="danger">{{ __('mails.members.btn.submit') }}</flux:button>
          </flux:modal.trigger>

          <flux:modal name="delete-profile"
                      class="min-w-[22rem]"
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

                      <flux:button type="submit"
                                   variant="danger"
                                   icon-trailing="envelope"
                      >{{ __('mails.members.btn.final') }}
                      </flux:button>
                  </div>
              </div>
          </flux:modal>
      </aside>


        </flux:card>


        <flux:card>

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
                            neue Anmeldungen in {{ \Carbon\Carbon::today()->locale('de')->isoFormat('MMMM') }}</flux:heading>

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
                                    <p class="text-sm text-teal-700"> Eine Anmeldung in {{ \Carbon\Carbon::today()->locale('de')->isoFormat('MMMM') }}</p>
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
                                    <p class="text-sm text-zinc-700"> Keine neuen Anmeldungen in {{ \Carbon\Carbon::today()->locale('de')->isoFormat('MMMM') }}</p>
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
                            neue Anmeldungen in {{ \Carbon\Carbon::today()->year }}</flux:heading>
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
                                    <p class="text-sm text-teal-700"> Eine neue Anmeldung in {{ \Carbon\Carbon::today()->year }}</p>
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
                                    <p class="text-sm text-zinc-700"> Keine neuen Anmeldungen in {{ \Carbon\Carbon::today()->year }}</p>
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
                        >E-Mail
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
                                    <flux:badge>{{ $entry->locale->value }}</flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

            @else
                <flux:text>Keine verifizierten Einträge in der Mailingliste vorhanden</flux:text>
            @endif

        </flux:card>

    </section>
</div>
