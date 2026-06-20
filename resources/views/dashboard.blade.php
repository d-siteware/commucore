<x-app-layout title="{{ __('nav.dashboard') }}">
    <div class="columns-lg lg:columns-2 xl:columns-3 gap-9 space-y-9 2xl:columns-4">
        <div class="break-inside-avoid">
            <livewire:dashboard.widgets.events/>
        </div>

        <div class="break-inside-avoid">
            <livewire:dashboard.widgets.project-widget/>
        </div>

        <div class="break-inside-avoid">
            <livewire:dashboard.widgets.upcomming-birthday-list/>
        </div>

        <div class="break-inside-avoid">
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="sm"
                                  class="text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-xs font-semibold"
                    >
                        {{ __('dashboard.account_balances') }}
                    </flux:heading>
                    <flux:button href="{{ route('accounts.index') }}"
                                 variant="ghost"
                                 size="sm"
                                 icon="arrow-right"
                    />
                </div>
                <x-balance-sheet compact="true"/>
            </flux:card>
        </div>

        @can('update', \App\Models\Setting::class)
            <div class="break-inside-avoid">
                <livewire:dashboard.widgets.onboarding-checklist />
            </div>
        @endcan

        <div class="break-inside-avoid">
            <livewire:dashboard.widgets.funding-widget/>
        </div>

        <div class="break-inside-avoid">
            <livewire:dashboard.widgets.applicants/>
        </div>
        <div class="break-inside-avoid">
            <livewire:dashboard.widgets.member-growth-chart/>
        </div>
        <div class="break-inside-avoid">
            <livewire:dashboard.widgets.member-fee-status/>
        </div>
    </div>
</x-app-layout>
