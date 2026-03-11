<x-login-layout title="{{ __('app.login.title') }}" >



    <flux:card class="space-y-6 max-w-2xl mx-auto mt-10 lg:mt-16">
        <div>
            <flux:heading size="lg">{{ __('app.login.header') }}</flux:heading>
{{--            <flux:subheading>Welcome back!</flux:subheading>--}}
        </div>

        @error('sso')
        <flux:callout icon="exclamation-triangle" variant="warning" inline>
            <flux:callout.heading>Fehler beim SSO</flux:callout.heading>

            <x-slot name="actions">
                <flux:button href="{{ route('home') }}">Neu anmelden</flux:button>
            </x-slot>
        </flux:callout>
        @enderror

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="space-y-6">
                <flux:input name="email" label="{{ __('app.login.email.label') }}" type="email" required autocomplete="email"/>

                <flux:field>
                    <div class="mb-3 flex justify-between">
                        <flux:label>{{ __('app.login.password.label') }}</flux:label>
                        <flux:link href="{{ route('password.request') }}" variant="subtle" class="text-sm">{{ __('app.login.forgot_password.label') }}</flux:link>
                    </div>

                    <flux:input name="password" type="password" required />

                    <flux:error name="password" />
                </flux:field>

                <flux:button type="submit" variant="primary" icon-trailing="arrow-right-end-on-rectangle" >{{ __('app.login.btn.login.label') }}</flux:button>
            </div>
        </form>

    </flux:card>

</x-login-layout>
