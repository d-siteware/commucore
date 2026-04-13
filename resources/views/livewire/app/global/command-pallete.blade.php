
<div>
    {{-- Keyboard Shortcut Listener --}}
    <div
            x-data
            x-on:keydown.meta.k.window.prevent="$dispatch('open-palette')"
            x-on:keydown.ctrl.k.window.prevent="$dispatch('open-palette')"
    ></div>

    @if($open)
        <flux:modal wire:model="open">
            <div class="p-1">
                {{-- Input --}}
                <div class="flex items-center gap-3 px-3 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:icon.magnifying-glass class="w-4 h-4 text-zinc-400 flex-shrink-0" />
                    <flux:input
                            wire:model.live.debounce.150ms="query"
                            type="text"
                            placeholder="{{ __('app.command_palette.placeholder') }}"
                            class="w-full bg-transparent text-sm outline-none text-zinc-900 dark:text-white placeholder:text-zinc-400"
                            x-ref="input"
                            x-init="$nextTick(() => $el.focus())"
                            wire:keydown.escape="close"
                    />
                    <kbd class="text-xs text-zinc-400 border border-zinc-200 dark:border-zinc-700 rounded px-1.5 py-0.5">esc</kbd>
                </div>

                {{-- Ergebnisse --}}
                <div class="max-h-80 overflow-y-auto py-2">
                    @forelse($results as $result)

                        <a href="{{ $result['url'] }}"
                        wire:navigate
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 group"
                        >
                        {{-- Typ-Icon --}}
                        <div class="w-7 h-7 rounded-md flex items-center justify-center flex-shrink-0
                                {{ match($result['type']) {
                                    'members'  => 'bg-teal-50 text-teal-600',
                                    'events'   => 'bg-cyan-50 text-cyan-600',
                                    'transactions' => 'bg-violet-50 text-violet-600',
                                    default    => 'bg-zinc-100 text-zinc-500',
                                } }}">
                            @if($result['type'] === 'members')
                                <flux:icon.user class="w-3.5 h-3.5" />
                            @elseif($result['type'] === 'events')
                                <flux:icon.calendar class="w-3.5 h-3.5" />
                            @else
                                <flux:icon.banknotes class="w-3.5 h-3.5" />
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                {{ $result['label'] }}
                            </div>
                            @if($result['meta'])
                                <div class="text-xs text-zinc-400 truncate">{{ $result['meta'] }}</div>
                            @endif
                        </div>

                        {{-- Typ-Badge --}}
                        <span class="text-xs text-zinc-400 flex-shrink-0">
                                {{ match($result['type']) {
                                    'members'  => __('app.command_palette.type_member'),
                                    'events'   => __('app.command_palette.type_event'),
                                    'transactions' => __('app.command_palette.filter_bookings'),
                                    default    => '',
                                } }}
                            </span>
                        </a>
                    @empty
                        @if(strlen($query) >= 1)
                            <div class="px-3 py-8 text-center text-sm text-zinc-400">
                                {{ __('app.command_palette.empty', ['query' => $query]) }}
                            </div>
                        @else
                            <div class="px-3 py-8 text-center text-sm text-zinc-400">
                                {{ __('app.command_palette.hint') }}
                            </div>
                        @endif
                    @endforelse
                </div>

                {{-- Footer --}}
                <div class="px-3 pt-2 border-t border-zinc-100 dark:border-zinc-800 flex gap-4 text-xs text-zinc-400">
                    <span><kbd class="border border-zinc-200 dark:border-zinc-700 rounded px-1">~</kbd>  {{ __('app.command_palette.filter_members') }}</span>
                    <span><kbd class="border border-zinc-200 dark:border-zinc-700 rounded px-1">></kbd>  {{ __('app.command_palette.filter_events') }}</span>
                    <span><kbd class="border border-zinc-200 dark:border-zinc-700 rounded px-1">#</kbd>  {{ __('app.command_palette.filter_bookings') }}</span>
                </div>
            </div>
        </flux:modal>
    @endif
</div>