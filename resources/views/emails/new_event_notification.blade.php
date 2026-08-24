@php use App\Models\Membership\Member; @endphp
@php use App\Enums\Gender; @endphp
<x-mails.header :title="$notifiable->getEmailSubject($recipient['locale'])"/>

@php
    // Refetch ist nötig (Recipient-Liste trägt nur id/email/locale), aber das
    // Mitglied kann zwischen Listenaufbau und Versand gelöscht worden sein —
    // dann wie ein Subscriber behandeln statt den ganzen Versandlauf zu killen.
    $member = $recipient['type'] === 'member' ? Member::find($recipient['id']) : null;
@endphp

@if($recipient['type'] === 'member' && $member)
    @if($recipient['locale'] === 'de')
        @if($member->gender === Gender::ma)
            <h1>{{ __('event.notification_mail.greeting.member_male', ['name' => $member->first_name]) }}</h1>
        @else
            <h1>{{ __('event.notification_mail.greeting.member_female', ['name' => $member->first_name]) }}</h1>

        @endif
    @else
        <h1>{{ __('event.notification_mail.greeting.member_male', ['name' => $member->fullName()], $recipient['locale']) }}</h1>
    @endif

    <p>{{ __('event.notification_mail.content_member') }}</p>

@else

    @if($recipient['locale'] === 'de')
        <h1>{{ __('event.notification_mail.greeting.subscriber') }}</h1>
    @else
        <h1>{{ __('event.notification_mail.greeting.subscriber', [], $recipient['locale']) }}</h1>
    @endif

    <p>{{ __('event.notification_mail.content_subscriber') }}</p>

@endif

<p style="font-size: 14pt;">{{ __('event.notification_mail.content.excerpt.header') }}</p>
{!! \Illuminate\Support\Str::limit($notifiable->description[$recipient['locale']], 200,' ... ', true) !!}

<p style="font-size: 14pt;">{{ __('event.notification_mail.content.details.header') }}</p>
<p>{{ __('event.notification_mail.content.details.event_date') }}: {{ $notifiable->event_date?->locale($recipient['locale'])->isoFormat('Do MMMM') ?? '—' }}</p>
<p>{{ __('event.notification_mail.content.details.start_time') }}: {{ $notifiable->start_time?->format('H:s') ?? '—' }}</p>
<p>{{ __('event.notification_mail.content.details.venue') }}: {{ $notifiable->venue?->address() ?? '—' }}</p>


<x-mails.link-button href="{{ route($notificationType.'.show',$notifiable->slug[$recipient['locale']]) }}" >{{ __('event.notification_mail.btn_link_label') }}</x-mails.link-button>

<br><br><br>

@if(isset($recipient['verification_token']))
    <x-mails.footer :token="$recipient['verification_token']"/>
@else
    <x-mails.footer/>
@endif
