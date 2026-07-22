<div class="space-y-6">


    <flux:heading size="xl">{{ __('event.page.title') }}</flux:heading>

    <nav class="flex flex-wrap lg:flex-nowrap gap-3 items-center">

        <flux:input size="sm"
                    wire:model.live.debounce="search"
                    clearable
                    icon="magnifying-glass"
                    placeholder="{{ __('members.index.search-placeholder') }}"
        />

        @can('create',\App\Models\Event\Event::class)
            <flux:button href="{{ route('backend.events.create') }}"
                         variant="primary"
                         icon="plus"
                         size="sm"
            ><span class="hidden lg:inline">{{ __('event.index.btn.start_new') }}</span>
            </flux:button>
        @endcan

        <flux:modal.trigger name="generate-program-list">
            <flux:button size="sm"
                         icon="printer"
            ><span class="hidden lg:inline">{{ __('event.program_letter.modal.btn') }}</span></flux:button>
        </flux:modal.trigger>

        <flux:select variant="listbox"
                     multiple
                     placeholder="{{ __('event.status.label') }}"
                     size="sm"
                     wire:model.live="filteredBy"
                     selected-suffix="{{ __('app.filter.selected') }}"
                     class="flex-1 lg:flex lg:shrink-2"
        >
            @foreach(App\Enums\EventStatus::options() as $value => $label)
                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select variant="listbox"
                     multiple
                     placeholder="{{ __('event.type.label') }}"
                     size="sm"
                     wire:model.live="typeFilteredBy"
                     selected-suffix="{{ __('app.filter.selected') }}"
                     class="flex-1 lg:flex lg:shrink-2"
        >
            @foreach(App\Enums\EventType::options() as $value => $label)
                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

    </nav>

    <flux:table :paginate="$this->events">
        <flux:table.columns>
            <flux:table.column sortable
                               :sorted="$sortBy === 'name'"
                               :direction="$sortDirection"
                               wire:click="sort('name')"
            >{{ __('event.index.table.header.name') }}</flux:table.column>
            <flux:table.column class="hidden sm:table-cell"
            >{{ __('event.index.table.header.image') }}</flux:table.column>
            <flux:table.column class="hidden sm:table-cell"
            >{{ __('event.index.table.header.subscriptions') }}</flux:table.column>
            <flux:table.column class="hidden md:table-cell"
            >{{ __('event.type.label') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'date'"
                               :direction="$sortDirection"
                               wire:click="sort('event_date')"
            >{{ __('event.date')}}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'starts_at'"
                               :direction="$sortDirection"
                               wire:click="sort('starts_at')"
                               class="hidden 2xl:table-cell"
            >{{ __('event.begins')}}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'ends_at'"
                               :direction="$sortDirection"
                               wire:click="sort('ends_at')"
                               class="hidden 2xl:table-cell"
            >{{ __('event.ends')  }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'venue'"
                               :direction="$sortDirection"
                               wire:click="sort('venue_id')"
                               class="hidden lg:table-cell"
            >{{ __('event.venue') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'status'"
                               :direction="$sortDirection"
                               wire:click="sort('status')"
            >{{ __('event.status.label') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->events as $event)
                <flux:table.row :key="$event->id">
                    <flux:table.cell variant="strong">
                        <a class="underline text-emerald-600"
                           href="{{ route('backend.events.show',$event) }}"
                        >{{ \Illuminate\Support\Str::limit($event->name??'öffnen', 45, preserveWords: true)  }}</a>
                    </flux:table.cell>
                    <flux:table.cell class="hidden sm:table-cell">
                        @if($event->image)
                            <flux:icon icon="photo"
                                       class="size-6 ml-3"
                            />
                        @else
                            <flux:icon icon="x-mark"
                                       class="size-4 ml-3"
                            />
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="hidden sm:table-cell">
                        <flux:badge color="{{ $event->subscriptions->count() > 0 ? 'lime' : 'grey' }}"
                                    size="sm"
                        > {{ $event->subscriptions->count() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="hidden md:table-cell">
                        <flux:badge size="sm" color="teal">{{ $event->type->label() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="hidden sm:table-cell"> {{ optional($event->event_date)->isoFormat('LL') }}</span>
                        <span class="table-cell sm:hidden"> {{ optional($event->event_date)->isoFormat('DD MMM') }}</span>
                    </flux:table.cell>

                    <flux:table.cell class="hidden 2xl:table-cell">
                        {{ optional($event->start_time)->format('H:i') }}
                    </flux:table.cell>

                    <flux:table.cell class=" hidden 2xl:table-cell">
                        {{optional($event->end_time)->format('H:i')}}
                    </flux:table.cell>

                    <flux:table.cell class=" hidden lg:table-cell"
                                     variant="strong"
                    >
                        {{ optional( $event->venue)->name }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $event->status->color() }}"
                        >{{ $event->status->label() }}</flux:badge>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>


    <flux:modal name="generate-program-list"
                class="md:w-96"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('event.program_letter.modal.heading') }}</flux:heading>
                <flux:text class="mt-2">{{ __('event.program_letter.modal.text') }}</flux:text>
            </div>
            <flux:radio.group label="Filter" wire:model="programmFilter">
                <flux:radio
                    name="program-filter"
                    value="year"
                    label="{{ __('event.program_letter.modal.radio.year.label') }}"
                    description="{{ __('event.program_letter.modal.radio.year.desc') }}"
                    checked
                />
                <flux:radio
                    name="program-filter"
                    value="upcoming"
                    label="{{ __('event.program_letter.modal.radio.upcoming.label') }}"
                    description="{{ __('event.program_letter.modal.radio.upcoming.desc') }}"
                />
                <flux:radio
                    name="program-filter"
                    value="all"
                    label="{{ __('event.program_letter.modal.radio.all.label') }}"
                    description="{{ __('event.program_letter.modal.radio.all.desc') }}"
                />
            </flux:radio.group>
            <div class="flex">
                <flux:spacer/>
                <flux:button variant="primary" wire:click="generateEventList"
                >{{ __('event.program_letter.modal.btn') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

