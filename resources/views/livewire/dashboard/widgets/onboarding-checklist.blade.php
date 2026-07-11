@php
    $pct = $this->totalCount > 0
        ? (int) round(($this->completedCount / $this->totalCount) * 100)
        : 0;
@endphp

<div
    @if (! $this->isDismissed)
        wire:poll.60s
    @endif
    class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900"
>
    @if ($this->isDismissed)
        <div class="flex items-center justify-between px-4 py-3">
            <span class="flex items-center gap-2 text-sm text-zinc-500">
                <flux:icon.check-circle class="size-4 text-emerald-500" />
                {{ __('onboarding.checklist.dismissed') }}
            </span>
            <button wire:click="reopen" class="flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                <flux:icon.eye class="size-4" />
                {{ __('onboarding.checklist.reopen') }}
            </button>
        </div>
    @else
        {{-- Header --}}
        <button
                wire:click="toggleCollapsed"
                class="flex w-full items-center gap-3 px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-800"
        >
            <flux:icon.list-bullet class="size-5 shrink-0 text-emerald-600" />
            <span class="flex-1 text-sm font-medium">{{ __('onboarding.checklist.title') }}</span>

            <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">
                <flux:icon.shield-check class="size-3" />
                {{ __('onboarding.checklist.admin_badge') }}
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
                        <flux:icon.star class="size-5 text-emerald-600" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ __('onboarding.checklist.all_done') }}</p>
                        <p class="text-xs text-zinc-500">{{ __('onboarding.checklist.all_done_subtitle') }}</p>
                    </div>
                </div>
            @endif

            <div class="divide-y divide-zinc-100 border-t border-zinc-100 dark:divide-zinc-800 dark:border-zinc-800">
                @foreach ($this->visibleSections as $section)
                    <div>
                            <p class="px-4 pb-1 pt-3 text-xs font-medium uppercase tracking-wide text-zinc-400">
                                {{ __($section['label']) }}
                            </p>

                        @foreach ($section['items'] as $item)
                            @php
                                $done = $this->status[$item['status_key']] ?? false;
                                $isCritical = $item['priority'] === \App\Enums\OnboardingPriority::Critical;
                                $needsAttention = ! $done && $isCritical;
                            @endphp

                            <div class="flex items-start gap-3 px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                {{-- Status-Icon, zusätzliches Warn-Icon für offene Muss-Punkte --}}
                                <div class="relative mt-0.5 shrink-0">
                                    <div class="flex size-5 items-center justify-center rounded-full {{ $done ? 'bg-emerald-500 text-white' : 'border-2 border-zinc-300 dark:border-zinc-600' }}">
                                        @if ($done)
                                            <flux:icon.check class="size-3" />
                                        @endif
                                    </div>
                                    @if ($needsAttention)
                                        <flux:icon.exclamation-triangle class="absolute -right-1.5 -top-1.5 size-3.5 rounded-full bg-white text-red-500 dark:bg-zinc-900" />
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <span class="text-sm {{ $done ? 'text-zinc-400 line-through' : 'text-zinc-700 dark:text-zinc-200' }}">
                                        {{ __($item['label']) }}
                                    </span>

                                    @unless ($done)
                                        <div class="mt-1 flex items-center gap-3">
                                            @if (! empty($item['route']) && \Illuminate\Support\Facades\Route::has($item['route']))
                                                <a href="{{ route($item['route']) }}" class="flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                                    <flux:icon.arrow-right class="size-3" />
                                                    {{ __('onboarding.checklist.go_to_module') }}
                                                </a>
                                            @endif

                                            @if (! empty($item['tutorial']))
                                                <a href="{{ $item['tutorial'] }}" target="_blank" rel="noopener" class="flex items-center gap-1 text-xs text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                                                    <flux:icon.book-open class="size-3" />
                                                    {{ __('onboarding.checklist.tutorial') }}
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
                <button wire:click="hideChecklist" class="flex items-center gap-1.5 text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <flux:icon.eye-slash class="size-3.5" />
                    {{ __('onboarding.checklist.hide') }}
                </button>
                <span class="text-xs text-zinc-400">{{ __('onboarding.checklist.completed', ['completed' => $this->completedCount, 'total' => $this->totalCount]) }}</span>
            </div>
        @endunless
    @endif
</div>
