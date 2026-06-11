<!-- resources/views/livewire/event/calendar.blade.php -->
<div class="lg:flex lg:h-full lg:flex-col">
    <!-- Header -->
    <header class="flex items-center justify-between border-b border-gray-200 px-6 py-4 lg:flex-none">
        <h1 class="text-base font-semibold text-gray-900">
            <time datetime="{{ $startOfMonth->format('Y-m') }}">{{ $startOfMonth->isoFormat('MMMM YYYY') }}</time>
        </h1>
        <div class="flex items-center">
            <div class="relative flex items-center rounded-md bg-white shadow-xs md:items-stretch">
                <button type="button" wire:click="previousMonth" class="flex h-9 w-12 items-center justify-center rounded-l-md border-y border-l border-gray-300 pr-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pr-0 md:hover:bg-gray-50" aria-label="{{ __('app.cal.prev_month') }}">
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button" wire:click="goToToday" class="hidden border-y border-gray-300 px-3.5 text-sm font-semibold text-gray-900 hover:bg-gray-50 focus:relative md:block">{{ __('app.today') }}</button>
                <span class="relative -mx-px h-5 w-px bg-gray-300 md:hidden"></span>
                <button type="button" wire:click="nextMonth" class="flex h-9 w-12 items-center justify-center rounded-r-md border-y border-r border-gray-300 pl-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pl-0 md:hover:bg-gray-50" aria-label="{{ __('app.cal.next_month') }}">
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

        </div>
    </header>

    <!-- Calendar Grid -->
    <div class="shadow-sm ring-1 ring-black/5 lg:flex lg:flex-auto lg:flex-col">
        <div class="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-200 text-center text-xs/6 font-semibold text-gray-700 lg:flex-none">
            <div class="flex justify-center bg-white py-2"><span class="inline sm:hidden">{{ __('app.cal.day_short.Mo')}}</span><span class="hidden sm:inline">{{ __('app.cal.day_medium.Mo')}}</span></div>
            <div class="flex justify-center bg-white py-2"><span class="inline sm:hidden">{{ __('app.cal.day_short.Tu')}}</span><span class="hidden sm:inline">{{ __('app.cal.day_medium.Tu')}}</span></div>
            <div class="flex justify-center bg-white py-2"><span class="inline sm:hidden">{{ __('app.cal.day_short.We')}}</span><span class="hidden sm:inline">{{ __('app.cal.day_medium.We')}}</span></div>
            <div class="flex justify-center bg-white py-2"><span class="inline sm:hidden">{{ __('app.cal.day_short.Th')}}</span><span class="hidden sm:inline">{{ __('app.cal.day_medium.Th')}}</span></div>
            <div class="flex justify-center bg-white py-2"><span class="inline sm:hidden">{{ __('app.cal.day_short.Fr')}}</span><span class="hidden sm:inline">{{ __('app.cal.day_medium.Fr')}}</span></div>
            <div class="flex justify-center bg-white py-2"><span class="inline sm:hidden">{{ __('app.cal.day_short.Sa')}}</span><span class="hidden sm:inline">{{ __('app.cal.day_medium.Sa')}}</span></div>
            <div class="flex justify-center bg-white py-2"><span class="inline sm:hidden">{{ __('app.cal.day_short.Su')}}</span><span class="hidden sm:inline">{{ __('app.cal.day_medium.Su')}}</span></div>
        </div>
        <div class="flex bg-gray-200 text-xs/6 text-gray-700 lg:flex-auto">
            <!-- Desktop Grid -->
            <div class="hidden w-full lg:grid lg:grid-cols-7 lg:grid-rows-6 lg:gap-px">
                @foreach ($days as $day)
                    <div class="relative px-3 py-2 min-h-28 {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50 text-gray-500' }}">
                        <time datetime="{{ $day['date']->format('Y-m-d') }}"
                              @if ($day['date']->isToday()) class="flex size-6 items-center justify-center rounded-full bg-emerald-600 font-semibold text-white" aria-current="date" @endif>
                            {{ $day['date']->day }}
                        </time>
                        @php
                            $dayEvents = collect($events)->filter(function ($event) use ($day) {
                                return $event['event_date'] && Carbon\Carbon::parse($event['event_date'])->isSameDay($day['date']);
                            });
                        @endphp
                        @if ($dayEvents->isNotEmpty())
                            <ol class="mt-2">
                                @foreach ($dayEvents as $event)
                                    <li>
                                        <a href="{{ route('events.show',$event['slug']) }}" class="group flex">
                                            <p class="flex-auto truncate font-medium text-gray-900 group-hover:text-emerald-600">{{ $event['title'] }}</p>
                                            @if ($event['start_time'])
                                                <time datetime="{{ $event['event_date'] }}T{{ $event['start_time'] }}" class="ml-3 hidden flex-none text-gray-500 group-hover:text-emerald-600 xl:block">
                                                    {{ Carbon\Carbon::parse($event['start_time'])->format('H:i') }}
                                                </time>
                                            @else
                                                <time datetime="{{ $event['event_date'] }}T16:00:00" class="ml-3 hidden flex-none text-gray-500 group-hover:text-emerald-600 xl:block" aria-label="{{ __('app.cal.set_default_time') }}">
                                                    {{ Carbon\Carbon::parse($event['start_time'])->format('H:i') }}
                                                </time>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                @endforeach
            </div>
            <!-- Mobile Grid -->
            <div class="isolate grid w-full grid-cols-7 grid-rows-6 gap-px lg:hidden">
                @foreach ($days as $day)
                    <button type="button" class="flex h-14 flex-col px-3 py-2 {{ $day['isCurrentMonth'] ? 'bg-white text-gray-900' : 'bg-gray-50 text-gray-500' }} hover:bg-gray-100 focus:z-10 {{ $day['date']->isToday() ? 'font-semibold text-emerald-600' : '' }}">
                        <time datetime="{{ $day['date']->format('Y-m-d') }}"
                              class="ml-auto {{ $day['date']->isToday() ? 'flex size-6 items-center justify-center rounded-full bg-emerald-600 text-white' : '' }}">
                            {{ $day['date']->day }}
                        </time>
                        @php
                            $dayEvents = collect($events)->filter(function ($event) use ($day) {
                                return $event['event_date'] && Carbon\Carbon::parse($event['event_date'])->isSameDay($day['date']);
                            });
                        @endphp
                        <span class="sr-only">{{ __('app.cal.events_count', ['count' => $dayEvents->count()]) }}</span>
                        @if ($dayEvents->isNotEmpty())
                            <span class="-mx-0.5 mt-auto flex flex-wrap-reverse">
                                @foreach ($dayEvents as $event)
                                    <span class="mx-0.5 mb-1 size-2 rounded-full bg-emerald-600"></span>
                                @endforeach
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Mobile Event List -->
    <div class="px-4 py-10 sm:px-6 lg:hidden">
        <ol class="divide-y divide-gray-100 overflow-hidden rounded-lg bg-white text-sm shadow-sm ring-1 ring-black/5">
            @forelse ($events as $event)
                <li class="group flex items-center gap-3 p-4 pr-6 focus-within:bg-gray-50 hover:bg-gray-50">
                    <div class="flex-auto">
                        <flux:heading size="lg" class="prose prose-emerald dark:prose-invert">{{ $event['title'] }}</flux:heading>
                        <time datetime="{{ $event['event_date'] }}T{{ $event['start_time'] }}"
                              class="mt-2 flex items-center text-gray-700"
                        >
                            <svg class="mr-2 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd" />
                            </svg>
                            {{ Carbon\Carbon::parse($event['event_date'])->isoFormat('Do MMMM') }} @if ($event['start_time']) {{ Carbon\Carbon::parse($event['start_time'])->format('H:i') }} @endif
                        </time>
                        @if ($event['excerpt'])
                            <flux:text class="mt-1 prose prose-emerald dark:prose-invert">{!! $event['excerpt'] !!}</flux:text>
                        @endif
                    </div>
                    <flux:button size="sm" variant="primary" href="{{ route('events.show',$event['slug']) }}" >{{ __('app.cal.details') }}<span class="sr-only">, {{ $event['title'] }}</span></flux:button>
                </li>
            @empty
                <li class="p-4 text-gray-500">{{ __('app.cal.empty') }}</li>
            @endforelse
        </ol>
    </div>
</div>
