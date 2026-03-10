<div>
    <flux:card class="space-y-6">
        @if($numApplicants>0)
            <flux:heading size="lg">{{ __('members.widgets.applicants.title') }}
                <flux:badge color="lime"
                            size="sm"
                >{{ $this->numApplicants }}</flux:badge>
            </flux:heading>
            <aside class="flex items-center gap-3">
                <flux:input icon="magnifying-glass"
                            placeholder="{{ __('members.widgets.applicants.search.label') }}"
                            class="flex-1"
                            size="sm"
                            wire:model.live="search"
                />
                <flux:dropdown x-show="$wire.selectedApplicants.length > 0"
                               x-cloak
                >
                    <flux:button icon="ellipsis-vertical"
                                 size="sm"
                    >{{__('members.widgets.applicants.options.label')}}
                    </flux:button>
                    <flux:menu>
                        <flux:menu.item icon="pencil" wire:click="editSelectedApplicants">{{__('members.widgets.applicants.options.edit.btn.label')}}</flux:menu.item>
                        <flux:menu.item variant="danger"
                                        wire:confirm="{{__('members.widgets.applicants.options.deletion.confirm')}}"
                                        icon="trash"
                                        wire:click="deleteSelectedApplicants"
                        >{{__('members.widgets.applicants.options.deletion.btn.label')}}
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </aside>

            @if($this->applicants->total()>0 )
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
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->applicants as $applicant)
                            <flux:table.row :key="$applicant->id">
                                <flux:table.cell>
                                    <flux:checkbox :value="$applicant->id"
                                                   wire:model="selectedApplicants"
                                    />
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap">
                                    {{ $applicant->name }}
                                </flux:table.cell>

                                <flux:table.cell>{{ $applicant->applied_at->diffForHumans() }}</flux:table.cell>

                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:text class="my-36 justify-center flex gap-3">{{ __('members.widgets.applicants.empty_search') }}</flux:text>
            @endif
        @else
            <flux:heading size="lg"
                          class="my-9 justify-center flex gap-3 text-zinc-600"
            >{{ __('members.widgets.applicants.empty_list') }}</flux:heading>
        @endif
    </flux:card>

</div>
