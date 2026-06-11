
    <div wire:poll.1000ms="tick"
            class="demo-banner fixed bottom-0 left-0 right-0 z-50 flex items-center justify-between
           px-4 py-2 text-sm font-medium
           bg-gradient-to-r from-teal-600 to-cyan-600 text-white shadow-lg"
            role="banner"
    >
        {{-- Links: Demo-Hinweis --}}
        <div class="flex items-center gap-2">
            <span class="text-lg">🎮</span>
            <span class="font-semibold">{{ __('app.demo_banner.mode') }}</span>
            <span class="hidden sm:inline text-teal-100">
            {{ __('app.demo_banner.reset_note') }}
        </span>
        </div>

        {{-- Mitte: Countdown --}}
        @if($timeLeft)
            <div class="flex items-center gap-2 tabular-nums">
                @if($resetting)
                    <span class="animate-pulse">{{ __('app.demo_banner.resetting') }}</span>
                @else
                    <span class="text-teal-100 hidden sm:inline">{{ __('app.demo_banner.next_reset') }}</span>
                    <span class="font-mono text-base font-bold tracking-wide bg-white/20 rounded px-2 py-0.5">
                {{ $timeLeft }}
            </span>
                @endif
            </div>
        @endif

        {{-- Rechts: CTA --}}
        <div class="flex items-center gap-3">
            <a
                    href="{{ config('app.register_url', 'https://commu-core.app/register') }}?utm_source=demo&utm_medium=banner"
                    target="_blank"
                    class="hidden sm:inline-flex items-center gap-1 px-3 py-1 rounded-full
                   bg-white text-teal-700 font-semibold text-xs hover:bg-teal-50
                   transition-colors duration-150 whitespace-nowrap"
            >
                {{ __('app.demo_banner.register_cta') }}
            </a>
            {{-- Mobile: nur Icon --}}
            <a
                    href="{{ config('app.register_url', 'https://commu-core.app/register') }}?utm_source=demo&utm_medium=banner"
                    target="_blank"
                    class="sm:hidden text-white hover:text-teal-100"
                    title="{{ __('app.demo_banner.register_cta') }}"
            >
                ✦
            </a>
        </div>
    </div>
