<div class="space-y-6">

    <flux:heading size="xl">{{ __('members.title') }}</flux:heading>
    <flux:text>{{ __('members.header') }}</flux:text>

    <nav class="flex flex-col-reverse lg:flex-row space-x-2 lg:items-center ">

        <flux:input size="sm"
                    wire:model.live.debounce="search"
                    clearable
                    icon="magnifying-glass"
                    placeholder="{{ __('members.index.search-placeholder') }}"
                    class="basis-full lg:w-64 lg:inline-flex"
        />


        <aside class="flex space-x-2 lg:items-start mb-3 lg:mb-0">
            <flux:dropdown class="shrink">
                <flux:button icon:trailing="adjustments-horizontal"
                             size="sm"
                >
                    <span class="lg:hidden">Nach Status filtern</span>
                </flux:button>
                <flux:menu>
                    @foreach(\App\Enums\MemberType::cases() as $type)
                        <flux:menu.checkbox wire:model.live="filteredBy"
                                            value="{{ $type->value }}"
                                            label="{{ \App\Enums\MemberType::value($type->value) }}"
                        />
                    @endforeach
                    <flux:menu.checkbox value="in_active"
                                        label="{{ __('members.status.showInactive') }}"
                                        wire:model.live="showInactive"
                    />
                </flux:menu>
            </flux:dropdown>

            @can('create',App\Models\Membership\Member::class)
                <flux:button href="{{ route('backend.members.create') }}"
                             size="sm"
                             variant="primary"
                             class="lg:self-center block"
                >
                    <flux:icon.user-plus class="size-4"/>
                    <span>{{ __('members.btn.addMember') }}</span>
                </flux:button>
            @endcan
        </aside>


    </nav>
    <flux:table :paginate="$this->members">
        <flux:table.columns>
            <flux:table.column sortable
                               :sorted="$sortBy === 'name'"
                               :direction="$sortDirection"
                               wire:click="sort('name')"
            >{{ __('members.table.header.name') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'mobile'"
                               :direction="$sortDirection"
                               wire:click="sort('mobile')"
                               class="hidden sm:table-cell"
            >{{ __('members.table.header.phone') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'status'"
                               :direction="$sortDirection"
                               wire:click="sort('status')"
                               class="hidden sm:table-cell"
            >{{ __('members.table.header.status') }}</flux:table.column>
            <flux:table.column class="hidden sm:table-cell"
            >{{ __('members.table.header.fee_status') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'birthdate'"
                               :direction="$sortDirection"
                               wire:click="sort('birth_date')"
                               class="hidden sm:table-cell"
            >{{ __('members.table.header.birthday') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->members as $member)

                @php
                    $fee_status = $member->feeStatus();
                $color = $fee_status['status'] ? 'lime' : 'orange';
                $paid = $member->fee_type === \App\Enums\MemberFeeType::FREE->value ? __('members.fee-type.free') :  $fee_status['paid'];
                @endphp
                <flux:table.row :key="$member->id">
                    <flux:table.cell class="flex items-center gap-3">

                        <section class=" hidden sm:table-cell ">
                            <span @class(['line-through' => $member->left_at !== null ])>
                                 {{ $member->first_name }}
                                {{ $member->name }}
                            </span>
                        </section>
                        <section class="sm:hidden flex flex-col gap-2 ">
                            <flux:heading size="lg">
                                <span @class(['line-through text-neutral-400' => $member->left_at !== null ])>
                                 {{ $member->first_name }}
                                    {{ $member->name }}
                            </span>
                            </flux:heading>
                            <div class="flex justify-start items-center gap-2">
                                <flux:badge size="sm"
                                            :color="\App\Enums\MemberType::color($member->type)"
                                            inset="top bottom"
                                >{{ \App\Enums\MemberType::value($member->type) }}</flux:badge>

                                <flux:badge size="sm"
                                            color="{{ $color }}"
                                            inset="top bottom"
                                >{{ $paid }}</flux:badge>
                                @if($member->left_at !== null)
                                    <flux:badge color="grey"
                                                size="sm"
                                    >{{ __('members.status.inactive') }} / {{ $member->left_at->isoFormat('LL') }}</flux:badge>
                                @endif
                            </div>
                            @if(!empty($member->mobile))
                                <p class="text-xs">
                                    <flux:button size="xs"
                                                 href="tel:{{ $member->mobile }}"
                                                 icon="phone"
                                                 variant="filled"
                                    >{{ $member->mobile }}</flux:button>
                                </p>
                            @endif

                        </section>
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap hidden sm:table-cell">{{ $member->mobile }}</flux:table.cell>
                    <flux:table.cell class=" hidden sm:table-cell">
                        @if($member->left_at !== null)
                            <flux:badge color="grey"
                                        size="sm"
                            >{{ __('members.status.inactive') }} / {{ $member->left_at->isoFormat('LL') }}</flux:badge>
                        @else
                            <flux:badge size="sm"
                                        :color="\App\Enums\MemberType::color($member->type)"
                                        inset="top bottom"
                            >{{ \App\Enums\MemberType::value($member->type) }}</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class=" hidden sm:table-cell">

                        <flux:badge size="sm"
                                    color="{{ $color }}"
                                    inset="top bottom"
                        >{{ $paid }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class=" hidden sm:table-cell">
                        {{ optional($member->birth_date)->format('Y-m-d') }}
                    </flux:table.cell>

                    @can('view', $member)
                        <flux:table.cell>
                            <flux:dropdown :key="$member->id">
                                <flux:button variant="ghost"
                                             size="sm"
                                             icon="ellipsis-horizontal"
                                             inset="top bottom"
                                ></flux:button>

                                <flux:menu>
                                    <flux:menu.item href="{{ route('backend.members.show',$member) }}"
                                                    icon="pencil"
                                    >{{ __('members.con.men.edit') }}</flux:menu.item>
                                    <flux:menu.item icon="currency-euro">{{ __('members.con.men.payment') }}</flux:menu.item>
                                    <flux:menu.item icon="trash">{{ __('members.con.men.delete') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    @endcan
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
