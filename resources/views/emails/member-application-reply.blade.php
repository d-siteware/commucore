<x-mails.header header="{{ __('members.appliance_received.mail.subject') }}"/>

<h1>{{ __('members.appliance_received.mail.greeting',['name' => $applicant->first_name . ' '. $applicant->name]) }}</h1>

<p>{{ __('members.appliance_received.mail.text') }}</p>
<br><br>

<x-mails.link-button href="{{ $url?? setting('organization.web') }}">{{ __('members.apply.verify.mail.action') }}</x-mails.link-button>
<x-mails.footer/>
