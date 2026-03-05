<x-mails.header header="{{ __('members.apply.verify.mail.subject') }}"/>

<p>{{ __('members.notifications.new_applicant.intro') }}</p>

<ul style="list-style: none;">
    <li>{{ __('members.name') }}: {{ $application->name }}</li>
    <li>{{ __('members.first_name') }}: {{ $application->first_name }}</li>

    @if($application->birth_date)
        <li>{{ __('members.birth_date') }}: {{ $application->birth_date->isoFormat('LL') }}</li>
    @endif

    <li>{{ __('members.email') }}: {{ $application->email }}</li>

    @if($application->mobile)
        <li>{{ __('members.mobile') }}: {{ $application->mobile }}</li>
    @endif

    @if($application->address)
        <li>{{ __('members.address') }}: {{ $application->address }}</li>
    @endif

    @if($application->zip)
        <li>{{ __('members.zip') }}: {{ $application->zip }}</li>
    @endif

    @if($application->city)
        <li>{{ __('members.city') }}: {{ $application->city }}</li>
    @endif

    @if($application->country)
        <li>{{ __('members.country') }}: {{ $application->country }}</li>
    @endif

    @if($application->locale)
        <li>{{ __('members.locale') }}: {{ $application->locale }}</li>
    @endif

    @if($application->gender)
        <li>{{ __('members.gender') }}: {{ $application->gender }}</li>
    @endif

    <li>{{ __('members.apply.discount.label') }}: {{ $application->is_deducted ? __('general.yes') : __('general.no') }}</li>

    @if($application->deduction_reason)
        <li>{{ __('members.apply.discount.reason.label') }}: {{ $application->deduction_reason }}</li>
    @endif

    @if($application->gdpr_consent_at)
        <li>{{ __('members.date.gdpr_consent_at') }}: {{ $application->gdpr_consent_at->isoFormat('LLL') }}</li>
    @endif
</ul>

<x-mails.link-button href="{{ route('dashboard') }}">
    {{ __('members.notifications.new_applicant.cta') }}
</x-mails.link-button>

<x-mails.footer/>