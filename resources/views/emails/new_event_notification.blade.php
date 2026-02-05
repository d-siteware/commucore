@php use App\Models\Membership\Member; @endphp
@php use App\Enums\Gender; @endphp
<x-mails.header :title="$notifiable->getEmailSubject($recipient['locale'])"/>

@if($recipient['type'] === 'member')
    @php
        $member = Member::find($recipient['id']);
    @endphp


    @if($recipient['locale'] === 'de')
        @if($member->gender === Gender::ma->value)
            <h1>Lieber {{ $member->first_name }},</h1>
        @else
            <h1>Liebe {{ $member->first_name }},</h1>

        @endif
    @else
        <h1>Kedves {{ $member->fullName() }},</h1>
    @endif

    <p>{{ __('event.notification_mail.content_member') }}</p>

@else

    @if($recipient['locale'] === 'de')
        <h1>Hallo,</h1>
    @else
        <h1>Szia,</h1>
    @endif

    <p>{{ __('event.notification_mail.content_subscriber') }}</p>

@endif

<p style="font-size: 14pt;">{{ __('event.notification_mail.content.excerpt.header') }}</p>
{!! \Illuminate\Support\Str::limit($notifiable->description[$recipient['locale']], 200,' ... ', true) !!}

<p style="font-size: 14pt;">{{ __('event.notification_mail.content.details.header') }}</p>
<p>{{ __('event.notification_mail.content.details.event_date') }}: {{ $notifiable->event_date->locale($recipient['locale'])->isoFormat('Do MMMM') }}</p>
<p>{{ __('event.notification_mail.content.details.start_time') }}: {{ $notifiable->start_time->format('H:s') }}</p>
<p>{{ __('event.notification_mail.content.details.venue') }}: {{ $notifiable->venue->address() }}</p>


<x-mails.link-button href="{{ route($notificationType.'.show',$notifiable->slug[$recipient['locale']]) }}" >{{ __('event.notification_mail.btn_link_label') }}</x-mails.link-button>

<br><br><br>

@if(isset($recipient['verification_token']))
    <x-mails.footer :token="$recipient['verification_token']"/>
@else
    <x-mails.footer/>
@endif
