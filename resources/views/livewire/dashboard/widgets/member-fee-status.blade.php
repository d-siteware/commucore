<div>
    <flux:card class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <flux:heading size="sm"
                              class="text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-xs font-semibold"
                >
                    Mitgliedsbeiträge {{ $year }}
                </flux:heading>
                <div class="flex items-baseline gap-2 mt-1">
                    <flux:heading size="xl"
                                  class="tabular-nums"
                    >
                        {{ number_format($bookedAmount / 100, 0, ',', '.') }} €
                    </flux:heading>
                    <flux:text class="text-zinc-500 dark:text-zinc-400 text-sm">
                        von {{ number_format($targetAmount / 100, 0, ',', '.') }} € erhalten
                    </flux:text>
                </div>
            </div>
            <flux:button href="{{ route('backend.members.index') }}"
                         variant="ghost"
                         size="sm"
                         icon="arrow-right"
            />
        </div>

        {{-- Fortschrittsbalken --}}
        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden flex">
            {{-- Gebucht --}}
            <div
                    class="h-full bg-emerald-500 transition-all duration-500"
                    style="width: {{ $this->bookedRate() }}%"
                    title="Gebucht: {{ $this->bookedRate() }} %"
            ></div>
            {{-- Eingereicht, noch nicht gebucht --}}
            @if ($this->submittedRate() > 0)
                <div
                        class="h-full bg-sky-400/60 transition-all duration-500"
                        style="width: {{ $this->submittedRate() }}%"
                        title="Eingereicht: {{ $this->submittedRate() }} %"
                ></div>
            @endif
        </div>

        {{-- Legende --}}
        <div class="flex items-center gap-4 mt-3">
            <div class="flex items-center gap-1.5">
                <span class="inline-block h-2 w-3 rounded-full bg-emerald-500"></span>
                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                    Gebucht ({{ $this->bookedRate() }} %)
                </flux:text>
            </div>
            @if ($submittedAmount > 0)
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-2 w-3 rounded-full bg-sky-400/60"></span>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                        Eingereicht ({{ number_format($submittedAmount / 100, 0, ',', '.') }} €)
                    </flux:text>
                </div>
            @endif
            <div class="ml-auto">
                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                    Offen: {{ number_format(max(0, $targetAmount - $bookedAmount - $submittedAmount) / 100, 0, ',', '.') }} €
                </flux:text>
            </div>
        </div>
    </flux:card>
</div>