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
            <flux:tab name="member-show-billing"
                      icon="banknotes"
                      wire:click="setSelectedTab('member-show-billing')"
            ><span class="hidden sm:flex">{{ __('members.show.payments') }}</span>
            </flux:tab>
        </flux:tabs>

        <flux:tab.panel name="member-show-profile">
            <section class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <form wire:submit="updateMemberData">
                    <flux:card class="space-y-6">

                        <flux:input wire:model="memberForm.first_name"
                                    label="{{ __('members.first_name') }}"
                        />

                        <flux:input wire:model="memberForm.name"
                                    label="{{ __('members.name') }}"
                        />

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            <flux:date-picker with-today
                                              selectable-header
                                              wire:model="memberForm.birth_date"
                                              wire:blur="checkBirthDate"
                                              label="{{ __('members.birth_date') }}"
                                              autocomplete="bday"
                                              start-day="1"
                            />
                            <flux:input wire:model="memberForm.birth_place"
                                        label="{{ __('members.birth_place') }}"
                                        autocomplete="address-level1"
                            />
                        </div>
                        <flux:textarea wire:model="memberForm.address"
                                       rows="auto"
                                       label="{{ __('members.address') }}"
                        />
                        <flux:input wire:model="memberForm.zip"
                                    label="{{ __('members.zip') }}"
                                    class="w-24"
                        />
                        <flux:input wire:model="memberForm.city"
                                    label="{{ __('members.city') }}"
                        />

                        <flux:input wire:model="memberForm.country"
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

                        <flux:input wire:model="memberForm.email"
                                    label="E-Mail"
                        />

                        <flux:input wire:model="memberForm.phone"
                                    label="{{ __('members.phone') }}"
                                    mask="+99 99 99999999"
                                    placeholder="+49 30 12345678"
                                    autocomplete="tel"
                        />

                        <flux:input wire:model="memberForm.mobile"
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
                            @foreach(\App\Enums\Locale::toArray() as $key => $locale)
                                <flux:radio :key
                                            value="{{ $locale }}"
                                            label="{{ \App\Enums\Locale::value($locale)  }}"
                                />
                            @endforeach
                        </flux:radio.group>

                        <flux:radio.group wire:model="memberForm.gender"
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

                        <flux:radio.group wire:model="memberForm.family_status"
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

                <form wire:submit="updateMemberData">

                    <flux:card class="space-y-6">
                        @can('create',\App\Models\Membership\Member::class)
                            <flux:date-picker wire:model="memberForm.applied_at"
                                              label="{{ __('members.date.applied_at') }}"
                                              selectable-header
                                              with-today
                                              start-day="1"
                            />
                            <flux:button variant="primary"
                                         type="submit"
                            >{{ __('members.show.store') }}
                            </flux:button>
                        @else
                            <flux:field>
                                <flux:text>{{ __('members.date.applied_at') }} {{ $member->applied_at }}</flux:text>
                                <flux:heading size="lg">{{ $member->applied_at->diffForHumans() }}</flux:heading>
                            </flux:field>
                        @endcan
                        @can('update', $member)
                            <flux:radio.group wire:model="memberForm.type"
                                              label="{{ __('members.type') }}"
                                              variant="cards"
                                              class="max-sm:flex-col"
                            >

                                @foreach(\App\Enums\MemberType::cases() as $key => $type)
                                    <flux:radio value="{{ $type->value }}"
                                                label="{{ \App\Enums\MemberType::value($type->value) }}"
                                    />
                                @endforeach
                            </flux:radio.group>
                        @else
                            <flux:field>
                                <flux:label>{{ __('members.type') }}</flux:label>
                                <flux:badge size="lg"
                                            color=" {{ \App\Enums\MemberType::color($member_type) }}"
                                > {{ \App\Enums\MemberType::value($member_type) }}</flux:badge>
                            </flux:field>
                        @endcan

                        @can('update', $member)
                            <flux:radio.group wire:model="memberForm.fee_type"
                                              label="{{ __('members.fee_type') }}"
                                              variant="cards"
                                              class="max-sm:flex-col"
                            >
                                @foreach(\App\Enums\MemberFeeType::cases() as $key => $type)
                                    <flux:radio value="{{ $type->value }}"
                                                label="{{ \App\Enums\MemberFeeType::value($type->value) }}"
                                    />
                                @endforeach
                            </flux:radio.group>
                        @else
                            <flux:field>
                                <flux:label>{{ __('members.fee_type') }}</flux:label>
                                <flux:badge size="lg"
                                            color=" {{ \App\Enums\MemberFeeType::color($member_type) }}"
                                > {{ \App\Enums\MemberFeeType::value($member_type) }}</flux:badge>
                            </flux:field>
                        @endcan

                        <flux:textarea wire:model="memberForm.deduction_reason"
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
                            >{{ __('members.') }}
                                <flux:icon.check-circle variant="mini"/>
                            </flux:badge>
                        @else
                            @if($feeStatus)
                                <flux:badge color="lime"
                                            size="lg"
                                >{{ __('members.show.fee_msg.paid') }}: <span class="mx-1.5 text-sm">EUR</span> {{$openFees}}
                                    <flux:icon.check-circle variant="mini"/>
                                </flux:badge>
                            @else
                                <flux:badge color="orange">{{ __('members.show.fee_msg.paid') }}: <span class="mx-1.5 text-sm">EUR</span> {{$openFees}}
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
                        @can('update', $member)
                            <flux:field>
                                @if($memberForm->user_id)
                                    <flux:label>verknüft mit Benutzer</flux:label>
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
                                                     wire:model="memberForm.newUser"
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

                        @else

                            <flux:field>
                                <flux:label>{{ __('members.linked_user') }}</flux:label>
                                <flux:badge size="lg"
                                            color="lime"
                                > {{ $linked_user_name }}</flux:badge>
                            </flux:field>
                        @endcan

                    </flux:field>

                </flux:card>


            </section>
        </flux:tab.panel>

        <flux:tab.panel name="member-show-billing">
            <flux:card class="space-y-6">

                <nav class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-payment">
                        <flux:button variant="primary"
                                     size="sm"
                        >Neue Zahlung erfassen
                        </flux:button>
                    </flux:modal.trigger>


                    <flux:input clearable
                                wire:model.live="searchPayment"
                                size="sm"
                                placeholder="Suche"
                    />
                </nav>

                <flux:subheading>Getätigte Zahlungen</flux:subheading>

                <flux:table :paginate="$this->payments">
                    <flux:table.columns>
                        <flux:table.column>Text</flux:table.column>
                        <flux:table.column sortable
                                           :sorted="$sortBy === 'transaction.date'"
                                           :direction="$sortDirection"
                                           wire:click="sort('date')"
                                           class="hidden md:table-cell"
                        >Datum
                        </flux:table.column>
                        <flux:table.column sortable
                                           :sorted="$sortBy === 'transaction.status'"
                                           :direction="$sortDirection"
                                           wire:click="sort('status')"
                                           align="right"
                                           class="hidden lg:table-cell"
                        >Betrag
                        </flux:table.column>
                        <flux:table.column class="hidden md:table-cell"
                        >Belege
                        </flux:table.column>
                        <flux:table.column sortable
                                           :sorted="$sortBy === 'transaction.amount'"
                                           :direction="$sortDirection"
                                           wire:click="sort('amount')"
                                           class="hidden md:table-cell"
                        >Status
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
                                    <flux:badge color="{{ \App\Enums\TransactionStatus::color($payment->transaction->status) }}">{{ $payment->transaction->status }}</flux:badge>

                                </flux:table.cell>

                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

            </flux:card>
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
                Nutzer löschen!
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
