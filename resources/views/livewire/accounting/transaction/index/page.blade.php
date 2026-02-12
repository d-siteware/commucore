<div x-data="{showFilter: false}">
    <header class="flex justify-between items-center mb-3 lg:mb-6">

        <flux:heading size="xl">{{ __('transaction.index.title') }}</flux:heading>
        <flux:button icon="adjustments-horizontal"
                     size="sm"
                     @click="showFilter = ! showFilter"
        ></flux:button>
    </header>

    <nav class="my-2 gap-3 lg:flex items-center w-full space-y-2 lg:space-y-0">

        <livewire:accounting.fiscal-year-switcher.form/>

        <flux:input wire:model.live="search"
                    clearable
                    size="sm"
                    icon="magnifying-glass"
                    placeholder="{{ __('transaction.index.search.placeholder') }}"
                    class="grow"
        />


        <flux:select variant="listbox"
                     placeholder="{{ __('transaction.index.filter.date_range.placeholder') }}"
                     wire:model.live="filter_date_range"
                     size="sm"
                     class="shrink-2"
                     x-cloak
                     x-show="showFilter"
        >
            @foreach(App\Enums\DateRange::cases() as $range)
                <flux:select.option value="{{ $range->value }}">{{ $range->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select variant="listbox"
                     indicator="checkbox"
                     multiple
                     selected-suffix="{{ __('transaction.index.filter.type.suffix') }}"
                     placeholder="{{ __('transaction.index.filter.type.placeholder') }}"
                     wire:model.live="filter_type"
                     size="sm"
                     class="shrink-2"
                     x-cloak
                     x-show="showFilter"
        >
            @foreach(App\Enums\TransactionType::cases() as $type)
                <flux:select.option value="{{ $type->value }}">{{ $type->value }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select variant="listbox"
                     indicator="checkbox"
                     multiple
                     selected-suffix="{{ __('transaction.index.filter.status.suffix') }}"
                     placeholder="{{ __('transaction.index.filter.status.placeholder') }}"
                     wire:model.live="filter_status"
                     size="sm"
                     class="shrink-2"
                     x-cloak
                     x-show="showFilter"
        >
            @foreach(App\Enums\TransactionStatus::cases() as $status)
                <flux:select.option value="{{ $status->value }}">{{ $status->value }}</flux:select.option>
            @endforeach
        </flux:select>


    </nav>

    <flux:table :paginate="$this->transactions">
        <flux:table.columns>
            <flux:table.column>{{ __('transaction.index.table.columns.booking') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'date'"
                               :direction="$sortDirection"
                               wire:click="sort('date')"
                               class="hidden md:table-cell"
            >{{ __('transaction.index.table.columns.date') }}
            </flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'created'"
                               :direction="$sortDirection"
                               wire:click="sort('created_at')"
                               class="hidden md:table-cell"
            >{{ __('transaction.index.table.columns.created') }}
            </flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'status'"
                               :direction="$sortDirection"
                               wire:click="sort('status')"
                               class="hidden md:table-cell"
            >{{ __('transaction.index.table.columns.status') }}
            </flux:table.column>

            <flux:table.column sortable
                               :sorted="$sortBy === 'account_id'"
                               :direction="$sortDirection"
                               wire:click="sort('account')"
                               class="hidden sm:table-cell"

            >{{ __('transaction.index.table.columns.account') }}
            </flux:table.column>
            <flux:table.column align="right"
                               sortable
                               :sorted="$sortBy === 'amount'"
                               :direction="$sortDirection"
                               wire:click="sort('amount_gross')"
                               class="hidden sm:table-cell"
            >{{ __('transaction.index.table.columns.amount') }}
            </flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'type'"
                               :direction="$sortDirection"
                               wire:click="sort('type')"
                               class="hidden sm:table-cell"

            >{{ __('transaction.index.table.columns.type') }}
            </flux:table.column>
            <flux:table.column class="hidden lg:table-cell">{{ __('transaction.index.table.columns.receipt') }}</flux:table.column>
            <flux:table.column class="hidden lg:table-cell">{{ __('transaction.index.table.columns.linked') }}</flux:table.column>

        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->transactions as $item)

                <flux:table.row :key="$item->id">
                    <flux:table.cell variant="strong"
                                     class="flex lg:items-center justify-start flex-col lg:flex-row space-y-3 lg:space-y-0 items-start"
                    >
                        <span class="text-wrap">{{ $item->label}}</span>

                        <span class="lg:hidden inline-block">
                             <flux:badge size="sm"
                                         :color="\App\Enums\TransactionStatus::color($item->status)"
                                         inset="top bottom"
                             >{{ $item->status }}</flux:badge>
                        </span>
                        <span class="lg:hidden inline-block">
                            <span class="mr-1 text-xs">EUR</span>
                            <span class="{{ $item->grossColor() }}">{{ $item->grossForHumans() }}</span>
                        </span>
                        <span class="flex">
                        @if($item->reference)
                                <flux:tooltip toggleable>
                                <flux:button icon="chat-bubble-bottom-center-text"
                                             size="xs"
                                             variant="ghost"
                                />

                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                    {{ __('transaction.index.table.tooltip.reference') }}: {{ $item->reference }}
                                </flux:tooltip.content>
                            </flux:tooltip>
                            @endif

                            @if($item->description)
                                <flux:tooltip toggleable>
                                <flux:button icon="document-text"
                                             size="xs"
                                             variant="ghost"
                                />

                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                    {{ __('transaction.index.table.tooltip.description') }}: {{ $item->description }}
                                </flux:tooltip.content>
                            </flux:tooltip>
                            @endif
                        </span>
                    </flux:table.cell>

                    <flux:table.cell class="hidden md:table-cell">
                        {{ $item->date->isoFormat('Do MMM YY') }}
                    </flux:table.cell>
                    <flux:table.cell class="hidden md:table-cell">{{ $item->created_at->isoFormat('Do MMM YY') }}</flux:table.cell>

                    <flux:table.cell class="hidden md:table-cell">
                        <flux:badge size="sm"
                                    :color="\App\Enums\TransactionStatus::color($item->status)"
                                    inset="top bottom"
                        >{{ $item->status }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="hidden sm:table-cell">{{ $item->account->name . ' - ' . $item->account->number }}</flux:table.cell>
                    <flux:table.cell variant="strong"
                                     align="end"
                                     class="hidden sm:table-cell"
                    ><span class="{{ $item->grossColor() }}">{{ $item->grossForHumans() }}</span></flux:table.cell>
                    <flux:table.cell class="hidden sm:table-cell">
                        <flux:badge size="sm"
                                    :color="\App\Enums\TransactionType::badgeColor($item->type)"
                                    inset="top bottom"
                        >{{ $item->type }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="hidden lg:table-cell">

                        @if($item->receipts->count() > 0)
                            @foreach($item->receipts as $key => $receipt)
                                <flux:tooltip content="{{ $receipt->file_name_original }}"
                                              position="top"
                                >
                                    <flux:button
                                            wire:click="download({{$receipt->id}})"
                                            icon-trailing="document-arrow-down"
                                            size="xs"
                                    />
                                </flux:tooltip>
                            @endforeach
                        @else
                            -
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="hidden lg:table-cell">
                        <aside class="flex gap-2">
                            @if($item->event_transaction)
                                <flux:tooltip content="{{ __('transaction.index.table.tooltip.event_assigned') }}: {{ $item->event_transaction->event->title[app()->getLocale()] }}"
                                              position="top"
                                >
                                    <flux:icon.calendar-days class="size-4"
                                                             variant="mini"
                                    />
                                </flux:tooltip>
                            @endif
                            @if($item->member_transaction)
                                <flux:tooltip content="{{ __('transaction.index.table.tooltip.member_assigned') }} {{ $item->member_transaction->member->fullName() }}"
                                              position="top"
                                >
                                    <flux:icon.users class="size-4"
                                                     variant="mini"
                                    />
                                </flux:tooltip>
                                @if($item->member_transaction->receipt_sent_timestamp)
                                    <flux:tooltip content="{{ __('transaction.index.table.tooltip.receipt_sent') }} {{ $item->member_transaction->receipt_sent_timestamp }}"
                                                  position="top"
                                    >
                                        <flux:icon.envelope class="size-4"
                                                            variant="mini"
                                        />
                                    </flux:tooltip>
                                @endif

                            @endif
                        </aside>

                    </flux:table.cell>

                    @can('update', \App\Models\Accounting\Account::class)
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost"
                                             size="sm"
                                             icon="ellipsis-horizontal"
                                             inset="top bottom"
                                ></flux:button>

                                <flux:menu>
                                    <flux:menu.group heading="{{ __('transaction.index.menu-group.booking') }}"
                                                     class="mt-4"
                                    >
                                        @if(isset($item->status) && $item->status === App\Enums\TransactionStatus::submitted->value)
                                            <flux:menu.item icon="check"
                                                            wire:click="bookItem({{ $item->id }})"
                                            >{{ __('transaction.index.menu-item.book') }}
                                            </flux:menu.item>

                                            <flux:menu.item icon="pencil"
                                                            wire:click="editItem({{ $item->id }})"
                                            >{{ __('transaction.index.menu-item.edit') }}
                                            </flux:menu.item>
                                        @else
                                            <flux:menu.item icon="trash"
                                                            variant="danger"
                                                            wire:click="startCancelItem({{ $item->id }})"
                                                            :disabled="!isset($item->type) || $item->type === \App\Enums\TransactionType::Reversal->value"
                                            >{{ __('transaction.index.menu-item.cancel') }}
                                            </flux:menu.item>
                                            <flux:menu.item icon="document"
                                                            wire:click="editTransactionText({{ $item->id }})"
                                                            :disabled="!isset($item->type) || $item->type === \App\Enums\TransactionType::Reversal->value"
                                            >{{ __('transaction.index.menu-item.edit_text') }}
                                            </flux:menu.item>
                                            <flux:menu.item icon="arrows-right-left"
                                                            wire:click="changeAccount({{ $item->id }})"
                                                            :disabled="!isset($item->type) || $item->type === \App\Enums\TransactionType::Reversal->value"
                                            >{{ __('transaction.index.menu-item.rebook') }}
                                            </flux:menu.item>
                                        @endif

                                        <flux:menu.separator/>

                                        <flux:menu.submenu heading="{{ __('transaction.index.menu-submenu.assign') }}"
                                                           icon="link"
                                        >
                                            <flux:menu.item icon="calendar-days"
                                                            wire:click="appendToEvent({{ $item->id }})"
                                            >{{ __('transaction.index.menu-item.attach_event') }}
                                            </flux:menu.item>
                                            <flux:menu.item icon="users"
                                                            wire:click="appendToMember({{ $item->id }})"
                                            >{{ __('transaction.index.menu-item.attach_member') }}
                                            </flux:menu.item>
                                        </flux:menu.submenu>

                                        @if(isset($item->event_transaction) && isset($item->event_transaction->id) )
                                            <flux:menu.submenu heading="{{ __('transaction.index.menu-submenu.detach') }}"
                                                               icon="link-slash"
                                            >
                                                @if(isset($item->event_transaction) && isset($item->event_transaction->id))
                                                    <flux:menu.item icon="calendar-days"
                                                                    wire:click="detachEvent({{ $item->event_transaction->id }})"
                                                    >{{ __('transaction.index.menu-item.detach_event') }}
                                                    </flux:menu.item>
                                                @endif

                                                @if(isset($item->member_transaction) && isset($item->member_transaction->id))
                                                    <flux:menu.item icon="users"
                                                                    wire:click="detachMember({{ $item->member_transaction->id }})"
                                                    >{{ __('transaction.index.menu-item.detach_member') }}
                                                    </flux:menu.item>
                                                @endif
                                            </flux:menu.submenu>
                                        @endif


                                    </flux:menu.group>

                                    @if(isset($item->member_transaction) && isset($item->member_transaction->id))
                                        <flux:menu.group heading="{{ __('transaction.index.menu-group.receipt') }}"
                                                         class="mt-4"
                                        >

                                            @if(is_null($item->member_transaction->receipt_sent_timestamp))
                                                <flux:menu.item icon="envelope"
                                                                wire:click="sendInvoice({{ $item->id }})"
                                                >{{ __('transaction.index.menu-item.send_invoice') }}
                                                </flux:menu.item>
                                            @else
                                                <flux:menu.item icon="envelope"
                                                                wire:confirm="{{ __('transaction.index.confirm.resend_invoice') }}"
                                                                wire:click="sendInvoice({{ $item->id }})"
                                                >{{ __('transaction.index.menu-item.send_invoice') }}
                                                </flux:menu.item>

                                            @endif

                                            <flux:menu.item icon="printer"
                                                            href="{{ route('transaction.invoice.preview', $item->id) }}"
                                                            target="_blank"
                                            >{{ __('transaction.index.menu-item.print_invoice') }}
                                            </flux:menu.item>
                                        </flux:menu.group>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    @endcan

                </flux:table.row>
            @empty
                <flux:table.row key="0">
                    <flux:table.cell colspan="6"
                                     class=" space-y-2"
                    >
                        <flux:text>{{ __('transaction.index.table.empty-results') }}</flux:text>

                    </flux:table.cell>

                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @can('create', \App\Models\Accounting\Account::class)
        <div class="flex mt-3">
            <flux:spacer/>
            <flux:button variant="primary"
                         href="{{ route('transaction.create') }}"
            >{{ __('transaction.index.btn.create') }}
            </flux:button>
        </div>
    @endcan



    <!-- MODALS -->

    <flux:modal name="edit-transaction"
                class=" space-y-6"
                variant="flyout"
                position="right"
    >
        <flux:heading size="lg">{{ __('transaction.index.modal.edit.heading') }}</flux:heading>

        @if($transaction)
            <livewire:accounting.transaction.create.form :transactionId="$transaction->id"/>
        @endif

    </flux:modal>

    <flux:modal name="book-transaction"
                class=" space-y-6"
                variant="flyout"
                position="right"
    >
        @if($transaction)
            <livewire:accounting.transaction.booking.form :transactionId="$transaction->id"/>
        @endif
    </flux:modal>

    <flux:modal name="append-to-event-transaction"
                variant="flyout"
                position="right"
    >

        <flux:heading class="my-4">{{ __('transaction.index.modal.append_event.heading') }}</flux:heading>

        <form wire:submit="appendEvent"
              class="space-y-6"
        >

            <flux:field>
                <flux:select wire:model="target_event"
                             variant="listbox"
                             searchable
                             placeholder="{{ __('transaction.index.modal.append_event.select_placeholder') }}"
                >
                    @foreach(\App\Models\Event\Event::select('id', 'name')->get() as $key => $event)
                        <flux:select.option value="{{ $event->id }}">{{ $event->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="target_event"/>
            </flux:field>

            <flux:accordion transition>
                <flux:accordion.item heading="{{ __('transaction.index.modal.append_event.optional') }}">
                    <section class=" space-y-4">
                        <flux:input label="{{ __('event.visitor.name') }}"
                                    wire:model="event_visitor_name"
                        />

                        <flux:radio.group wire:model="event_gender"
                                          label="{{ __('members.gender') }}"
                                          variant="segmented"
                        >
                            @foreach( App\Enums\Gender::cases() as $gender)
                                <flux:radio value="{{ $gender->value }}">{{ App\Enums\Gender::value($gender->value) }}</flux:radio>
                            @endforeach
                        </flux:radio.group>
                    </section>

                </flux:accordion.item>
            </flux:accordion>

            <flux:button variant="primary"
                         type="submit"
            >{{ __('transaction.index.modal.append_event.btn.submit') }}
            </flux:button>
        </form>

    </flux:modal>

    <flux:modal name="append-to-member-transaction"
                variant="flyout"
                position="right"
    >

        <flux:heading class="my-4">{{ __('transaction.index.modal.append_member.heading') }}</flux:heading>

        <form wire:submit="appendMember"
              class="space-y-6"
        >

            <flux:field>
                <flux:select wire:model="target_member"
                             variant="listbox"
                             searchable
                             placeholder="{{ __('transaction.index.modal.append_member.select_placeholder') }}"
                >
                    @foreach(App\Models\Membership\Member::select('id', 'name', 'first_name')->get() as $key => $member)
                        <flux:select.option value="{{ $member->id }}">{{ $member->fullName() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="target_member"/>
                <flux:error name="transaction.id"/>
            </flux:field>

            <flux:separator text="{{ __('transaction.index.modal.append_member.membership_fees') }}"/>

            <flux:checkbox wire:model="is_membership_fee"
                           label="{{ __('transaction.index.modal.append_member.is_membership_fee') }}"
            />

            <flux:input type="number"
                        wire:model="fee_year"
                        label="{{ __('transaction.index.modal.append_member.fee_year') }}"
            />

            <flux:button variant="primary"
                         type="submit"
            >{{ __('transaction.index.modal.append_member.btn.submit') }}
            </flux:button>
        </form>
    </flux:modal>

    <flux:modal name="edit-transaction-text"
                variant="flyout"
                position="right"
    >
        <flux:heading size="lg">{{ __('transaction.edit-text-modal.heading') }}</flux:heading>

        <form wire:submit="changeTransactionText"
              class="space-y-6"
        >


            <flux:input wire:model="edit_text_form.label"
                        label="{{ __('transaction.edit-text-modal.label') }}"
            />
            <flux:input wire:model="edit_text_form.reference"
                        label="{{ __('transaction.edit-text-modal.reference') }}"
            />
            <flux:textarea rows="auto"
                           wire:model="edit_text_form.description"
                           label="{{ __('transaction.edit-text-modal.description') }}"
            />

            <flux:button variant="primary"
                         type="submit"
            >{{ __('transaction.edit-text-modal.btn.label') }}</flux:button>
        </form>
    </flux:modal>

    <flux:modal name="account-transfer-modal"
                variant="flyout"
                position="right"
                class="space-y-6 w-full lg:w-64"
    >
        <flux:heading size="lg">{{ __('transaction.account-transfer-modal.heading') }}</flux:heading>
        <flux:text>{{ __('transaction.account-transfer-modal.content') }}</flux:text>
        <form wire:submit="transferAccount"
              class="space-y-6"
        >

            <flux:select wire:model="transfer_transaction_form.account_id"
                         size="sm"
                         placeholder="{{ __('transaction.account-transfer-modal.account_placeholder') }}"
                         variant="listbox"
                         label="{{ __('transaction.account-transfer-modal.new_account') }}"
                         clearable
                         searchable
            >
                @foreach(App\Models\Accounting\Account::select('id', 'name')->get() as $key => $account)
                    <flux:select.option :key
                                        value="{{ $account->id }}"
                    >{{ $account->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <input type="hidden"
                   wire:model="transfer_transaction_form.transaction_id"
            >
            <flux:textarea wire:model="transfer_transaction_form.reason"
                           label="{{ __('transaction.account-transfer-modal.reason') }}"
            />
            <flux:button variant="primary"
                         type="submit"
            >{{ __('transaction.account-transfer-modal.btn.submit') }}</flux:button>
        </form>
        <x-debug/>
    </flux:modal>

    <flux:modal name="cancel-transaction"
                class="w-1/5 pt-6 space-y-6"
    >

        <flux:heading size="lg"><span class="text-red-600">{{ __('transaction.cancel-transaction-modal.heading') }}</span></flux:heading>
        @if($transaction)
            <livewire:accounting.transaction.cancel.form :transaction-id="$transaction->id"/>
        @endif
    </flux:modal>

</div>