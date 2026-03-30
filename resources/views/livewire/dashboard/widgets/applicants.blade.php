<div>
    <flux:card class="space-y-6">
        @if($numApplicants>0)
            <flux:heading size="lg">{{ __('members.widgets.applicants.title') }}
                <flux:badge color="lime" size="sm">{{ $this->numApplicants }}</flux:badge>
            </flux:heading>

            <aside class="flex items-center gap-3">
                <flux:input icon="magnifying-glass"
                            placeholder="{{ __('members.widgets.applicants.search.label') }}"
                            class="flex-1"
                            size="sm"
                            wire:model.live="search"
                />
                <flux:dropdown x-show="$wire.selectedApplicants.length > 0" x-cloak>
                    <flux:button icon="ellipsis-vertical" size="sm">
                        {{ __('members.widgets.applicants.options.label') }}
                    </flux:button>
                    <flux:menu>
                        <flux:menu.item variant="danger"
                                        wire:confirm="{{ __('members.widgets.applicants.options.deletion.confirm') }}"
                                        icon="trash"
                                        wire:click="deleteSelectedApplicants"
                        >{{ __('members.widgets.applicants.options.deletion.btn.label') }}
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </aside>

            @if($this->applicants->total() > 0)
                <flux:table :paginate="$this->applicants">
                    <flux:table.columns>
                        <flux:table.column class="w-1/6">
                            <x-selectAllApplicants/>
                        </flux:table.column>
                        <flux:table.column sortable
                                           :sorted="$sortBy === 'name'"
                                           :direction="$sortDirection"
                                           wire:click="sort('name')"
                        >{{ __('members.widgets.applicants.tab.header.name') }}
                        </flux:table.column>
                        <flux:table.column sortable
                                           :sorted="$sortBy === 'applied_at'"
                                           :direction="$sortDirection"
                                           wire:click="sort('applied_at')"
                        >{{ __('members.widgets.applicants.tab.header.from') }}
                        </flux:table.column>
                        <flux:table.column class="w-12"></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->applicants as $applicant)
                            <flux:table.row :key="$applicant->id">
                                <flux:table.cell>
                                    <flux:checkbox :value="$applicant->id" wire:model="selectedApplicants"/>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap">
                                    {{ $applicant->name }}{{ $applicant->first_name ? ', '.$applicant->first_name : '' }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $applicant->applied_at->diffForHumans() }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button size="xs"
                                                 icon="pencil"
                                                 variant="ghost"
                                                 wire:click="openDetailModal({{ $applicant->id }})"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:text class="my-36 justify-center flex gap-3">
                    {{ __('members.widgets.applicants.empty_search') }}
                </flux:text>
            @endif
        @else
            <flux:heading size="lg" class="my-9 justify-center flex gap-3 text-zinc-600">
                {{ __('members.widgets.applicants.empty_list') }}
            </flux:heading>
        @endif
    </flux:card>

    {{-- ── Detail Modal ─────────────────────────────────────────────────────── --}}
    <flux:modal wire:model="showDetailModal" class="w-full max-w-lg">
        @if($this->activeApplicant)
                <flux:heading size="lg">
                    {{ $this->activeApplicant->name }}{{ $this->activeApplicant->first_name ? ', '.$this->activeApplicant->first_name : '' }}
                </flux:heading>
                <flux:subheading>
                    {{ __('members.widgets.applicants.modal.fields.applied_at', ['date' => $this->activeApplicant->applied_at->isoFormat('LL')]) }}
                </flux:subheading>

            <div class="space-y-4 py-4">
                {{-- Persönliche Daten --}}
                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    @if($this->activeApplicant->email)
                        <div>
                            <flux:text class="text-xs text-zinc-500">{{ __('members.widgets.applicants.modal.fields.email') }}</flux:text>
                            <p class="font-medium">{{ $this->activeApplicant->email }}</p>
                        </div>
                    @endif

                    @if($this->activeApplicant->birth_date)
                        <div>
                            <flux:text class="text-xs text-zinc-500">{{ __('members.widgets.applicants.modal.fields.birth_date') }}</flux:text>
                            <p class="font-medium">{{ $this->activeApplicant->birth_date->isoFormat('LL') }}</p>
                        </div>
                    @endif

                    @if($this->activeApplicant->phone || $this->activeApplicant->mobile)
                        <div>
                            <flux:text class="text-xs text-zinc-500">{{ __('members.widgets.applicants.modal.fields.phone') }}</flux:text>
                            <p class="font-medium">{{ $this->activeApplicant->phone ?? $this->activeApplicant->mobile }}</p>
                        </div>
                    @endif

                    @if($this->activeApplicant->address)
                        <div>
                            <flux:text class="text-xs text-zinc-500">{{ __('members.widgets.applicants.modal.fields.address') }}</flux:text>
                            <p class="font-medium">
                                {{ $this->activeApplicant->address }}<br>
                                {{ $this->activeApplicant->zip }} {{ $this->activeApplicant->city }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Einwilligungen --}}
                <flux:separator/>
                <div class="flex gap-4 text-sm">
                    <div class="flex items-center gap-1.5">
                        @if($this->activeApplicant->gdpr_consent_at)
                            <flux:icon.check-circle class="size-4 text-green-500"/>
                        @else
                            <flux:icon.x-circle class="size-4 text-zinc-400"/>
                        @endif
                        <span>{{ __('members.widgets.applicants.modal.fields.gdpr') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        @if($this->activeApplicant->newsletter_consent_at)
                            <flux:icon.check-circle class="size-4 text-green-500"/>
                        @else
                            <flux:icon.x-circle class="size-4 text-zinc-400"/>
                        @endif
                        <span>{{ __('members.widgets.applicants.modal.fields.newsletter') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        @if($this->activeApplicant->photo_consent_at)
                            <flux:icon.check-circle class="size-4 text-green-500"/>
                        @else
                            <flux:icon.x-circle class="size-4 text-zinc-400"/>
                        @endif
                        <span>{{ __('members.widgets.applicants.modal.fields.photo_consent') }}</span>
                    </div>
                </div>
            </div>

            <aside>
                <div class="flex justify-between w-full">
                    <flux:button variant="ghost" wire:click="closeDetailModal">
                        {{ __('members.widgets.applicants.modal.btn.cancel') }}
                    </flux:button>
                    <div class="flex gap-2">
                        <flux:button variant="danger" wire:click="openRejectModal">
                            {{ __('members.widgets.applicants.modal.btn.reject') }}
                        </flux:button>
                        <flux:button variant="primary" wire:click="acceptApplicant" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="acceptApplicant">
                                {{ __('members.widgets.applicants.modal.btn.accept') }}
                            </span>
                            <span wire:loading wire:target="acceptApplicant">
                                {{ __('actions.processing') }}
                            </span>
                        </flux:button>
                    </div>
                </div>
            </aside>
        @endif
    </flux:modal>

    {{-- ── Reject Modal ─────────────────────────────────────────────────────── --}}
    <flux:modal wire:model="showRejectModal" class="w-full max-w-md">

            <flux:heading size="lg">{{ __('members.widgets.applicants.modal.reject.title') }}</flux:heading>
            <flux:subheading>{{ __('members.widgets.applicants.modal.reject.subtitle') }}</flux:subheading>

        <div class="py-4">
            <flux:textarea
                    wire:model="rejectionReason"
                    :label="__('members.widgets.applicants.modal.reject.reason_label')"
                    :placeholder="__('members.widgets.applicants.modal.reject.reason_placeholder')"
                    rows="4"
            />
        </div>

        <aside class="mt-3">
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="closeRejectModal">
                    {{ __('members.widgets.applicants.modal.btn.cancel') }}
                </flux:button>
                <flux:button variant="danger"
                             wire:click="rejectApplicant"
                             wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="rejectApplicant">
                        {{ __('members.widgets.applicants.modal.reject.confirm_btn') }}
                    </span>
                    <span wire:loading wire:target="rejectApplicant">
                        {{ __('actions.processing') }}
                    </span>
                </flux:button>
            </div>
        </aside>
    </flux:modal>
</div>