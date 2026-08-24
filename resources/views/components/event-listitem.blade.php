<article class="flex justify-between items-center p-2 flex-col lg:flex-row gap-3 lg:gap-9">
    <section class="flex-1 space-y-2 w-full">
        <flux:heading size="lg">{{ $event->title[$locale??'de'] }}</flux:heading>
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
{{--        @if(\Illuminate\Support\Facades\Storage::disk('public')->exists('images/posters/'.$event->id.'_poster_'.$locale.'.jpg'))
            <figure>
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url('images/posters/'.$event->id.'_poster_'.$locale.'.jpg') }}"
                     alt="{{ 'images/posters/'.$event->id.'_poster_'.$locale.'.jpg' }}"
                     class="w-32"
                >
            </figure>
        @endif--}}

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
