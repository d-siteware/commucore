@props([
    'item' => 1,
    'step' => '01',
    'label' => 'Step #1',
    'last' => false
])
<!-- Current Step -->
<button wire:click="goToStep({{ $item }})" aria-current="step" class="flex items-center px-6 py-4 text-sm font-medium">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full border-2 border-teal-600 dark:border-teal-400">
                      <span class="text-teal-600 dark:text-teal-400">{{ $step }}</span>
                    </span>
    <span class="ml-4 text-sm font-medium text-teal-600 dark:text-teal-400">{{ $label }}</span>
</button>
@if (!$last)
    <!-- Arrow separator for lg screens and up -->
    <div aria-hidden="true" class="absolute top-0 right-0 hidden h-full w-5 md:block">
        <svg viewBox="0 0 22 80" fill="none" preserveAspectRatio="none" class="size-full text-gray-300 dark:text-white/15">
            <path d="M0 -2L20 40L0 82" stroke="currentcolor" vector-effect="non-scaling-stroke" stroke-linejoin="round" />
        </svg>
    </div>
@endif