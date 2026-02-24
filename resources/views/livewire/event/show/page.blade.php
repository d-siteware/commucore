<div>
    <flux:heading class="lg:mb-9 lg:hidden"
                  size="lg"
    >{{ __('event.show.page.title') }}</flux:heading>
    <flux:heading class="lg:mb-9 lg:flex hidden">{{ __('event.show.page.title') }}</flux:heading>
    <flux:subheading size="xl">{{ $form->name ?? $form->title[app()->getLocale()] }}</flux:subheading>
    <flux:tab.group class="mt-3">
        <flux:tabs wire:model="selectedTab">
            <flux:tab name="event-show-dates"
                      icon="calendar-days"
                      wire:click="setSelectedTab('event-show-dates')"
            ><span class="hidden md:inline">{{ __('event.tabs.nav.dates') }}</span></flux:tab>
            <flux:tab name="event-show-descriptions"
                      icon="document-text"
                      wire:click="setSelectedTab('event-show-descriptions')"
            ><span class="hidden md:inline">{{ __('event.tabs.nav.texts') }}</span></flux:tab>
            <flux:tab name="event-show-payments"
                      icon="banknotes"
                      wire:click="setSelectedTab('event-show-payments')"
            ><span class="hidden md:inline">{{ __('event.tabs.nav.payments') }}</span></flux:tab>
            <flux:tab name="event-show-subscriptions"
                      icon="chat-bubble-left-ellipsis"
                      wire:click="setSelectedTab('event-show-subscriptions')"
            ><span class="hidden md:inline">{{ __('event.tabs.nav.subscriptions') }}</span></flux:tab>
            <flux:tab name="event-show-visitors"
                      icon="users"
                      wire:click="setSelectedTab('event-show-visitors')"
            ><span class="hidden md:inline">{{ __('event.tabs.nav.visitors') }}</span></flux:tab>
            <flux:tab name="event-show-planing"
                      icon="queue-list"
                      wire:click="setSelectedTab('event-show-planing')"
            ><span class="hidden md:inline">{{ __('event.tabs.nav.planing') }}</span></flux:tab>
        </flux:tabs>

        <flux:tab.panel name="event-show-dates">
            <form wire:submit="updateEventData">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3"
                >
                    <section class="space-y-6">

                        <flux:input wire:model="form.name"
                                    label="{{ __('event.form.name') }}"
                        />


                        <flux:date-picker with-today
                                          selectable-header
                                          wire:model="form.event_date"
                                          label="{{__('event.form.event_date')}}"
                        />

                        <section class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-9">
                            <flux:input type="time"
                                        wire:model="form.start_time"
                                        label="{{__('event.form.start_time')}}"
                            />

                            <flux:input type="time"
                                        wire:model="form.end_time"
                                        label="{{__('event.form.end_time')}}"
                            />
                        </section>

                        <flux:field>
                            <flux:label>{{__('event.form.venue_id')}}</flux:label>

                            <flux:select variant="listbox"
                                         searchable
                                         placeholder="{{ __('event.form.venue.select') }}"
                                         wire:model="form.venue_id"
                            >
                                @can('update',\App\Models\Event\Event::class)
                                    <flux:select.option value="new">{{ __('event.form.venue.new') }}</flux:select.option>
                                @endcan
                                @foreach($this->venues as $key => $venue)
                                    <flux:select.option value="{{ $venue->id }}"
                                                        :key
                                    >{{ $venue->name }}</flux:select.option>
                                @endforeach

                            </flux:select>

                            <div x-show="$wire.form.venue_id ==='new'"
                                 class="pt-3"
                            >
                                @can('update',\App\Models\Event\Event::class)
                                    <flux:modal.trigger name="add-new-venue">
                                        <flux:button>{{ __('venue.new.btn.label') }}</flux:button>
                                    </flux:modal.trigger>
                                @endcan
                            </div>
                        </flux:field>

                        @if($form->status === \App\Enums\EventStatus::PUBLISHED->value)
                            <div class="rounded-md bg-green-50 dark:bg-zinc-900 dark:border-lime-700 dark:border p-4">
                                <div class="flex">
                                    <div class="shrink-0">
                                        <svg class="size-5 text-green-400 dark:text-lime-600"
                                             viewBox="0 0 20 20"
                                             fill="currentColor"
                                             aria-hidden="true"
                                             data-slot="icon"
                                        >
                                            <path fill-rule="evenodd"
                                                  d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                                  clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800 dark:text-zinc-200">{{ __('event.show.tab.main.published.header') }}</h3>
                                        <div class="mt-2 text-sm text-green-700 dark:text-zinc-100">
                                            <p>{{ __('event.show.tab.main.published.status_msg') }}</p>

                                        </div>
                                        @can('update',\App\Models\Event\Event::class)
                                            <div class="mt-4">
                                                <div class="-mx-2 -my-1.5 flex-col lg:flex-row gap-3 items-center">
                                                    <flux:button size="sm"
                                                                 icon-trailing="arrow-uturn-left"
                                                                 variant="ghost"
                                                                 wire:click="resetPublication"
                                                                 wire:confirm="{{ __('event.show.tab.main.published.confirmation_msg') }}"
                                                    >{{ __('event.show.tab.main.published.btn_reset') }}</flux:button>

                                                    <flux:button size="sm"
                                                                 icon-trailing="megaphone"
                                                                 variant="filled"
                                                                 wire:click="sendPublicationNotification"
                                                    >{{ __('event.show.tab.main.published.btn_sendMails') }}</flux:button>
                                                    @if($form->notification_sent_at)
                                                        <flux:text size="sm"
                                                                   variant="ghost"
                                                                   class="text-lime-700"
                                                        >{{ __('event.show.tab.main.published.sent_at', ['time' => $form->notification_sent_at->diffForHumans()]) }}</flux:text>
                                                    @endif
                                                    <flux:button size="sm"
                                                                 icon-trailing="document-text"
                                                                 variant="filled"
                                                                 wire:click="sendPublicationLetter"
                                                    >{{ __('event.show.tab.main.published.btn_makeLetters') }}</flux:button>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-end space-x-2">
                                <flux:select wire:model="form.status"
                                             variant="listbox"
                                             placeholder="{{ __('event.form.status_placeholder') }}"
                                             label="{{__('event.form.status')}}"
                                >
                                    @foreach(\App\Enums\EventStatus::cases() as $status)
                                        <flux:select.option value="{{ $status }}">
                                            <flux:badge color="{{ \App\Enums\EventStatus::color($status->value) }}">{{ \App\Enums\EventStatus::value($status->value) }}</flux:badge>
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                                @can('update',\App\Models\Event\Event::class)
                                    <flux:button variant="primary"
                                                 icon-trailing="cloud-arrow-up"
                                                 wire:click="publishEvent"
                                    >{{ __('event.show.section.published.btn_publish_now') }}</flux:button>
                                @endcan
                            </div>

                        @endif

                    </section>

                    <section class="space-y-6">

                        <flux:field>
                            <flux:label>{{__('event.form.entry_fee')}}</flux:label>
                            <flux:input.group>
                                <flux:input wire:model="form.entry_fee"
                                            placeholder="{{ __('event.form.entry_fee') }}"
                                            x-mask:dynamic="$money($input, ',', '.')"
                                />
                                <flux:input.group.suffix>EUR</flux:input.group.suffix>
                            </flux:input.group>
                            <flux:error name="entry_fee"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{__('event.form.entry_fee_discounted')}}</flux:label>
                            <flux:input.group>
                                <flux:input wire:model="form.entry_fee_discounted"
                                            placeholder="{{ __('event.form.entry_fee_discounted') }}"
                                            x-mask:dynamic="$money($input, ',', '.')"
                                />
                                <flux:input.group.suffix>EUR</flux:input.group.suffix>
                            </flux:input.group>
                            <flux:error name="entry_fee_discounted"/>
                        </flux:field>


                        <flux:input wire:model="form.payment_link"
                                    label="{{ __('event.form.payment_link') }}"
                        />

                        <flux:separator text="{{ __('event.form.image.upload') }}"/>
                        @if($form->event->image)
                            <img src="{{ asset('storage/images/'.$form->event->image) }}"
                                 alt=""
                                 class="w-1/3 my-3 lg:my-9 rounded-md shadow-sm"
                            >
                            @can('update',\App\Models\Event\Event::class)
                                <flux:button size="sm"
                                             variant="danger"
                                             icon="trash"
                                             wire:click="deleteImage"
                                />
                            @endcan
                        @else
                            @can('update',\App\Models\Event\Event::class)
                                <livewire:app.global.image-upload/>
                            @endcan
                        @endif
                        <livewire:event.poster-generator.create :event="$event"/>

                    </section>

                </div>


                @can('update',\App\Models\Event\Event::class)
                    <flux:button type="submit"
                                 variant="primary"
                    >{{ __('event.form.btn.save') }}
                    </flux:button>
                @endcan

            </form>  <!-- END PANEL EVENT DATA -->
        </flux:tab.panel>

        <flux:tab.panel name="event-show-descriptions">
            <form wire:submit="updateEventData">
                @can('update',\App\Models\Event\Event::class)
                    <nav class="flex gap-6">
                        <flux:button size="sm"
                                     wire:click="makeWebText"
                                     variant="primary"
                                     icon-trailing="document"
                        >{{ __('event.backend.text-nav.btn-make-web-texts') }}</flux:button>
                        <flux:button type="submit"
                                     variant="primary"
                                     size="sm"
                        >{{ __('event.backend.text-nav.btn-store') }}
                        </flux:button>
                    </nav>
                @endcan

                <section id="event-description-tabs">
                    <flux:tab.group>
                        <flux:tabs>
                            @foreach($form->locales as $locale)
                            <flux:tab name="text-tab-{{ $locale }}">{{ $locale }}</flux:tab>
                            @endforeach
                        </flux:tabs>
                        @foreach($form->locales as $locale)
                        <flux:tab.panel name="text-tab-{{ $locale }}">
                            <flux:card class="space-y-6">

                                <flux:field>
                                    <flux:label>{{ __('event.backend.texts.title_label') }}
                                        <flux:badge color="lime">{{ $locale }}</flux:badge>
                                    </flux:label>
                                    <flux:input wire:model="form.title.{{$locale}}"
                                                description="{{ __('event.backend.texts.title_description') }}"
                                    />
                                </flux:field>

                                <flux:field>
                                    <flux:label>{{ __('event.backend.texts.description_label') }}
                                        <flux:badge color="lime">{{ $locale }}</flux:badge>
                                    </flux:label>
                                    <flux:editor wire:model="form.description.{{$locale}}"/>
                                </flux:field>


                                <flux:field>
                                    <flux:label>{{ __('event.backend.texts.slug_label') }}
                                        <flux:badge color="lime">{{ $locale }}</flux:badge>
                                    </flux:label>
                                    <flux:input wire:model="form.slug.{{$locale}}"
                                                description="{{ __('event.backend.texts.slug_description') }}"
                                    />
                                </flux:field>

                                <flux:field>
                                    <flux:label>{{ __('event.backend.texts.excerpt_label') }}
                                        <flux:badge color="lime">{{ $locale }}</flux:badge>
                                    </flux:label>
                                    <flux:description>{{ __('event.backend.texts.excerpt_description') }}</flux:description>
                                    <flux:editor class="**:data-[slot=content]:min-h-[100px]"
                                                 wire:model="form.excerpt.{{$locale}}"
                                    />
                                </flux:field>
                            </flux:card>
                        </flux:tab.panel>
                        @endforeach
                    </flux:tab.group>

                </section>

            </form>
        </flux:tab.panel>

        <flux:tab.panel name="event-show-subscriptions">
            @can('create',\App\Models\Event\Event::class)

                <flux:modal.trigger name="add-subscription">
                    <flux:button variant="primary" size="sm">{{ __('event.subscriptions.btn.add_new') }}</flux:button>
                </flux:modal.trigger>
            @endcan

            <flux:table :paginate="$this->subscriptions">
                <flux:table.columns>
                    <flux:table.column>{{ __('event.subscriptions.table.name') }}</flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'date'"
                                       :direction="$sortDirection"
                                       wire:click="sort('date')"
                    >{{ __('event.subscriptions.table.date') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'status'"
                                       :direction="$sortDirection"
                                       wire:click="sort('status')"
                    >{{ __('event.subscriptions.table.email') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'amount'"
                                       :direction="$sortDirection"
                                       wire:click="sort('amount')"
                    >{{ __('event.subscriptions.table.notifications') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'amount'"
                                       :direction="$sortDirection"
                                       wire:click="sort('amount')"
                    >{{ __('event.subscriptions.table.phone') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'amount'"
                                       :direction="$sortDirection"
                                       wire:click="sort('amount')"
                    >{{ __('event.subscriptions.table.guests') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'amount'"
                                       :direction="$sortDirection"
                                       wire:click="sort('amount')"
                    >{{ __('event.subscriptions.table.confirmed_at') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->subscriptions as $subscription)
                        <flux:table.row :key="$subscription->id">
                            <flux:table.cell>
                                {{ $subscription->name }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $subscription->created_at->diffForHumans() }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $subscription->email }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($subscription->consentNotification)
                                    <flux:icon.check-circle class="size-4 text-lime-700"/>
                                @else
                                    <flux:icon.minus-circle class="size-4 text-orange-700"/>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $subscription->phone }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $subscription->amount_guests }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ optional($subscription->confirmed_at)->diffForHumans() }}
                            </flux:table.cell>


                        </flux:table.row>
                    @empty

                    @endforelse
                </flux:table.rows>
            </flux:table>

        </flux:tab.panel>

        <flux:tab.panel name="event-show-payments">
            @can('create',\App\Models\Event\Event::class)
                <aside class="flex gap-6">
                    <flux:modal.trigger name="add-new-payment">
                        <flux:button variant="primary" size="sm">{{ __('event.payments.btn.add_new') }}</flux:button>
                    </flux:modal.trigger>

                    <flux:button href="{{ route('backend.events.report', $event) }}"
                                 wire:click="generateEventReport"
                                 target="_blank"
                                 size="sm"
                    >{{ __('event.payments.btn.create_report') }}
                    </flux:button>
                </aside>
            @endcan
            <flux:table :paginate="$this->payments">
                <flux:table.columns>
                    <flux:table.column>{{ __('event.payments.table.text') }}</flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'date'"
                                       :direction="$sortDirection"
                                       wire:click="sort('date')"
                    >{{ __('event.payments.table.date') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'member_id'"
                                       :direction="$sortDirection"
                                       wire:click="sort('member')"
                    >{{ __('event.payments.table.visitor') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'amount'"
                                       :direction="$sortDirection"
                                       wire:click="sort('amount')"
                                       align="right"
                    >{{ __('event.payments.table.amount') }}
                    </flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->payments as $payment)
                        @if($payment->transaction)
                            <flux:table.row :key="$payment->transaction->id">

                                <flux:table.cell variant="strong">
                                    {{ $payment->transaction->label }}
                                </flux:table.cell>

                                <flux:table.cell>{{ $payment->transaction->date->diffForHumans() }}</flux:table.cell>
                                <flux:table.cell>{{ $payment->visitor_name }}</flux:table.cell>

                                <flux:table.cell variant="strong"
                                                 align="end"
                                >
                                <span class="text-{{ \App\Enums\TransactionType::color($payment->transaction->type) }}-600">
                                    {{ $payment->transaction->grossForHumans() }}
                                </span>
                                </flux:table.cell>

                            </flux:table.row>
                        @endif
                    @endforeach
                </flux:table.rows>
            </flux:table>

        </flux:tab.panel>

        <flux:tab.panel name="event-show-visitors">

            <nav class="mb-10 justify-between flex items-center">

                @can('create', \App\Models\Event\Event::class)
                    <flux:button wire:click="addVisitor"
                                 variant="primary"
                                 icon-trailing="user-plus"
                                 size="sm"
                    >{{ __('event.visitor.btn.add.label') }}</flux:button>

                    <flux:modal.trigger name="add-box-office-payment">
                        <flux:button variant="primary" icon-trailing="banknotes" size="sm">{{ __('event.boxoffice.btn.openmodal') }}</flux:button>
                    </flux:modal.trigger>
                @endcan


            </nav>

            <flux:input icon="magnifying-glass" placeholder="{{ __('event.visitors.search_placeholder') }}" wire:model.live.debounce="searchVisitor" clearable size="sm"/>
            <flux:table :paginate="$this->visitors">
                <flux:table.columns>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'name'"
                                       :direction="$sortDirection"
                                       wire:click="sort('name')"
                    >{{ __('event.visitor-table.header.name') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'email'"
                                       :direction="$sortDirection"
                                       wire:click="sort('email')"
                                       class="hidden md:table-cell"
                    >{{ __('event.visitor-table.header.email') }}
                    </flux:table.column>

                    <flux:table.column sortable
                                       :sorted="$sortBy === 'member_id'"
                                       :direction="$sortDirection"
                                       wire:click="sort('is_member')"
                                       class="hidden md:table-cell"
                    >{{ __('event.visitor-table.header.is_member') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'subscription_id'"
                                       :direction="$sortDirection"
                                       wire:click="sort('is_subscriber')"
                                       class="hidden md:table-cell"
                    >{{ __('event.visitor-table.header.is_subscriber') }}
                    </flux:table.column>
                    <flux:table.column>{{ __('event.visitors.table.paid') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->visitors as $visitor)
                        <flux:table.row :key="$visitor->id">
                            <flux:table.cell variant="strong">
                                {{ $visitor->name }}
                            </flux:table.cell>
                            <flux:table.cell class="hidden md:table-cell">
                                {{ $visitor->email }}
                            </flux:table.cell>
                            <flux:table.cell class="hidden md:table-cell">
                                @if($visitor->member_id)
                                    <flux:icon.user-circle size="4"/>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="hidden md:table-cell">
                                @if($visitor->event_subscription_id)
                                    <flux:icon.chat-bubble-left-ellipsis size="4"/>
                                @endif

                            </flux:table.cell>

                            <flux:table.cell>
                                @if($visitor->transaction)
                                    <flux:icon.check-circle class="text-green-700 size-6"/>
                                @endif
                            </flux:table.cell>
                            @can('create',\App\Models\Accounting\Transaction::class)
                                <flux:table.cell>
                                    <flux:dropdown>
                                        <flux:button icon="ellipsis-vertical" size="xs" variant="ghost"></flux:button>

                                        <flux:menu>
                                            <flux:menu.submenu heading="{{ __('event.visitors.menu.assign') }}">
                                                <flux:menu.item icon="user">{{ __('event.visitors.menu.assign_member') }}</flux:menu.item>
                                                <flux:menu.item icon="chat-bubble-left-ellipsis">{{ __('event.visitors.menu.assign_subscriber') }}</flux:menu.item>
                                            </flux:menu.submenu>
                                            <flux:menu.separator />

                                            <flux:menu.item variant="danger" icon="trash">{{ __('event.visitors.menu.delete') }}</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </flux:table.cell>
                            @endcan
                        </flux:table.row>

                    @empty
                        <flux:table.row>
                            <flux:table.cell variant="strong">
                                {{ __('event.visitors.empty_results_msg') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:tab.panel>

        <flux:tab.panel name="event-show-planing">
            <section class="grid grid-cols-1 gap-3 lg:gap-6 lg:grid-cols-2">
                <flux:card>

                    <header class="flex justify-between items-center mb-3 lg:mb-6">
                        <flux:heading size="lg">{{ __('event.assignments.heading') }}</flux:heading>
                        @can('create', \App\Models\Event\Event::class)

                            <flux:button size="sm"
                                         icon-trailing="plus"
                                         wire:click="startNewAssigment"
                            >{{ __('assignment.btn.open-modal.label') }}
                            </flux:button>

                        @endcan
                    </header>

                    <flux:table :paginate="$this->assignments">
                        <flux:table.columns>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'task'"
                                               :direction="$sortDirection"
                                               wire:click="sort('task')"
                            >{{ __('assignment.table.header.task') }}</flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'member_id'"
                                               :direction="$sortDirection"
                                               wire:click="sort('member')"
                                               class="hidden xl:table-cell"
                            >{{ __('assignment.table.header.lead') }}</flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'status'"
                                               :direction="$sortDirection"
                                               wire:click="sort('status')"
                            >{{ __('assignment.table.header.status') }}</flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'due_at'"
                                               :direction="$sortDirection"
                                               wire:click="sort('due_at')"
                                               class="hidden 2xl:table-cell"
                            >{{ __('assignment.table.header.due_at')}}</flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'amount'"
                                               :direction="$sortDirection"
                                               wire:click="sort('amount')"
                                               class="hidden sm:table-cell"
                                               align="right"
                            >{{ __('assignment.table.header.amount')}}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($this->assignments as $assignment)
                                <flux:table.row :key="$assignment->id">
                                    <flux:table.cell variant="strong"
                                                     class="flex items-center gap-2"
                                    >
                                        {{ $assignment->task }}
                                        @if($assignment->description)
                                            <flux:tooltip toggleable>
                                                <flux:button icon="document-text"
                                                             size="xs"
                                                             variant="ghost"
                                                />
                                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                                    {{ $assignment->description }}
                                                </flux:tooltip.content>
                                            </flux:tooltip>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell class="hidden xl:table-cell">
                                        {{ $assignment->member->fullName() }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge color="{{ $assignment->statusColor() }}"
                                                    size="sm"
                                        >{{ $assignment->statusLabel() }}</flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell class="hidden sm:table-cell">
                                        {{ $assignment->getDueString() }}
                                    </flux:table.cell>
                                    <flux:table.cell class="hidden 2xl:table-cell"
                                                     align="end"
                                    >
                                        {{ $assignment->amountString() }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:dropdown>
                                            <flux:button variant="ghost"
                                                         size="sm"
                                                         icon="ellipsis-horizontal"
                                                         inset="top bottom"
                                            ></flux:button>

                                            <flux:menu>
                                                <flux:menu.item icon="pencil-square"
                                                                wire:click="editAssignment({{ $assignment->id }})"
                                                >{{ __('assignment.btn.editAssignment') }}
                                                </flux:menu.item>
                                                <flux:menu.item icon="envelope"
                                                                wire:click="sendAssignmentNotification({{ $assignment->id }})"
                                                >{{ __('assignment.btn.sendAssignmentNotification') }}
                                                </flux:menu.item>
                                                <flux:menu.item icon="trash"
                                                                variant="danger"
                                                                wire:click="deleteAssignment({{ $assignment->id }})"
                                                >{{ __('assignment.btn.deleteAssignment') }}
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>

                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="3">{{ __('assignment.empty_list') }}</flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>


                </flux:card>
                <flux:card>
                    <header class="flex justify-between items-center mb-3 lg:mb-6">
                        <flux:heading size="lg">{{ __('event.timeline.heading') }}</flux:heading>

                        @can('create', \App\Models\Event\Event::class)
                            <flux:button size="sm"
                                         icon-trailing="plus"
                                         wire:click="startNewTimelineItem"
                            >{{ __('timeline.btn.open-modal.label') }}
                            </flux:button>
                        @endcan
                    </header>

                    <flux:table :paginate="$this->timelineItems">
                        <flux:table.columns>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'title'"
                                               :direction="$sortDirection"
                                               wire:click="sort('title')"
                            >{{ __('timeline.table.header.title') }}</flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'performer'"
                                               :direction="$sortDirection"
                                               wire:click="sort('performer')"
                                               class="hidden xl:table-cell"
                            >{{ __('timeline.table.header.performer') }}</flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'place'"
                                               :direction="$sortDirection"
                                               wire:click="sort('place')"
                                               class="hidden 2xl:table-cell"
                            >{{ __('timeline.table.header.place') }}</flux:table.column>
                            <flux:table.column>{{ __('timeline.table.header.start') }}</flux:table.column>
                            <flux:table.column>{{ __('timeline.table.header.end') }}</flux:table.column>
                            <flux:table.column sortable
                                               :sorted="$sortBy === 'member_id'"
                                               :direction="$sortDirection"
                                               wire:click="sort('member')"
                                               class="hidden 2xl:table-cell"
                            >{{ __('timeline.table.header.member')}}</flux:table.column>

                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($this->timelineItems as $timeItem)
                                <flux:table.row :key="$timeItem->id">
                                    <flux:table.cell variant="strong"
                                                     class="flex items-center gap-2"
                                    >
                                        {{ $timeItem->title }}
                                        @if($timeItem->description)
                                            <flux:tooltip toggleable>
                                                <flux:button icon="document-text"
                                                             size="xs"
                                                             variant="ghost"
                                                />
                                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                                    {{ __('timeline.description') }}: {{ $timeItem->description }}
                                                </flux:tooltip.content>
                                            </flux:tooltip>
                                        @endif   @if($timeItem->notes)
                                            <flux:tooltip toggleable>
                                                <flux:button icon="chat-bubble-oval-left-ellipsis"
                                                             size="xs"
                                                             variant="ghost"
                                                />
                                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                                    {{ __('timeline.notes') }}: {{ $timeItem->notes }}
                                                </flux:tooltip.content>
                                            </flux:tooltip>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell class="hidden xl:table-cell">
                                        {{ $timeItem->performer }}
                                    </flux:table.cell>
                                    <flux:table.cell class="hidden 2xl:table-cell">
                                        {{ $timeItem->place }}
                                    </flux:table.cell>
                                    <flux:table.cell class="hidden sm:table-cell">
                                        {{ $timeItem->start->format('H:i') }}
                                    </flux:table.cell>
                                    <flux:table.cell class="hidden sm:table-cell">
                                        {{ $timeItem->end->format('H:i') }}
                                    </flux:table.cell>
                                    <flux:table.cell class="hidden 2xl:table-cell">
                                        {{ optional($timeItem->member)->fullName() }}
                                    </flux:table.cell>

                                    @can('create', \App\Models\Event\Event::class)
                                        <flux:table.cell>
                                            <flux:dropdown>
                                                <flux:button variant="ghost"
                                                             size="sm"
                                                             icon="ellipsis-horizontal"
                                                             inset="top bottom"
                                                ></flux:button>

                                                <flux:menu>
                                                    <flux:menu.item icon="pencil-square"
                                                                    wire:click="editTimeline({{ $timeItem->id }})"
                                                    >{{ __('timeline.menu.edit') }}
                                                    </flux:menu.item>
                                                    <flux:menu.item icon="trash"
                                                                    variant="danger"
                                                                    wire:click="deleteTimeline({{ $timeItem->id }})"
                                                    >{{ __('timeline.menu.delete') }}
                                                    </flux:menu.item>
                                                </flux:menu>
                                            </flux:dropdown>

                                        </flux:table.cell>
                                    @endcan
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="3">{{ __('timeline.empty_list') }}</flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>


                </flux:card>
            </section>
        </flux:tab.panel>
    </flux:tab.group>

    <flux:modal name="confirm-resending-publication-notification"
                class="min-w-[22rem]"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('event.modal.resend_notification.heading') }}</flux:heading>
                <flux:text class="mt-2"><p>{{ __('event.modal.resend_notification.text_1', ['date' => $event->notification_sent_at ?? '']) }}</p>
                    <p>{{ __('event.modal.resend_notification.text_2') }}</p></flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('event.modal.resend_notification.btn_cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="reSendPublicationNotification"
                             variant="danger"
                >{{ __('event.modal.resend_notification.btn_confirm') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="add-subscription"
                variant="flyout"
                position="right"
                class="space-y-6"
    >
        <livewire:event.subscription.create.form :event-id="$event->id"/>

    </flux:modal>

    <flux:modal name="add-new-venue"
                variant="flyout"
                position="right"
                class="space-y-6"
    >
        <flux:heading size="lg">{{ __('venue.new.btn.label') }}</flux:heading>

        <livewire:venue.create.page/>

    </flux:modal>

    <flux:modal name="add-new-payment"
                variant="flyout"
                position="right"
                class="space-y-6"
    >

        <livewire:accounting.transaction.create.form :event="$form->event"/>

    </flux:modal>

    <flux:modal name="add-new-visitor"
                variant="flyout"
                position="right"
                class="space-y-6"
    >
        <livewire:event.visitor.create.form :event="$form->event"/>
    </flux:modal>

    <flux:modal name="assignment-modal"
                class="md:w-96 space-y-6"
                variant="flyout"
                position="right"
    >

        <form wire:submit="storeAssignment">
            <section class="space-y-6">

                @if($assignmentForm)
                    <flux:heading size="lg">{{ __('assignment.modal.heading.edit') }}</flux:heading>
                @else
                    <flux:heading size="lg">{{ __('assignment.modal.heading.new') }}</flux:heading>
                @endif
                <flux:select wire:model="assignmentForm.member_id"
                             variant="listbox"
                             searchable
                             label="{{__('assignment.lead')}}"
                >
                    @foreach(\App\Models\Membership\Member::select('id', 'name', 'first_name')->get() as $member)
                        <flux:select.option value="{{ $member->id }}">{{ $member->fullName() }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="assignmentForm.task"
                            label="{{__('assignment.task')}}"
                />

                <flux:select variant="listbox"
                             wire:model="assignmentForm.status"
                             label="{{__('assignment.status')}}"
                >
                    @foreach(App\Enums\AssignmentStatus::cases() as $status)
                        <flux:select.option value="{{$status->value}}">{{ App\Enums\AssignmentStatus::value($status->value) }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:textarea rows="auto"
                               wire:model="assignmentForm.description"
                               label="{{__('assignment.description')}}"
                />

                <flux:date-picker wire:model="assignmentForm.due_at"
                                  label="{{__('assignment.due_at')}}"
                />

                <flux:field>
                    <flux:label>{{__('assignment.amount')}}</flux:label>
                    <flux:input.group>
                        <flux:input wire:model="assignmentForm.amount"
                                    x-mask:dynamic="$money($input, ',', '.')"
                        />
                        <flux:input.group.suffix>EUR</flux:input.group.suffix>
                    </flux:input.group>
                    <flux:error name="assignmentForm.amount"/>
                </flux:field>

                <flux:button type="submit"
                             variant="primary"
                >{{ __('assignment.btn.submit.label') }}
                </flux:button>

            </section>
        </form>


    </flux:modal>

    <flux:modal name="timeline-modal"
                class="space-y-6"
                variant="flyout"
                position="right"
    >
        @if($timelineForm)
            <flux:heading size="lg">{{ __('timeline.modal.heading.edit') }}</flux:heading>
        @else
            <flux:heading size="lg">{{ __('timeline.modal.heading.new') }}</flux:heading>
        @endif
        <form wire:submit="storeTimeline">

            <section class="space-y-6">

                <flux:input wire:model="timelineForm.title"
                            label="{{ __('timeline.title') }}"
                />

                <flux:card class="space-y-6">
                    @foreach($form->locales as $locale)
                        <flux:input wire:model="timelineForm.title_extern.{{ $locale }}"
                                    label="{{ __('timeline.title_extern') }} {{ $locale }}"
                        />
                    @endforeach
                </flux:card>

                <flux:textarea rows="auto"
                               wire:model="timelineForm.description"
                               label="{{__('timeline.description')}}"
                />
                <flux:textarea rows="auto"
                               wire:model="timelineForm.notes"
                               label="{{__('timeline.notes')}}"
                />

                <flux:input type="time"
                            wire:model="timelineForm.start"
                            label="{{ __('timeline.start') }}"
                />
                <flux:input type="time"
                            wire:model="timelineForm.end"
                            label="{{ __('timeline.end') }}"
                />
                <flux:input wire:model="timelineForm.place"
                            label="{{ __('timeline.place') }}"
                />
                <flux:input wire:model="timelineForm.performer"
                            label="{{ __('timeline.performer') }}"
                />
                <flux:input wire:model="timelineForm.duration"
                            label="{{ __('timeline.duration') }}"
                />
                <flux:select wire:model="timelineForm.member_id"
                             variant="listbox"
                             searchable
                             clearable
                             label="{{__('timeline.table.header.member')}}"
                >
                    @foreach(\App\Models\Membership\Member::select('id', 'name', 'first_name')->get() as $member)
                        <flux:select.option value="{{ $member->id }}">{{ $member->fullName() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:button variant="primary"
                             type="submit"
                >{{ __('timeline.btn.submit.label') }}</flux:button>
            </section>

        </form>

    </flux:modal>

    <flux:modal name="add-box-office-payment" variant="flyout">
        <livewire:accounting.transaction.boxoffice.form :event="$event" />
    </flux:modal>
    <x-history-entries :histories="$this->histories" />
</div>