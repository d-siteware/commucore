<x-login-layout title="{{ __('auth.account_deleted.title') }}">

    <flux:card class="space-y-6 max-w-2xl mx-auto mt-10 lg:mt-16">
        <flux:callout icon="information-circle" variant="warning" inline>
            <flux:callout.heading>{{ __('auth.account_deleted.title') }}</flux:callout.heading>
            <flux:callout.text>{{ __('auth.account_deleted.text') }}</flux:callout.text>
        </flux:callout>

        <div class="flex flex-col gap-3 sm:flex-row">
            <flux:button href="mailto:helpdesk@commu-core.app" variant="primary" icon-trailing="envelope">
                {{ __('auth.account_deleted.cta') }}
            </flux:button>

            <flux:button href="{{ route('home') }}" variant="ghost">
                {{ __('auth.account_deleted.home') }}
            </flux:button>
        </div>
    </flux:card>

</x-login-layout>
