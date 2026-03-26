<div class="space-y-8">

    <div>
        <flux:heading size="xl">{{ __('members.import.title') }}</flux:heading>
        <flux:subheading>{{ __('members.import.description') }}</flux:subheading>
    </div>

    <nav aria-label="Progress">
        <ol role="list" class="divide-y divide-gray-300 rounded-md border border-gray-300 md:flex md:divide-y-0 dark:divide-white/15 dark:border-white/15">
            <li class="relative md:flex md:flex-1">
                @if($currentStep >1)
                <x-steps.completed step="01" label="Upload" :item="1" />
                @endif
                @if($currentStep === 1)
                <x-steps.current step="01" label="Upload" :item="1" />
                @endif
            </li>
            <li class="relative md:flex md:flex-1">
                @if($currentStep > 2)
                <x-steps.completed step="02" label="Zuordnung" :item="2" />
                @endif
                @if($currentStep < 2)
                <x-steps.upcomming step="02" label="Zuordnung" :item="2" />
                @endif
                @if($currentStep === 2)
                <x-steps.current step="02" label="Zuordnung" :item="2" />
                @endif
            </li>
            <li class="relative md:flex md:flex-1">
                @if($currentStep > 3)
                <x-steps.completed step="03" label="Vorschau" :item="3" />
                @endif
                @if($currentStep === 3 )
                <x-steps.current step="03" label="Vorschau" :item="3" />
                @endif
                @if($currentStep < 3)
                <x-steps.upcomming step="03" label="Vorschau" :item="3" />
                @endif
            </li>
            <li class="relative md:flex md:flex-1">
                @if($currentStep === 4 )
                <x-steps.current step="04" label="Import" :item="4" last="true" />
                @endif
                    @if($currentStep <4 )
                <x-steps.upcomming step="04" label="Import" :item="4" last="true" />
                        @endif
            </li>
        </ol>
    </nav>
    {{-- Steps --}}
    @if($currentStep === 1)
        <livewire:member.import.steps.upload-step
                :import-type="$importType"
                @upload-complete="handleUploadComplete($event.detail.data)"
        />
    @elseif($currentStep === 2)
        <livewire:member.import.steps.mapping-step
                :csv-headers="$csvHeaders"
                :import-cache-key="$importCacheKey"
                @mapping-complete="handleMappingComplete($event.detail.data)"
        />
    @elseif($currentStep === 3)
        <livewire:member.import.steps.preview-step
                :import-cache-key="$importCacheKey"
                @backup-complete="handleBackupComplete($event.detail.backupPath)"
        />

    @elseif($currentStep === 4)
        <livewire:member.import.steps.import-step
                :import-cache-key="$importCacheKey"
                :backup-path="$backupPath"
                :import-type="$importType"
                @import-complete="handleImportComplete()"
        />
    @endif

</div>