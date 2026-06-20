@php
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $isDismissed = $user->onboarding_checklist_dismissed_at !== null;
    $pct = $this->totalCount > 0
        ? (int) round(($this->completedCount / $this->totalCount) * 100)
        : 0;
@endphp

@if ($isDismissed)
    <div class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900">
        <span class="flex items-center gap-2 text-zinc-500">
            <flux:icon.check-circle class="size-4 text-emerald-500" />
            Einrichtungs-Checkliste ausgeblendet
        </span>
        <button wire:click="reopen" class="flex items-center gap-1 text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
            <flux:icon.eye class="size-4" />
            Wieder anzeigen
        </button>
    </div>
@else
    <div wire:poll.60s class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        {{-- Header --}}
        <button
                wire:click="$toggle('collapsed')"
                class="flex w-full items-center gap-3 px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-800"
        >
            <flux:icon.list-checks class="size-5 shrink-0 text-emerald-600" />
            <span class="flex-1 text-sm font-medium">Einrichtungs-Checkliste</span>

            <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">
                <flux:icon.shield-check class="size-3" />
                Admin & Vorstand
            </span>

            <div class="flex items-center gap-2">
                <div class="h-1.5 w-32 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-700">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-300" style="width: {{ $pct }}%"></div>
                </div>
                <span class="min-w-[2.5rem] text-right text-xs text-zinc-500">{{ $pct }}%</span>
            </div>

            <flux:icon.chevron-down class="size-4 text-zinc-400 transition-transform {{ $collapsed ? '' : 'rotate-180' }}" />
        </button>

        {{-- Body --}}
        @unless ($collapsed)
            @if ($this->completedCount === $this->totalCount)
                <div class="flex items-center gap-3 border-t border-zinc-100 bg-emerald-50 px-4 py-3 dark:border-zinc-700 dark:bg-emerald-900/20">
                    <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-800">
                        <flux:icon.party-popper class="size-5 text-emerald-600" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">Alles erledigt!</p>
                        <p class="text-xs text-zinc-500">Euer Verein ist startklar. Viel Erfolg mit CommuCore.</p>
                    </div>
                </div>
            @endif

            <div class="divide-y divide-zinc-100 border-t border-zinc-100 dark:divide-zinc-800 dark:border-zinc-800">
                @foreach ($this->visibleSections as $section)
                    <div>
                        <p class="px-4 pb-1 pt-3 text-xs font-medium uppercase tracking-wide text-zinc-400">
                            {{ $section['section'] }}
                        </p>

                        @foreach ($section['items'] as $item)
                            @php $done = $this->status[$item['status_key']] ?? false; @endphp

                            <div class="flex items-start gap-3 px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                {{-- Reines Status-Icon, keine Interaktion --}}
                                <div class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full {{ $done ? 'bg-emerald-500 text-white' : 'border-2 border-zinc-300 dark:border-zinc-600' }}">
                                    @if ($done)
                                        <flux:icon.check class="size-3" />
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <span class="text-sm {{ $done ? 'text-zinc-400 line-through' : 'text-zinc-700 dark:text-zinc-200' }}">
                                        {{ $item['label'] }}
                                    </span>

                                    @unless ($done)
                                        <div class="mt-1 flex items-center gap-3">
                                            @if (! empty($item['route']) && \Illuminate\Support\Facades\Route::has($item['route']))
                                                <a href="{{ route($item['route']) }}" class="flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                                    <flux:icon.arrow-right class="size-3" />
                                                    Zum Modul
                                                </a>
                                            @endif

                                            @if (! empty($item['tutorial']))
                                                <a href="{{ $item['tutorial'] }}" target="_blank" rel="noopener" class="flex items-center gap-1 text-xs text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                                                    <flux:icon.book-open class="size-3" />
                                                    Tutorial
                                                </a>
                                            @endif
                                        </div>
                                    @endunless
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between border-t border-zinc-100 px-4 py-2.5 dark:border-zinc-800">
                <button wire:click="dismiss" class="flex items-center gap-1.5 text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <flux:icon.eye-off class="size-3.5" />
                    Checkliste ausblenden
                </button>
                <span class="text-xs text-zinc-400">{{ $this->completedCount }} / {{ $this->totalCount }} erledigt</span>
            </div>
        @endunless
    </div>
@endif
