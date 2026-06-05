@component('mail::message')

    <h1 style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 22pt; line-height: 1.8;">
        {{ __('members.import.mail.heading') }}
    </h1>

    <p style="font-size: 12pt; line-height: 1.6; margin: 0 0 16px; color: #475569;">
        {{ __('members.import.mail.greeting', ['name' => $user->name]) }}
    </p>

    <p style="font-size: 12pt; line-height: 1.6; margin: 0 0 24px; color: #475569;">
        {{ __('members.import.mail.intro', ['date' => $importedAt]) }}
    </p>

    <table style="width: 100%; border-collapse: collapse; margin: 0 0 24px;">
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 12px; font-size: 12pt; color: #475569;">✅ {{ __('members.import.mail.imported') }}</td>
            <td style="padding: 10px 12px; font-size: 12pt; color: #0f172a; font-weight: 600; text-align: right;">{{ $protocol['imported'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 12px; font-size: 12pt; color: #475569;">⏭️ {{ __('members.import.mail.skipped') }}</td>
            <td style="padding: 10px 12px; font-size: 12pt; color: #0f172a; font-weight: 600; text-align: right;">{{ $protocol['skipped'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 12px; font-size: 12pt; color: #475569;">❌ {{ __('members.import.mail.errors') }}</td>
            <td style="padding: 10px 12px; font-size: 12pt; color: #0f172a; font-weight: 600; text-align: right;">{{ count($protocol['errors']) }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; font-size: 12pt; color: #475569;">⏱️ {{ __('members.import.mail.duration') }}</td>
            <td style="padding: 10px 12px; font-size: 12pt; color: #0f172a; font-weight: 600; text-align: right;">{{ $protocol['duration_ms'] }} ms</td>
        </tr>
    </table>

    @if(count($protocol['errors']) > 0)
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px 20px; margin: 0 0 24px;">
            <p style="font-size: 12pt; font-weight: 600; color: #991b1b; margin: 0 0 10px;">
                {{ __('members.import.mail.error_details') }}
            </p>
            @foreach($protocol['errors'] as $error)
                <p style="font-size: 11pt; color: #7f1d1d; margin: 4px 0;">
                    – {{ __('members.import.mail.error_row', ['row' => $error['row']]) }}: {{ $error['reason'] }}
                </p>
            @endforeach
        </div>
    @endif

    <p style="font-size: 12pt; line-height: 1.6; margin: 0 0 24px; color: #475569;">
        {{ __('members.import.mail.backup_info') }}
    </p>

    <p style="text-align: center; padding: 8px 0 24px;">
        <a href="{{ $backupDownloadUrl }}"
           style="background-color: #009688; color: #ffffff; padding: 13px 30px;
                  border-radius: 100px; font-weight: 600; font-size: 14px;
                  text-decoration: none; display: inline-block;">
            {{ __('members.import.mail.backup_download') }} →
        </a>
    </p>

    <p style="font-size: 11pt; line-height: 1.6; margin: 0 0 24px; color: #94a3b8;">
        {{ __('members.import.mail.backup_expiry') }}
    </p>

    <p style="font-size: 12pt; line-height: 1.6; margin: 0; color: #475569;">
        {{ __('members.import.mail.footer') }}<br>
        {{ config('app.name') }}
    </p>

    @slot('subcopy')
        {{ __('members.import.mail.backup_download') }}: {{ $backupDownloadUrl }}
    @endslot

@endcomponent