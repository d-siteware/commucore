<div class="space-y-2 lg:space-y-6">
    <flux:heading size="xl">{{ __('members.apply.title') }}</flux:heading>
    <flex:text>{{ __('members.apply.text') }}</flex:text>

    <flux:accordion transition
                    class="bg-white dark:bg-zinc-600 p-2 rounded-sm"
    >
        <flux:accordion.item>
            <flux:accordion.heading>{{ __('members.apply.process') }}</flux:accordion.heading>

            <flux:accordion.content>
                <aside class="p-3 my-3 border border-zinc-300 dark:border-zinc-500">
                    <flux:heading size="lg">{{ __('members.apply.email.note.header') }}</flux:heading>
                    <flux:text>{{ __('members.apply.email.note.content') }}</flux:text>
                </aside>
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


    <livewire:member.create.form :isExternalMemberApplication="(bool) $isExternalMemberApplication"/>
</div>
