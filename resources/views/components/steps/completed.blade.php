@props([
    'item' => 1,
    'step' => '01',
    'label' => 'Step #1'
])
<!-- Completed Step -->
<button wire:click="goToStep({{ $item }})" class="group flex w-full items-center">
    <span class="flex items-center px-6 py-4 text-sm font-medium">
      <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-teal-600 group-hover:bg-teal-800 dark:bg-teal-500 dark:group-hover:bg-teal-400">
        <svg viewBox="0 0 24 24"
             fill="currentColor"
             data-slot="icon"
             aria-hidden="true"
             class="size-6 text-white"
        >
          <path d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z"
                clip-rule="evenodd"
                fill-rule="evenodd"
          />
        </svg>
      </span>
      <span class="ml-4 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</span>
    </span>
</button>
<!-- Arrow separator for lg screens and up -->
<div aria-hidden="true" class="absolute top-0 right-0 hidden h-full w-5 md:block">
    <svg viewBox="0 0 22 80" fill="none" preserveAspectRatio="none" class="size-full text-gray-300 dark:text-white/15">
        <path d="M0 -2L20 40L0 82" stroke="currentcolor" vector-effect="non-scaling-stroke" stroke-linejoin="round" />
    </svg>
</div>