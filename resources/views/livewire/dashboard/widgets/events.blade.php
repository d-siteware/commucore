<flux:card class="space-y-6 break-inside-avoid">

    <flux:heading size="lg">{{ __('event.page.title') }}</flux:heading>

    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-slate-100 px-4 py-5 shadow-sm sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">{{ \App\Enums\EventStatus::DRAFT->value }}</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $this->draftedEvents() }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-lime-100 px-4 py-5 shadow-sm sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">{{ \App\Enums\EventStatus::PUBLISHED }}</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $this->publishedEvents() }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-blue-100 px-4 py-5 shadow-sm sm:p-6 ">
            <dt class="truncate text-sm font-medium text-gray-500">{{ \App\Enums\EventStatus::PENDING }}</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $this->pendingEvents() }}</dd>
        </div>
    </dl>

    <flux:separator/>

    <flux:heading size="lg">{{ __('event.upcoming.title') }}</flux:heading>
{{--
    @if($showYearSelector)
        <flux:checkbox.group wire:model="yearsList"
                             label="Filter Jahre"
                             variant="pills"
        >
            @foreach($yearsList as $year)
                <flux:checkbox value="{{ $year }}"
                               label="{{ $year }}"
                />
            @endforeach

        </flux:checkbox.group>
    @endif--}}


        <flux:table>
            <flux:table.columns>
                <flux:table.column>Interner Name</flux:table.column>
                <flux:table.column>Datum</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($upcomingEventList as $event)
                    <flux:table.row>

                        <flux:table.cell>
                            <a class="underline underline-offset-2 text-accent"
                               href="{{ route('backend.events.show', $event) }}"
                            >
                                {{ $event->name }}
                            </a>

                        </flux:table.cell>
                        <flux:table.cell>
                            {{$event->event_date->isoFormat('LL')}}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="{{ \App\Enums\EventStatus::color($event->status) }}">{{ \App\Enums\EventStatus::value($event->status) }}</flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
