@component('mail::message')

    <h1 style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 22pt; line-height: 1.8;">
        {{ __('members.import.mail.failed_heading') }}
    </h1>

    <p style="font-size: 12pt; line-height: 1.6; margin: 0 0 16px; color: #475569;">
        {{ __('members.import.mail.failed_greeting', ['name' => $user->name]) }}
    </p>

    <p style="font-size: 12pt; line-height: 1.6; margin: 0 0 24px; color: #475569;">
        {{ __('members.import.mail.failed_intro') }}
    </p>

    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px 20px; margin: 0 0 24px;">
        <p style="font-size: 12pt; color: #7f1d1d; margin: 0;">
            {{ $reason }}
        </p>
    </div>

    <p style="font-size: 12pt; line-height: 1.6; margin: 0; color: #475569;">
        {{ __('members.import.mail.failed_footer') }}<br>
        {{ config('app.name') }}
    </p>

@endcomponent