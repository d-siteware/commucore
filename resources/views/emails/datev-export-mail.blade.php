<x-mails.header>
    <x-slot name="header">
        {{ __('accounting.datev.mail.greeting') }}
    </x-slot>
</x-mails.header>

<h2>{{ __('accounting.datev.mail.heading', ['period' => $accountReport->period_start->isoFormat('MMMM Y')]) }}</h2>
<p>{{ __('accounting.datev.mail.body', ['account' => $accountReport->account->name, 'period' => $accountReport->period_start->isoFormat('Do MMMM') . ' - ' . $accountReport->period_end->isoFormat('Do MMMM')]) }}</p>

<p>{!! __('accounting.datev.mail.zip_structure') !!}</p>

<br>
<x-mails.link-button href="{{ $url }}">{{ __('accounting.datev.mail.download_link_label') }}</x-mails.link-button>

<p style="margin-top: 2em; font-size: 0.9em; color: #888;">{{ __('accounting.datev.mail.link_expiry') }}</p>

<p style="font-size: 0.85em; color: #999; word-break: break-all;">
    <strong>{{ __('accounting.datev.mail.checksum_label') }}</strong><br>
    <code style="font-size: 0.9em;">SHA-256: {{ $hash }}</code>
</p>

<x-mails.footer/>
