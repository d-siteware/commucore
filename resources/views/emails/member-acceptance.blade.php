<x-mails.header>

<x-slot name="header">
    {{ __('mails.invitation.greeting',[ 'name' => $member->first_name]) }}
</x-slot>

</x-mails.header>

<h2>{{ __('mails.acceptance.header') }}</h2>
<h3>{{ __('mails.acceptance.text') }}</h3>

<x-mails.footer/>
