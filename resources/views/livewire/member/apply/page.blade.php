<div class="space-y-2 lg:space-y-6">
    <flux:heading size="xl">{{ __('members.apply.title',['name' => setting('organization.name')]) }}</flux:heading>


    {{-- STEP: Formular --}}
    @if($step === 'form')
        <flex:text>{{ __('members.apply.text',['name' => setting('organization.name')]) }}</flex:text>

        <flux:accordion transition
                        class="bg-white dark:bg-zinc-600 p-2 rounded-sm xl:my-10"
        >
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('members.apply.process') }}</flux:accordion.heading>

                <flux:accordion.content>

                    <flux:callout icon="exclamation-triangle"
                                  class="my-6 mx-3"
                    >
                        <flux:callout.heading>{{ __('members.apply.email.note.header') }}</flux:callout.heading>
                        <flux:callout.text>
                            {{ __('members.apply.email.note.content') }}
                        </flux:callout.text>
                    </flux:callout>


                    <section class="space-y-2 lg:space-y-6 mb-6">
                        <p><span class="font-semibold">{{ __('members.apply.step1.label') }}:</span> {{ __('members.apply.step1.text') }}</p>
                        <p><span class="font-semibold">{{ __('members.apply.step2.label') }}:</span> {{ __('members.apply.step2.text') }}</p>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">

                            <div class="space-y-2 lg:space-y-6">
                                <h3 class="font-semibold">{{ __('members.apply.via.web') }}</h3>
                                <p><span class="font-semibold">{{ __('members.apply.step3a.label') }}:</span> {{ __('members.apply.click.button') }} [{{ __('members.apply.checkAndSubmit') }}].</p>
                                <p><span class="font-semibold">{{ __('members.apply.step4a.label') }}:</span> {{ __('members.apply.step4a.text') }} </p>
                                <p><span class="font-semibold">{{ __('members.apply.step5a.label') }}:</span> {{ __('members.apply.step5a.text') }} </p>
                            </div>

                            <div class="space-y-2 lg:space-y-6">
                                <h3 class="font-semibold">{{ __('members.apply.via.postal') }}</h3>
                                <p><span class="font-semibold">{{ __('members.apply.step3b.label') }}:</span> {{ __('members.apply.click.checkbox') }} [{{ __('members.apply.email.none') }}].</p>
                                <p><span class="font-semibold">{{ __('members.apply.step4b.label') }}:</span> {{ __('members.apply.step4b.text') }}</p>
                                <p><span class="font-semibold">{{ __('members.apply.step5b.label') }}:</span> {{ __('members.apply.step4b.text') }}</p>
                            </div>
                        </div>


                        <p><span class="font-semibold">{{ __('members.apply.step6.label') }}:</span> {{ __('members.apply.step6.text') }}</p>
                        <p><span class="font-semibold">{{ __('members.apply.step7.label') }}:</span> {{ __('members.apply.step7.text') }}</p>
                    </section>
                </flux:accordion.content>
            </flux:accordion.item>

        </flux:accordion>

        <livewire:member.create.form
                :isExternalMemberApplication="true"
                @application-submitted="applicationSubmitted"
        />
    @endif

    {{-- STEP: Warten auf E-Mail-Bestätigung --}}
    @if($step === 'pending')
        <flux:card class="space-y-4 text-center">
            <flux:icon name="envelope"
                       class="mx-auto size-12 text-accent"
            />
            <flux:heading size="lg">{{ __('members.apply.pending.title') }}</flux:heading>
            <flux:text>{{ __('members.apply.pending.text') }}</flux:text>
        </flux:card>
    @endif

    {{-- STEP: E-Mail-Link abgelaufen --}}
    @if($step === 'expired')
        <flux:card class="space-y-4">
            <flux:callout variant="danger"
                          icon="exclamation-circle"
            >
                <flux:callout.heading>{{ __('members.apply.expired.title') }}</flux:callout.heading>
                <flux:callout.text>{{ __('members.apply.expired.text') }}</flux:callout.text>
            </flux:callout>
        </flux:card>
    @endif

    {{-- STEP: Ungültiger Token --}}
    @if($step === 'invalid')
        <flux:callout variant="danger"
                      icon="exclamation-circle"
        >
            <flux:callout.heading>{{ __('members.apply.invalid.title') }}</flux:callout.heading>
            <flux:callout.text>{{ __('members.apply.invalid.text') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- STEP: DSGVO-Bestätigung --}}
    @if($step === 'verify')
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('members.apply.verify.title') }}</flux:heading>
            <flux:text>
                {{ __('members.apply.verify.greeting', ['name' => $application->first_name . ' ' . $application->name]) }}
            </flux:text>

            {{-- Zusammenfassung der Antragsdaten --}}
            <flux:separator text="{{ __('members.apply.verify.summary') }}"/>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <flux:text class="font-semibold">{{ __('members.name') }}</flux:text>
                <flux:text>{{ $application->name }}, {{ $application->first_name }}</flux:text>

                <flux:text class="font-semibold">{{ __('members.email') }}</flux:text>
                <flux:text>{{ $application->email }}</flux:text>

                @if($application->address)
                    <flux:text class="font-semibold">{{ __('members.address') }}</flux:text>
                    <flux:text>{{ $application->address }}, {{ $application->zip }} {{ $application->city }}</flux:text>
                @endif
            </div>

            {{-- DSGVO-Felder --}}
            <flux:separator text="{{ __('members.apply.dsgvo.section.label') }}"/>
            <flux:text>{{ __('members.apply.dsgvo.section.text') }}</flux:text>
            <flux:checkbox wire:model="gdpr_consent"
                           label="{{ __('members.apply.dsgvo.gdpr.label') }} *"
                           description="{{ __('members.apply.dsgvo.gdpr.description') }}"
            />

            <flux:checkbox wire:model="newsletter_consent"
                           label="{{ __('members.apply.dsgvo.newsletter.label') }}"
                           description="{{ __('members.apply.dsgvo.newsletter.description') }}"
            />

            <flux:checkbox wire:model="photo_consent"
                           label="{{ __('members.apply.dsgvo.photo.label') }}"
                           description="{{ __('members.apply.dsgvo.photo.description') }}"
            />
{{--
            @error('gdpr_consent')
            <flux:callout variant="danger">
                <flux:callout.text>{{ $message }}</flux:callout.text>
            </flux:callout>
            @enderror--}}

            <flux:button wire:click="confirmConsents"
                         variant="primary"
                         icon="check-circle"
            >
                {{ __('members.apply.verify.submit') }}
            </flux:button>
        </flux:card>
    @endif

    {{-- STEP: Fertig --}}
    @if($step === 'done')
        <flux:card class="space-y-4 text-center">
            <flux:icon name="check-circle"
                       class="mx-auto size-12 text-green-500"
            />
            <flux:heading size="lg">{{ __('members.apply.done.title') }}</flux:heading>
            <flux:text>{{ __('members.apply.done.text') }}</flux:text>
        </flux:card>
    @endif
</div>

