
<x-mail::message>
    # {{ __('members.import.mail.heading') }}

    {{ __('members.import.mail.greeting', ['name' => $user->name]) }}

    {{ __('members.import.mail.intro', ['date' => $importedAt]) }}

    <x-mail::table>
        | | |
        |:--|:--|
        | ✅ {{ __('members.import.mail.imported') }} | **{{ $protocol['imported'] }}** |
        | ⏭️ {{ __('members.import.mail.skipped') }} | **{{ $protocol['skipped'] }}** |
        | ❌ {{ __('members.import.mail.errors') }} | **{{ count($protocol['errors']) }}** |
        | ⏱️ {{ __('members.import.mail.duration') }} | **{{ $protocol['duration_ms'] }} ms** |
    </x-mail::table>

    @if(count($protocol['errors']) > 0)
        <x-mail::panel>
            **{{ __('members.import.mail.error_details') }}**

            @foreach($protocol['errors'] as $error)
                - {{ __('members.import.mail.error_row', ['row' => $error['row']]) }}: {{ $error['reason'] }}
            @endforeach
        </x-mail::panel>
    @endif

    {{ __('members.import.mail.backup_info') }}

    <x-mail::button :url="$backupDownloadUrl" color="green">
        {{ __('members.import.mail.backup_download') }}
    </x-mail::button>

    {{ __('members.import.mail.backup_expiry') }}

    {{ __('members.import.mail.footer') }}

    {{ config('app.name') }}
</x-mail::message>