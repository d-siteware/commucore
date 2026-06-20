@props(['level' => null])

@if ($level)
    <a
        href="{{ route('dashboard') }}"
        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
        {{ match($level) {
            'red' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
        } }}"
    >
        <span class="relative flex size-2">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75
            {{ match($level) {
                'red' => 'bg-red-400',
                'amber' => 'bg-amber-400',
                default => 'bg-zinc-400',
            } }}"></span>
            <span class="relative inline-flex size-2 rounded-full
            {{ match($level) {
                'red' => 'bg-red-500',
                'amber' => 'bg-amber-500',
                default => 'bg-zinc-500',
            } }}"></span>
        </span>

        {{ match($level) {
            'red' => __('onboarding.badge.red'),
            'amber' => __('onboarding.badge.amber'),
            default => '',
        } }}
    </a>
@endif
