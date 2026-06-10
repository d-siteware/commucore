<a href="{{ route('events.show',$event->slug[app()->getLocale()]) }}"
   role="listitem"
   aria-label="{{ $event->title[app()->getLocale()] }}"
>
    <flux:card class="space-y-3 my-3 hover:bg-teal-50 dark:hover:bg-emerald-800 transition duration-500 ">
        @if($event->event_date == today('Europe/Berlin') )
            <flux:badge size="lg" color="lime">{{ __('app.today') }}</flux:badge>
        @endif
        <flux:heading size="lg">{{ $event->title[app()->getLocale()] }}</flux:heading>

        <flux:text size="sm">
            {{ __('event.date') }}: {{ $event->event_date->format('Y-m-d') }} | {{ __('event.begins') }}: {{ $event->start_time->format('H:s') }} | {{ __('event.ends') }}: {{ $event->end_time->format('H:s') }} | {{ __('event.venue') }}: {{ $event->venue?->name ?? '-' }} </flux:text>

    </flux:card>
</a>
