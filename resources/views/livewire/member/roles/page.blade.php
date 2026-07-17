<div class="space-y-6">

    <div>
        <flux:heading size="xl">{{ __('role.page.title', ['name' => setting('organization.name')]) }}</flux:heading>
        <flux:text>{{ __('role.page.subtitle') }}</flux:text>
    </div>

    <div class="grid lg:grid-cols-3 gap-3 lg:gap-6">

        {{-- Leitungsteam (zugeordnete Führungspositionen) --}}
        <flux:card class="lg:col-span-2 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <flux:heading size="lg">{{ __('role.leadership.heading') }}</flux:heading>
                    <flux:badge size="sm">{{ $this->leadershipRoster->total() }}</flux:badge>
                </div>
                @can('create', \App\Models\Membership\MemberRole::class)
                    <flux:button variant="primary"
                                 size="sm"
                                 icon="plus"
                                 wire:click="attachMemberRole"
                    >{{ __('role.leadership.btn_add') }}</flux:button>
                @endcan
            </div>

            <flux:separator/>

            @if($this->leadershipRoster->count() > 0)
                <ul role="list" class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($this->leadershipRoster as $leader)
                        <x-leader-card :$leader/>
                    @endforeach
                </ul>

                {{ $this->leadershipRoster->links() }}
            @else
                <div class="flex flex-col items-center justify-center gap-3 py-12 text-center">
                    <flux:icon.user-group class="size-10 text-zinc-300 dark:text-zinc-600"/>
                    <flux:text>{{ __('role.leadership.empty_roster') }}</flux:text>
                    @can('create', \App\Models\Membership\MemberRole::class)
                        <flux:button variant="primary"
                                     size="sm"
                                     icon="plus"
                                     wire:click="attachMemberRole"
                        >{{ __('role.leadership.btn_add') }}</flux:button>
                    @endcan
                </div>
            @endif
        </flux:card>

        {{-- Rollen --}}
        <flux:card class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <flux:heading size="lg">{{ __('role.page.heading') }}</flux:heading>
                @can('create', \App\Models\Membership\Role::class)
                    <flux:button size="sm"
                                 icon="plus"
                                 wire:click="addRole"
                    >{{ __('role.create.form.btn_add_new_role.label') }}</flux:button>
                @endcan
            </div>

            <flux:separator/>

            @if($this->roles->count() > 0)
                <section x-sort="$wire.sortItem($item, $position)" class="space-y-3">
                    @foreach($this->roles as $role)
                        <x-role-card :$role
                                     x-sort:item="{{ $role->id }}"
                                     wire:key="{{ $role->id }}"
                        />
                    @endforeach
                </section>

                {{ $this->roles->links() }}
            @else
                <div class="flex flex-col items-center justify-center gap-3 py-12 text-center">
                    <flux:icon.rectangle-stack class="size-10 text-zinc-300 dark:text-zinc-600"/>
                    <flux:text>{{ __('role.leadership.empty_roles_list') }}</flux:text>
                    @can('create', \App\Models\Membership\Role::class)
                        <flux:button size="sm"
                                     icon="plus"
                                     wire:click="addRole"
                        >{{ __('role.create.form.btn_add_new_role.label') }}</flux:button>
                    @endcan
                </div>
            @endif
        </flux:card>
    </div>


    @can('create', \App\Models\Membership\MemberRole::class)
        <flux:modal name="add-member-to-leaderboard"
                    variant="flyout"
                    position="right"
        >

            <flux:heading size="lg">{{ __('role.create.form.title') }}</flux:heading>


            <form wire:submit.prevent="saveMemberRole">
                <section class="space-y-6 mb-6">

                    <flux:field>
                        <flux:label>{{ __('role.create.form.select_member.label') }}</flux:label>
                        <flux:select wire:model="memberRoleForm.member_id"
                                     placeholder="{{ __('role.create.form.select_member.label') }}"
                                     variant="listbox"
                                     searchable
                        >

                            @foreach ($this->members() as $member)
                                <flux:select.option value="{{ $member->id }}">{{ $member->fullName() }}</flux:select.option>
                            @endforeach

                        </flux:select>
                        <flux:error name="memberRoleForm.member_id"/>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('role.create.form.select_role.label') }}</flux:label>
                        <flux:button.group>
                            <flux:select wire:model="memberRoleForm.role_id"
                                         placeholder="{{ __('role.create.form.select_role.label') }}"
                            >
                                <flux:select.option value="null">{{ __('role.create.form.option_select_role') }}</flux:select.option>

                                @foreach ($this->roles() as $role)
                                    <flux:select.option value="{{ $role->id }}">{{ $role->name[ app()->getLocale() ] }}</flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:modal.trigger name="make-new-role">
                                <flux:button>{{ __('role.create.form.btn_add_new_role.label') }}</flux:button>
                            </flux:modal.trigger>
                        </flux:button.group>
                        <flux:error name="memberRoleForm.role_id"/>
                    </flux:field>


                    <flux:field>
                        <flux:label>{{ __('role.create.form.designated_at') }}</flux:label>
                        <flux:date-picker locale="{{ app()->getLocale() }}"
                                          wire:model.blur="memberRoleForm.designated_at"
                                          placeholder="{{ __('role.create.form.designated_at.placeholder') }}"
                        />
                        <flux:error name="memberRoleForm.designated_at"/>
                    </flux:field>

                    <flux:separator text="{{ __('role.create.form.section_profile') }}"
                                    class="my-4"
                    />

                    @isMultiLang
                        <flux:tab.group>
                            <flux:tabs>
                                @foreach(\App\Models\Locale::getNames() as $locale)
                                    <flux:tab name="about-tab-{{ $locale }}">{{ $locale }}</flux:tab>
                                @endforeach
                            </flux:tabs>
                            @foreach(\App\Models\Locale::getNames() as $locale)
                                <flux:tab.panel name="about-tab-{{ $locale }}">
                                    <flux:textarea label="{{ __('role.create.form.about_me') }}"
                                                   badge="{{ $locale }}"
                                                   wire:model.blur="memberRoleForm.about_me.{{ $locale }}"
                                    ></flux:textarea>
                                </flux:tab.panel>
                            @endforeach
                        </flux:tab.group>
                    @else
                        <flux:textarea label="{{ __('role.create.form.about_me') }}"
                                       wire:model.blur="memberRoleForm.about_me.{{ \App\Models\Locale::getNames()[0] }}"
                        ></flux:textarea>
                    @endIsMultiLang


                    @if($memberRoleForm->profile_image && is_string($memberRoleForm->profile_image))
                        <div>
                            <img src="{{ Storage::url($memberRoleForm->profile_image) }}"
                                 alt="Current Profile Image"
                                 class="w-32 h-32 object-cover mb-2"
                            >
                            <flux:button size="xs"
                                         variant="danger"
                                         wire:click="deleteProfileImage"
                            >
                                <flux:icon.trash variant="micro"/>
                            </flux:button>
                        </div>
                    @endif

                    <flux:field>
                        <flux:label>{{ __('role.create.form.profile_image') }}</flux:label>
                        <flux:input type="file"
                                    accept=".jpeg,.jpg,.webp,.png"
                                    wire:model.defer="memberRoleForm.profile_image"
                        />
                        <flux:error name="memberRoleForm.profile_image"/>
                    </flux:field>
                </section>

                <div class="flex gap-2">
                    <flux:spacer/>
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('common.cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary"
                                 type="submit"
                    >@if(isset($memberRoleForm->id))
                            {{ __('role.create.form.btn_update_member') }}
                        @else
                            {{ __('role.create.form.btn_add_member') }}
                        @endif
                    </flux:button>
                </div>
            </form>

        </flux:modal>

        <flux:modal name="make-new-role"
                    class="w-1/2 space-y-6"
        >

            @if($this->roleForm->id !== null)

                <flux:heading size="lg">{{ __('role.create.modal.title_edit') }}</flux:heading>
            @else
                <flux:heading size="lg">{{ __('role.create.modal.title') }}</flux:heading>
            @endif
            <form wire:submit="storeRole"
                  class="space-y-6"
            >

                @isMultiLang
                    @foreach(\App\Models\Locale::getNames() as $locale)
                        <flux:input wire:model.blur="roleForm.name.{{ $locale }}"
                                    label="{{ __('role.create.modal.name') }}"
                                    badge="{{ $locale }}"
                        />
                    @endforeach
                @else
                    <flux:input wire:model.blur="roleForm.name.{{ \App\Models\Locale::getNames()[0] }}"
                                label="{{ __('role.create.modal.name') }}"
                    />
                @endIsMultiLang

                <flux:input wire:model.blur="roleForm.description"
                            label="{{ __('role.create.modal.description') }}"
                />

                <flux:checkbox wire:model="roleForm.can_manage_accounting"
                               label="{{ __('role.create.modal.can_manage_accounting') }}"
                />

                <flux:checkbox wire:model="roleForm.can_audit_accounting"
                               label="{{ __('role.create.modal.can_audit_accounting') }}"
                />


                <flux:callout icon="shield-exclamation"
                              color="amber"
                >
                    <flux:callout.heading>{{ __('role.create.modal.callout_heading') }}</flux:callout.heading>

                    <flux:callout.text>
                        {{ __('role.create.modal.callout_text') }}
                    </flux:callout.text>
                    <flux:checkbox wire:model="roleForm.can_represent_organization"
                                   label="{{ __('role.create.modal.can_represent_organization') }}"
                    />
                </flux:callout>

                <flux:input wire:model.blur="roleForm.sort"
                            label="{{ __('role.create.modal.sort') }}"
                />

                <div class="flex gap-2">
                    <flux:spacer/>
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('common.cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary"
                                 type="submit"
                    >{{ __('role.create.modal.button') }}
                    </flux:button>
                </div>

            </form>

        </flux:modal>
    @endcan

</div>
