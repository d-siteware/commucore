<flux:card class="h-full">
    <div class="flex items-center justify-between mb-4">
        <div>
            <flux:heading size="sm" class="text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-xs font-semibold">
                Förderungen
            </flux:heading>
            <div class="flex items-baseline gap-2 mt-1">
                <flux:heading size="xl" class="tabular-nums">
                    {{ number_format($totalApproved / 100, 0, ',', '.') }} €
                </flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400 text-sm">bewilligt</flux:text>
            </div>
        </div>
        <flux:button href="{{ route('funding.index') }}" variant="ghost" size="sm" icon="arrow-right" />
    </div>

    {{-- Gesamt-Übersicht: 3 KPI-Werte --}}
    @if ($totalApproved > 0)
        <div class="grid grid-cols-3 gap-2 mb-4 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
            <div class="text-center">
                <flux:text class="text-xs text-zinc-400 block">Erhalten</flux:text>
                <flux:text class="text-sm font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">
                    {{ number_format($totalReceived / 100, 0, ',', '.') }} €
                </flux:text>
            </div>
            <div class="text-center border-x border-zinc-200 dark:border-zinc-700">
                <flux:text class="text-xs text-zinc-400 block">Verplant</flux:text>
                <flux:text class="text-sm font-semibold tabular-nums text-sky-600 dark:text-sky-400">
                    {{ number_format($totalAllocated / 100, 0, ',', '.') }} €
                </flux:text>
            </div>
            <div class="text-center">
                <flux:text class="text-xs text-zinc-400 block">Rest</flux:text>
                <flux:text @class([
                    'text-sm font-semibold tabular-nums',
                    'text-zinc-700 dark:text-zinc-300' => $totalRemaining >= 0,
                    'text-red-600 dark:text-red-400'   => $totalRemaining < 0,
                ])>
                    {{ number_format($totalRemaining / 100, 0, ',', '.') }} €
                </flux:text>
            </div>
        </div>
    @endif

    @if ($fundings->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <flux:icon name="banknotes" class="size-10 text-zinc-300 dark:text-zinc-600 mb-2" />
            <flux:text class="text-zinc-500">{{ __('dashboard.no_active_fundings') }}</flux:text>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($fundings as $funding)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <flux:text class="text-sm font-medium truncate">
                                    {{ $funding['title'] }}
                                </flux:text>
                                @if ($funding['expires_soon'])
                                    <flux:badge color="amber" size="sm" icon="clock">bald</flux:badge>
                                @endif
                            </div>
                            <flux:text class="text-xs text-zinc-400 truncate">
                                {{ $funding['funder'] }}
                            </flux:text>
                        </div>
                        <flux:text class="text-xs text-zinc-400 shrink-0 ml-2 tabular-nums">
                            {{ number_format($funding['approved'] / 100, 0, ',', '.') }} €
                        </flux:text>
                    </div>

                    {{-- Gestapelter Fortschrittsbalken: erhalten (grün) + verplant (blau) --}}
                    <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden flex">
                        {{-- Erhalten --}}
                        <div
                                class="h-full bg-emerald-500 transition-all duration-500"
                                style="width: {{ $funding['received_rate'] }}%"
                                title="Erhalten: {{ $funding['received_rate'] }} %"
                        ></div>
                        {{-- Verplant aber noch nicht erhalten --}}
                        @php $extraAllocated = max(0, $funding['allocated_rate'] - $funding['received_rate']); @endphp
                        @if ($extraAllocated > 0)
                            <div
                                    class="h-full bg-sky-400/60 transition-all duration-500"
                                    style="width: {{ $extraAllocated }}%"
                                    title="Verplant: {{ $funding['allocated_rate'] }} %"
                            ></div>
                        @endif
                    </div>

                    <div class="flex justify-between mt-0.5">
                        <flux:text class="text-xs text-zinc-400">
                            {{ $funding['received_rate'] }} % erhalten
                            · {{ $funding['allocated_rate'] }} % verplant
                        </flux:text>
                        @if ($funding['period_end'])
                            <flux:text class="text-xs text-zinc-400">
                                bis {{ $funding['period_end'] }}
                            </flux:text>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</flux:card>