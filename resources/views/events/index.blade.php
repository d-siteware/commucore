<x-guest-layout title="{{ __('event.page.title') }}">


  <header class="flex justify-between">
      <h1 class="text-3xl">{{ __('event.page.title') }}</h1>

      <flux:button href="{{ route('api.events.feed') }}" icon-trailing="rss" size="sm" variant="ghost" target="_blank">RSS</flux:button>
  </header>

{{--    <livewire:event.calendar />--}}


    @if($todayEvents->count() > 0)
        <div class="bg-teal-50 border border-teal-200 rounded-lg p-3 lg:p-10 my-3 lg:my-10">
            <flux:heading size="xl">{{ __('event.today.title') }}</flux:heading>
            @foreach($todayEvents as $event)
                <article class="flex justify-between items-center p-2 flex-col lg:flex-row gap-3 lg:gap-9">
                    <section class="flex-1 space-y-2 w-full">
                        <header class="flex relative gap-3 items-center">
                              <span class="relative flex size-3">
                              <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-lime-400 opacity-75"></span>
                              <span class="relative inline-flex size-3 rounded-full bg-lime-500"></span>
                            </span>
                            <flux:heading>{{ $event->title[$locale??'de'] }}</flux:heading>
                        </header>
                        <section class="prose-emerald dark:prose-invert">{!! $event->excerpt[$locale??'de'] !!}</section>

                        <nav class="flex gap-3">
                            <flux:button variant="primary"
                                         size="sm"
                                         href="{{ route('events.show', $event->slug[app()->getLocale()]) }}"
                            >{{ __('event.show.label') }}</flux:button>
                            <flux:button variant="filled"
                                         size="sm"
                                         href="{{ route('events.ics',$event->slug[$locale??'de'] ) }}"
                                         icon-trailing="calendar-days"
                                         target="_blank"

                            />
                        </nav>

                    </section>
                    <aside class="space-y-1 flex-row flex justify-between border-t border-zinc-300 lg:border-t-0 pt-2 gap-3">
                          <section class="space-y-1 grow w-52">
                            <div class="flex justify-between items-center gap-3">
                                <span>{{ __('event.date') }}:</span>
                                <span>{{ $event->event_date->locale($locale)->isoFormat('LL') }}</span>
                            </div>
                            <div class="flex justify-between items-center gap-3 text-sm">
                                <span>{{ __('event.begins') }}:</span>
                                <span>{{ $event->start_time->locale($locale)->isoFormat('LT') }}</span>
                            </div>
                            <div class="flex justify-between items-center gap-3 text-sm">
                                <span>{{ __('event.ends') }}:</span>
                                <span>{{ $event->end_time->locale($locale)->isoFormat('LT') }}</span>
                            </div>
                            <div class="flex justify-between items-center gap-3 text-sm">
                                <span>{{ __('event.venue') }}:</span>
                                <div>{{ $event->venue?->name ?? '—' }}</div>
                            </div>

                        </section>

                    </aside>
                </article>
            @endforeach
        </div>

    @endif

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-12">
        <div>
            <flux:heading size="xl" class="my-3 lg:my-12">{{ __('event.upcoming.title') }}</flux:heading>
            <section class="space-y-4 divide-y divide-emerald-600 mt-3 mb-10">
                @foreach($upcomingEvents as $event)
                    <x-event-listitem :event="$event" :locale="$locale" />
                @endforeach
            </section>
        </div>

        <div>
            <flux:heading size="xl" class="my-3 lg:my-12">{{ __('event.recent.title') }}</flux:heading>
            <section class="space-y-4 divide-y divide-emerald-600 mt-3 mb-10">
                @foreach($recentEvents as $event)
                    <x-event-listitem :event="$event" :locale="$locale" />
                @endforeach
            </section>
        </div>

    </div>


    <livewire:app.global.mailinglist.form/>
</x-guest-layout>
