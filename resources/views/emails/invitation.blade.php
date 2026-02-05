<x-mails.header >

    <x-slot name="header">{{ __('mails.invitation.greeting',[ 'name' => $member->first_name]) }}</x-slot>
</x-mails.header>
<h2>{{ __('mails.invitation.header') }}</h2>
<h3>{{ __('mails.invitation.text',['name'=>setting('organization.name')]) }}</h3>
<p>
    <x-mails.link-button href="{{ $url?? config('app.url') }}"
    >{{ __('mails.invitation.btn.label') }}
    </x-mails.link-button>
</p>
<br><br>
<x-mails.footer/>
