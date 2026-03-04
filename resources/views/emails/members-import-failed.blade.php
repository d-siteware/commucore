<x-mail::message>
    # {{ __('members.import.mail.failed_heading') }}

    {{ __('members.import.mail.failed_greeting', ['name' => $user->name]) }}

    {{ __('members.import.mail.failed_intro') }}

    <x-mail::panel>
        {{ $reason }}
    </x-mail::panel>

    {{ __('members.import.mail.failed_footer') }}

    {{ config('app.name') }}
</x-mail::message>