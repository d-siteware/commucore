<div>

    <flux:heading size="xl">{{ __('minutes.index.page_title') }}</flux:heading>

    <section class="grid grid-cols-1 gap-3 lg:grid-cols-3 lg:gap-6">
        <div class="col-span-1">
            @can('create',\App\Models\Protocols\Minutes\MeetingMinute::class)
                <section class="my-3 lg:my-6">
                    <flux:input.group>
                        <flux:input placeholder="{{ __('common.search') }}"
                                    wire:model.live.debounce="search"
                                    clearable
                        />
                        <flux:button variant="primary"
                                     href="{{ route('minutes.create') }}"
                                     icon-trailing="plus"
                        >{{ __('minutes.index.btn.create') }}</flux:button>
                    </flux:input.group>
                </section>
            @elsecan('viewAny',\App\Models\Protocols\Minutes\MeetingMinute::class)
                <flux:input placeholder="{{ __('common.search') }}"
                            wire:model.live.debounce="search"
                            clearable
                />
            @endcan


            <flux:table :paginate="$this->minutes">
                <flux:table.columns>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'title'"
                                       :direction="$sortDirection"
                                       wire:click="sort('title')"
                    >{{ __('minutes.index.table.header_title') }}
                    </flux:table.column>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'date'"
                                       :direction="$sortDirection"
                                       wire:click="sort('meeting_date')"
                    >{{ __('minutes.index.table.header_date') }}
                    </flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->minutes as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell class="flex items-center gap-3">
                                {{ $item->title }}
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">{{ $item->meeting_date->isoFormat('LL') }}</flux:table.cell>
                            <flux:table.cell>

                                <flux:dropdown position="bottom"
                                               align="end"
                                >
                                    <flux:button variant="ghost"
                                                 size="sm"
                                                 icon="ellipsis-horizontal"
                                                 inset="top bottom"
                                    />
                                    <flux:navmenu>
                                        <flux:navmenu.item wire:click="fetchMeetingMinutes({{ $item->id }})"
                                                           icon="information-circle"
                                        >{{ __('minutes.index.table.row.view') }}
                                        </flux:navmenu.item>
                                        <flux:navmenu.item wire:click="editMeetingMinutes({{ $item->id }})"
                                                           icon="pencil-square"
                                        >{{ __('minutes.index.table.row.edit') }}
                                        </flux:navmenu.item>
                                        <flux:navmenu.item wire:click="printMeetingMinutes({{ $item->id }})"
                                                           icon="printer"
                                        >{{ __('minutes.index.table.row.print') }}
                                        </flux:navmenu.item>
                                    </flux:navmenu>
                                </flux:dropdown>


                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach    </flux:table.rows>
            </flux:table>
        </div>

        <aside class="col-span-2">
            <flux:heading size="lg">{{ __('minutes.index.details.heading') }}</flux:heading>
            <x-meeting-minute-details :meeting-minute="$selectedMeeting"
                                      class="mt-6"
            />
        </aside>
    </section>


</div>
