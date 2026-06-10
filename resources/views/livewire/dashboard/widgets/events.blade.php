<flux:card class="break-inside-avoid">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-4">
        <flux:heading size="sm" class="text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-xs font-semibold">
            {{ __('event.page.title') }}
        </flux:heading>
        <flux:button href="{{ route('backend.events.index') }}" variant="ghost" size="sm" icon="arrow-right" />
    </div>

    {{-- Status-Zeile: kompakt horizontal statt große Kacheln --}}
    <div class="flex flex-col lg:flex-row gap-3 mb-4">
        <div class="flex-1 flex items-center gap-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 px-3 py-2">
            <span class="size-2 rounded-full bg-zinc-400 shrink-0"></span>
            <div class="min-w-0">
                <div class="text-xs text-zinc-500 truncate">{{ \App\Enums\EventStatus::DRAFT->label() }}</div>
                <div class="text-lg font-semibold tabular-nums leading-tight">{{ $this->draftedEvents() }}</div>
            </div>
        </div>
        <div class="flex-1 flex items-center gap-2 rounded-lg bg-lime-50 dark:bg-lime-900/20 px-3 py-2">
            <span class="size-2 rounded-full bg-lime-500 shrink-0"></span>
            <div class="min-w-0">
                <div class="text-xs text-zinc-500 truncate">{{ \App\Enums\EventStatus::PUBLISHED->label() }}</div>
                <div class="text-lg font-semibold tabular-nums leading-tight">{{ $this->publishedEvents() }}</div>
            </div>
        </div>
        <div class="flex-1 flex items-center gap-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 px-3 py-2">
            <span class="size-2 rounded-full bg-blue-400 shrink-0"></span>
            <div class="min-w-0">
                <div class="text-xs text-zinc-500 truncate">{{ \App\Enums\EventStatus::PENDING->label() }}</div>
                <div class="text-lg font-semibold tabular-nums leading-tight">{{ $this->pendingEvents() }}</div>
            </div>
        </div>
    </div>

    <flux:separator class="mb-3" />

    {{-- Upcoming: kompakte Liste statt flux:table --}}
    <div class="flex items-center justify-between mb-2">
        <flux:text class="text-sm font-medium">{{ __('event.upcoming.title') }}</flux:text>
        <flux:text class="text-xs text-zinc-400">{{ $upcomingEventList->count() }} Termine</flux:text>
    </div>

    @if ($upcomingEventList->isEmpty())
        <div class="flex flex-col items-center justify-center py-6 text-center">
            <flux:icon name="calendar" class="size-8 text-zinc-300 dark:text-zinc-600 mb-1" />
            <flux:text class="text-sm text-zinc-400">{{ __('dashboard.no_upcoming_events') }}</flux:text>
        </div>
    @else
        <div class="space-y-1 max-h-48 overflow-y-auto -mx-1 px-1">
            @foreach ($upcomingEventList as $event)
                <a href="{{ route('backend.events.show', $event) }}"
                   class="flex items-center justify-between gap-2 rounded-lg px-2 py-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors group">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-xs tabular-nums text-zinc-400 shrink-0 w-16">
                            {{ $event->event_date->format('d.m.Y') }}
                        </span>
                        <span class="text-sm truncate group-hover:text-accent transition-colors">
                            {{ $event->name }}
                        </span>
                    </div>
                    <flux:badge size="sm" color="{{ $event->status->color() }}" class="shrink-0">
                        {{ $event->status->label() }}
                    </flux:badge>
                </a>
            @endforeach
        </div>
    @endif

</flux:card>