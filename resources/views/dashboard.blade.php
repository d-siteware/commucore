<x-app-layout title="{{ __('nav.dashboard') }}">
    <div class="grid gap-4 items-start" style="
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-areas:
            'events   projects   birthdays'
            'accounts fundings   applicants';
    ">
        <div style="grid-area: events">
            <livewire:dashboard.widgets.events />
        </div>

        <div style="grid-area: projects">
            <livewire:dashboard.widgets.project-widget />
        </div>

        <div style="grid-area: birthdays">
            <livewire:dashboard.widgets.upcomming-birthday-list />
        </div>

        <div style="grid-area: accounts">
            <flux:card>
                <flux:heading size="lg">Kontostände</flux:heading>
                <x-balance-sheet compact="true"/>
            </flux:card>
        </div>

        <div style="grid-area: fundings">
            <livewire:dashboard.widgets.funding-widget />
        </div>

        <div style="grid-area: applicants">
            <livewire:dashboard.widgets.applicants />
        </div>
    </div>
</x-app-layout>
