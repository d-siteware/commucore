<div class="space-y-6">

    <flux:heading size="xl">{{ __('members.show.title',['name' => $member->first_name . ' ' . $member->name]) }}</flux:heading>
    <flux:text size="sm">{{ __('members.show.created_at') }}: {{ $member->created_at }} | {{ __('members.show.updated_at') }}: {{ $member->updated_at }}</flux:text>

    <flux:tab.group>
        <flux:tabs wire:model.lazy="selectedTab">
            <flux:tab name="member-show-profile"
                      icon="user"
                      wire:click="setSelectedTab('member-show-profile')"
            ><span class="hidden sm:flex">{{ __('members.show.about') }}</span>
            </flux:tab>
            <flux:tab name="member-show-account"
                      icon="cog-6-tooth"
                      wire:click="setSelectedTab('member-show-account')"
            ><span class="hidden sm:flex">{{ __('members.show.membership') }}</span>
            </flux:tab>
            @can('viewAny', \App\Models\MemberChangeRequest::class)
            <flux:tab name="member-show-member-requests"
                      icon="bolt"
                      wire:click="setSelectedTab('member-show-member-requests')"
            ><span class="hidden sm:flex">{{ __('members.show.change_requests') }}</span>
            </flux:tab>
            @endcan
            <flux:tab name="member-show-billing"
                      icon="banknotes"
                      wire:click="setSelectedTab('member-show-billing')"
            ><span class="hidden sm:flex">{{ __('members.show.payments') }}</span>
            </flux:tab>
            <flux:tab name="member-show-documents"
                      icon="document-text"
                      wire:click="setSelectedTab('member-show-documents')"
            ><span class="hidden sm:flex">{{ __('members.show.documents') }}</span>
            </flux:tab>
            <flux:tab name="member-show-sepa"
                      icon="currency-euro"
                      wire:click="setSelectedTab('member-show-sepa')"
            ><span class="hidden sm:flex">{{ __('sepa.mandate.heading') }}</span>
            </flux:tab>
        </flux:tabs>

        <flux:tab.panel name="member-show-profile">
            <section class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <form wire:submit="updateMemberData">
                    <flux:card class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <flux:input wire:model.blur="memberForm.first_name"
                                    label="{{ __('members.first_name') }}"
                        />

                        <flux:input wire:model.blur="memberForm.name"
                                    label="{{ __('members.name') }}"
                        />


                            <flux:date-picker locale="{{ app()->getLocale() }}" with-today
                                              selectable-header
                                              wire:model.blur="memberForm.birth_date"
                                              wire:blur="checkBirthDate"
                                              label="{{ __('members.birth_date') }}"
                                              autocomplete="bday"
                                              start-day="1"
                            />
                            <flux:input wire:model.blur="memberForm.birth_place"
                                        label="{{ __('members.birth_place') }}"
                                        autocomplete="address-level1"
                            />
                        </div>
                        <flux:textarea wire:model.blur="memberForm.address"
                                       rows="auto"
                                       label="{{ __('members.address') }}"
                        />
                        <flux:input wire:model.blur="memberForm.zip"
                                    label="{{ __('members.zip') }}"
                                    class="w-24"
                        />
                        <flux:input wire:model.blur="memberForm.city"
                                    label="{{ __('members.city') }}"
                        />

                        <flux:input wire:model.blur="memberForm.country"
                                    label="{{ __('members.country') }}"
                        />
                        @can('update', $member)
                            <flux:button variant="primary"
                                         type="submit"
                            >{{ __('members.show.store') }}
                            </flux:button>
                        @endcan
                    </flux:card>

                </form>

                <form wire:submit="updateMemberData">
                    <flux:card class="space-y-6">

                        <flux:input wire:model.blur="memberForm.email"
                                    label="E-Mail"
                        />

                        <flux:input wire:model.blur="memberForm.phone"
                                    label="{{ __('members.phone') }}"
                                    mask="+99 99 99999999"
                                    placeholder="+49 30 12345678"
                                    autocomplete="tel"
                        />

                        <flux:input wire:model.blur="memberForm.mobile"
                                    label="{{ __('members.mobile') }}"
                                    mask="+99 999 99999999"
                                    placeholder="+49 173 12345678"
                                    autocomplete="tel"
                        />

                        <flux:radio.group wire:model="memberForm.locale"
                                          label="{{ __('members.locale') }}"
                                          variant="segmented"
                                          size="sm"
                        >
                            @foreach(\App\Models\Locale::getNames() as $key => $locale)
                                <flux:radio :key
                                            value="{{ $locale }}"
                                            label="{{ $locale  }}"
                                />
                            @endforeach
                        </flux:radio.group>

                        <flux:radio.group wire:model="memberForm.gender"
                                          label="{{ __('members.gender') }}"
                                          variant="segmented"
                                          size="sm"
                        >
                            @foreach(\App\Enums\Gender::options() as $value => $label)
                                <flux:radio :key
                                            value="{{ $value }}"
                                >{{ $label }}</flux:radio>
                            @endforeach
                        </flux:radio.group>

                        <flux:radio.group wire:model="memberForm.family_status"
                                          label="{{ __('members.familystatus.label') }}"
                                          variant="segmented"
                                          size="sm"
                        >
                            @foreach(\App\Enums\MemberFamilyStatus::options() as $value => $label)
                                <flux:radio :key
                                            value="{{ $value }}"
                                >{{ $label }}</flux:radio>
                            @endforeach
                        </flux:radio.group>

                        @can('update', $member)
                            <flux:spacer/>
                            <flux:button variant="primary"
                                         type="submit"
                            >{{ __('members.show.store') }}
                            </flux:button>
                        @endcan
                    </flux:card>
                </form>
            </section>
        </flux:tab.panel>

        <flux:tab.panel name="member-show-account">
            <section class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @can('create',\App\Models\Membership\Member::class)
                <form wire:submit="updateMemberData">

                    <flux:card class="space-y-6">
                            <flux:date-picker locale="{{ app()->getLocale() }}" wire:model.blur="memberForm.applied_at"
                                              label="{{ __('members.date.applied_at') }}"
                                              selectable-header
                                              with-today
                                              start-day="1"
                            />
                            <flux:button variant="primary"
                                         type="submit"
                            >{{ __('members.show.store') }}
                            </flux:button>

                                <div class="lg:hidden">
                                    <flux:radio.group wire:model="memberForm.type"
                                                      label="{{ __('members.type.label') }}"
                                                      variant="cards"
                                                      class="max-sm:flex-col"
                                    >

                                        @foreach(\App\Enums\MemberType::options() as $value => $label)
                                            <flux:radio value="{{ $value }}"
                                                        label="{{ $label }}"
                                            />
                                        @endforeach
                                    </flux:radio.group>
                                </div>
                            <div class="hidden lg:block">
                                <flux:select wire:model.blur="memberForm.type"
                                             label="{{ __('members.type.label') }}"
                                >

                                    @foreach(\App\Enums\MemberType::options() as $value => $label)
                                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>

                            <flux:radio.group wire:model="memberForm.fee_type"
                                              label="{{ __('members.fee-type.label') }}"
                                              variant="cards"
                                              class="max-sm:flex-col"
                            >
                                @foreach(\App\Enums\MemberFeeType::options() as $value => $label)
                                    <flux:radio value="{{ $value }}"
                                                label="{{ $label }}"
                                    />
                                @endforeach
                            </flux:radio.group>

                        <flux:textarea wire:model.blur="memberForm.deduction_reason"
                                       rows="auto"
                                       label="{{ __('members.apply.discount.reason.label') }}"
                        />
                        <flux:spacer/>
                        <flux:button variant="primary"
                                     type="submit"
                        >{{ __('members.show.store') }}
                        </flux:button>
                    </flux:card>
                </form>
                <flux:card class="space-y-6">
                    <flux:field>
                        @if($fee_type === \App\Enums\MemberFeeType::FREE->value )
                            <flux:badge color="lime"
                                        size="lg"
                            >{{ __('members.fee-type.free') }}
                                <flux:icon.check-circle variant="mini"/>
                            </flux:badge>
                        @else
                            @if($feeStatus)
                                <flux:badge color="lime"
                                            size="lg"
                                >{{ __('members.show.fee_msg.paid') }}: <span class="mx-1.5 text-sm">{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</span> {{$openFees}}
                                    <flux:icon.check-circle variant="mini"/>
                                </flux:badge>
                            @else
                                <flux:badge color="orange">{{ __('members.show.fee_msg.paid') }}: <span class="mx-1.5 text-sm">{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</span> {{$openFees}}
                                    <flux:icon.bolt variant="mini"/>
                                </flux:badge>
                            @endif
                        @endif
                    </flux:field>

                    <flux:field>
                        @if($member->verified_at)
                            <flux:text>{{ __('members.date.verified_at') }} {{ $member->verified_at }}</flux:text>
                            <flux:heading size="lg">{{ $member->verified_at->diffForHumans() }}</flux:heading>
                        @else
                            <flux:button variant="filled">{{ __('members.btn.sendVerificationMail.label') }}</flux:button>
                        @endif
                    </flux:field>
                    <flux:field>
                        @if($member->entered_at)
                            <flux:text>{{ __('members.date.entered_at') }} {{ $member->entered_at }}</flux:text>
                            <flux:heading size="lg">{{ $member->entered_at->diffForHumans() }}</flux:heading>
                        @else
                            <flux:button wire:click="acceptApplication(false)"
                            >{{ __('members.btn.sendAcceptance.label') }}</flux:button>
                            <flux:button variant="primary"
                                         wire:click="acceptApplication"
                            >{{ __('members.btn.sendAcceptanceMail.label') }}</flux:button>

                        @endif
                    </flux:field>

                    @if(! $memberForm->user_id)
                        <flux:field>
                            @if($invitation_status === 'none')
                                <flux:button type="button"
                                             wire:click="sendInvitation"
                                >{{ __('members.btn.inviteAsUser.label') }}
                                </flux:button>
                                <flux:error for="email"/>
                            @elseif($invitation_status === 'invited')
                                <flux:badge color="lime"
                                            size="lg"
                                            icon="envelope"
                                >{{ __('members.show.invitation_sent') }}</flux:badge>
                            @endif
                        </flux:field>
                    @endif

                    <flux:field class="space-y-2">
                        @if($member->left_at)

                            <div>
                                <flux:text>{{ __('members.date.left_at') }} {{ $member->left_at }}</flux:text>
                                <flux:heading size="lg">{{ $member->left_at->diffForHumans() }}</flux:heading>
                            </div>

                            @can('delete',$this->member)
                                <flux:button variant="primary"
                                             wire:click="reactivateMembership"
                                >{{ __('members.show.member.reactivate') }}
                                </flux:button>
                            @endcan
                        @else
                            @if($member->entered_at)
                                @can('delete',$this->member)
                                    <flux:button variant="danger"
                                                 wire:click="cancelMember"
                                    >{{ __('members.btn.cancelMembership.label') }}</flux:button>

                                @endcan
                            @endif

                        @endif
                            <flux:field>
                                @if($memberForm->user_id)
                                    <flux:label>{{ __('members.linked_user') }}</flux:label>
                                    <div class="flex gap-3">
                                        <flux:badge color="lime"
                                                    size="lg"
                                                    class="flex-1"
                                        >{{ $memberForm->linked_user_name }}</flux:badge>
                                        <flux:button size="sm"
                                                     variant="danger"
                                                     wire:click="detachUser({{$memberForm->user_id}})"
                                                     icon="trash"
                                        ><span class="hidden lg:flex">{{ __('members.unlink_user') }}</span>

                                        </flux:button>
                                    </div>
                                @else
                                    <flux:button.group>
                                        <flux:select variant="listbox"
                                                     wire:model.blur="memberForm.newUser"
                                                     searchable
                                                     placeholder="{{ __('members.show.attached.placeholder') }}"
                                        >
                                            <flux:select.option wire:key="0"
                                                                value="0"
                                            >{{ __('members.show.select_user') }}
                                            </flux:select.option>
                                            @forelse($users as $user)
                                                <flux:select.option wire:key="{{ $user->id }}"
                                                                    value="{{ $user->id }}"
                                                >{{ $user->name }}</flux:select.option>
                                            @empty
                                                <flux:select.option wire:key="0"
                                                                    value="0"
                                                >{{ __('members.show.empty_user_list') }}
                                                </flux:select.option>

                                            @endforelse
                                        </flux:select>
                                        <flux:button square
                                                     wire:click="attachUser"
                                        >
                                            <flux:icon.user-plus variant="micro"
                                                                 class="text-emerald-500 dark:text-emerald-300"
                                            />
                                        </flux:button>
                                    </flux:button.group>
                                @endif
                            </flux:field>
                    </flux:field>

                </flux:card>
                @else
                    <flux:card class="space-y-6">
                        <flux:field>
                            <flux:text size="sm">{{ __('members.date.applied_at') }} {{ $member->applied_at }}</flux:text>
                            <flux:heading>{{ $member->applied_at->diffForHumans() }}</flux:heading>
                        </flux:field>
                        <flux:field>
                            <flux:text size="sm">{{ __('members.type.label') }}</flux:text>
                            <flux:badge size="lg"
                                        color="{{ $member->type->color()  }}"
                            >{{ $member->type->label() }}</flux:badge>
                        </flux:field>
                        <div class="grid lg:grid-cols-2 lg:gap-6">
                            <flux:field>
                                <flux:text size="sm">{{ __('members.fee-type.label') }}</flux:text>
                                <flux:badge size="lg"
                                            color="{{ $member->fee_type->color()}}"
                                > {{ $member->fee_type->label() }}</flux:badge>
                            </flux:field>
                            <flux:field>
                                <flux:text size="sm">{{ __('members.show.fee_msg.paid') }}: </flux:text>
                                @if($feeStatus)
                                    <flux:badge color="lime"
                                                size="lg"
                                    ><span class="mx-1.5">{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</span>{{$openFees}}
                                        <flux:icon.check-circle variant="mini"/>
                                    </flux:badge>
                                @else
                                    <flux:badge color="orange"><span class="mx-1.5">{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</span>{{$openFees}}
                                        <flux:icon.bolt variant="micro"/>
                                    </flux:badge>
                                @endif
                            </flux:field>
                        </div>
                        <flux:field>
                            <flux:text size="sm">{{ __('members.apply.discount.reason.label') }}</flux:text>
                            <flux:heading>{{ $memberForm->deduction_reason }}</flux:heading>
                        </flux:field>
                        
                        <flux:field>
                            <flux:text size="sm">{{ __('members.date.verified_at') }} {{ $member->verified_at }}</flux:text>
                            <flux:heading>{{ $member->verified_at?->diffForHumans() ?? '-'}}</flux:heading>
                        </flux:field>
                        <flux:field>
                            <flux:text size="sm">{{ __('members.date.entered_at') }} {{ $member->entered_at }}</flux:text>
                            <flux:heading>{{ $member->entered_at?->diffForHumans() ?? '-' }}</flux:heading>
                        </flux:field>
                        <flux:field>
                            @if($memberForm->user_id)
                                <flux:label>{{ __('members.linked_user') }}</flux:label>
                                <div class="flex gap-3">
                                    <flux:badge color="lime"
                                                size="lg"
                                                class="flex-1"
                                    >{{ $memberForm->linked_user_name }}</flux:badge>
                                    @can('update',$member)
                                    <flux:button size="sm"
                                                 variant="danger"
                                                 wire:click="detachUser({{$memberForm->user_id}})"
                                                 icon="trash"
                                    ><span class="hidden lg:flex">{{ __('members.unlink_user') }}</span></flux:button>
                                    @endcan
                                </div>
                            @else
                                <flux:button.group>
                                    <flux:select variant="listbox"
                                                 wire:model.blur="memberForm.newUser"
                                                 searchable
                                                 placeholder="{{ __('members.show.attached.placeholder') }}"
                                    >
                                        <flux:select.option wire:key="0"
                                                            value="0"
                                        >{{ __('members.show.select_user') }}
                                        </flux:select.option>
                                        @forelse($users as $user)
                                            <flux:select.option wire:key="{{ $user->id }}"
                                                                value="{{ $user->id }}"
                                            >{{ $user->name }}</flux:select.option>
                                        @empty
                                            <flux:select.option wire:key="0"
                                                                value="0"
                                            >{{ __('members.show.empty_user_list') }}
                                            </flux:select.option>

                                        @endforelse
                                    </flux:select>
                                    <flux:button square
                                                 wire:click="attachUser"
                                    >
                                        <flux:icon.user-plus variant="micro"
                                                             class="text-emerald-500 dark:text-emerald-300"
                                        />
                                    </flux:button>
                                </flux:button.group>
                            @endif
                        </flux:field>
                    </flux:card>
                    <flux:card class="space-y-6">
                        <livewire:member.change-request.create :member="$member" />
                        <livewire:member.change-request.table :member="$member" />
                        <livewire:member.cancellation-request.create :member="$member" />
                    </flux:card>
                @endcan
            </section>
        </flux:tab.panel>

        @can('viewAny', \App\Models\MemberChangeRequest::class)
        <flux:tab.panel name="member-show-member-requests">
            <section class="grid grid-cols-1 sm:grid-cols-2 gap-6">
           @can('viewAny', \App\Models\MemberChangeRequest::class)
               <livewire:member.change-request.review :member="$member" />
           @endcan
           @can('viewAny', \App\Models\MemberCancellationRequest::class)
               <livewire:member.cancellation-request.review :member="$member" />
           @endcan
            </section>
        </flux:tab.panel>
        @endcan
        <flux:tab.panel name="member-show-billing">
            <flux:card class="space-y-6">

                <nav class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-payment">
                        <flux:button variant="primary"
                                     size="sm"
                        >{{ __('members.show.new_payment') }}
                        </flux:button>
                    </flux:modal.trigger>


                    <flux:input clearable
                                wire:model.live="searchPayment"
                                size="sm"
                                placeholder="{{ __('common.search') }}"
                    />
                </nav>

                <flux:subheading>{{ __('members.show.payments_made') }}</flux:subheading>

                <flux:table :paginate="$this->payments">
                    <flux:table.columns>
                        <flux:table.column>{{ __('members.show.payment_label') }}</flux:table.column>
                        <flux:table.column sortable
                                           :sorted="$sortBy === 'transaction.date'"
                                           :direction="$sortDirection"
                                           wire:click="sort('date')"
                                           class="hidden md:table-cell"
                        >{{ __('common.date') }}
                        </flux:table.column>
                        <flux:table.column sortable
                                           :sorted="$sortBy === 'transaction.status'"
                                           :direction="$sortDirection"
                                           wire:click="sort('status')"
                                           align="right"
                                           class="hidden lg:table-cell"
                        >{{ __('members.show.amount') }}
                        </flux:table.column>
                        <flux:table.column class="hidden md:table-cell"
                        >{{ __('members.show.receipts') }}
                        </flux:table.column>
                        <flux:table.column sortable
                                           :sorted="$sortBy === 'transaction.amount'"
                                           :direction="$sortDirection"
                                           wire:click="sort('amount')"
                                           class="hidden md:table-cell"
                        >{{ __('common.status') }}
                        </flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->payments as $payment)
                            <flux:table.row :key="$payment->id">

                                <flux:table.cell variant="strong">

                                    {{ $payment->transaction->label }}
                                </flux:table.cell>

                                <flux:table.cell class="hidden lg:table-cell">{{ $payment->transaction->date->diffForHumans() }}</flux:table.cell>

                                <flux:table.cell variant="strong"
                                                 align="end"
                                                 class="hidden md:table-cell"
                                >{{ $payment->transaction->grossForHumans() }}</flux:table.cell>
                                <flux:table.cell class="hidden lg:table-cell">

                                    @if($payment->transaction->receipts->count() > 0)
                                        @foreach($payment->transaction->receipts as $key => $receipt)

                                            <flux:tooltip content="{{ $receipt->file_name_original }}"
                                                          position="top"
                                            >
                                                <flux:button
                                                        wire:click="download({{$payment->transaction->receipt}})"
                                                        icon-trailing="document-arrow-down"
                                                        size="xs"
                                                />
                                            </flux:tooltip>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge color="{{ $payment->transaction->status->color() }}" size="sm">{{ $payment->transaction->status->label() }}</flux:badge>

                                </flux:table.cell>

                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

            </flux:card>
        </flux:tab.panel>

        <flux:tab.panel name="member-show-documents">
            <livewire:app.global.documents
                    :model="$member"
                    :category-enum="\App\Enums\MemberDocumentCategory::class"
                    :key="'member-documents-'.$member->id"
            />
        </flux:tab.panel>

        <flux:tab.panel name="member-show-sepa">
            <livewire:member.sepa-mandate.manage
                    :member="$member"
                    :key="'sepa-'.$member->id"
            />
        </flux:tab.panel>

    </flux:tab.group>

    <flux:modal name="add-new-payment"
                variant="flyout"
                position="right"
                class="space-y-6"
    >
        <livewire:accounting.transaction.create.form :member="$member"/>
    </flux:modal>

    <flux:modal name="delete-membership">
        <form wire:submit="deleteMembershipForSure"
              class="space-y-6"
        >
            <div>
                <flux:heading size="lg">{{ __('members.cancel.modal.title') }}</flux:heading>

                <flux:subheading>
                    <p>{{ __('members.cancel.modal.text') }}</p>
                </flux:subheading>
            </div>

            <div>
                <flux:input wire:model.live="confirm_deletion_text"
                            label="{{ __('members.cancel.confirm_text_input.label') }}"
                />
            </div>

            @if($memberForm->user_id)
                {{ __('members.show.delete_user') }}
            @endif

            <div class="flex gap-2">
                <flux:spacer/>

                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('profile.2FA.modal-confirm.btn.cancel.label') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit"
                             variant="danger"
                             :disabled="$confirm_deletion_text !== $memberForm->name"
                >{{ __('members.cancel.btn.final.label') }}</flux:button>
            </div>
        </form>
    </flux:modal>


</div>
