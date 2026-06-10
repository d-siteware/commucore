<flux:card class="h-full">
    <div class="flex items-start justify-between mb-4">
        <div>
            <flux:heading size="sm" class="text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-xs font-semibold">
                Projekte
            </flux:heading>
            <div class="flex items-baseline gap-3 mt-1">
                <flux:heading size="xl" class="tabular-nums">{{ $totalActive }}</flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400 text-sm">aktiv</flux:text>
                @if ($totalPlanned > 0)
                    <flux:badge color="zinc" size="sm">{{ $totalPlanned }} geplant</flux:badge>
                @endif
            </div>
        </div>
        <flux:button href="{{ route('project.index') }}" variant="ghost" size="sm" icon="arrow-right" />
    </div>

    @if ($projects->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <flux:icon name="folder-open" class="size-10 text-zinc-300 dark:text-zinc-600 mb-2" />
            <flux:text class="text-zinc-500">{{ __('dashboard.no_active_projects') }}</flux:text>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($projects as $project)
                <div class="group">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2 min-w-0">
                            {{-- Status-Dot --}}
                            <span @class([
                                'size-2 rounded-full shrink-0',
                                'bg-emerald-500'                           => $project['status'] === \App\Enums\ProjectStatus::Active && !$project['overdue'],
                                'bg-amber-500 animate-pulse'               => $project['overdue'],
                                'bg-zinc-400 dark:bg-zinc-500'             => $project['status'] === \App\Enums\ProjectStatus::Planned,
                            ])></span>

                            <flux:text class="font-medium text-sm truncate">
                                {{ $project['title'] }}
                            </flux:text>

                            @if ($project['overdue'])
                                <flux:badge color="red" size="sm">{{ __('dashboard.overdue_badge') }}</flux:badge>
                            @endif
                        </div>

                        <flux:text class="text-xs text-zinc-400 shrink-0 ml-2 tabular-nums">
                            {{ $project['coverage'] }} %
                        </flux:text>
                    </div>

                    {{-- Fortschrittsbalken: Förderdeckung --}}
                    <div class="h-1.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div
                                class="h-full rounded-full transition-all duration-500 @if($project['coverage'] >= 80) bg-emerald-500 @elseif($project['coverage'] >= 40) bg-amber-400 @else bg-red-400 @endif"
                                style="width: {{ min(100, $project['coverage']) }}%"
                        ></div>
                    </div>

                    <div class="flex justify-between mt-0.5">
                        <flux:text class="text-xs text-zinc-400">
                            {{ number_format($project['funding'] / 100, 2, ',', '.') }} € Förderung
                        </flux:text>
                        <flux:text class="text-xs text-zinc-400">
                            @if ($project['end_date'])
                                bis {{ $project['end_date'] }}
                            @else
                                kein Enddatum
                            @endif
                        </flux:text>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</flux:card>