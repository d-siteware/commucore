<div class="max-w-md mx-auto py-12 space-y-6">
    @if (!$mailingList->verified_at)
        <flux:heading size="lg"><h2>{{ __('mails.mailing_list.verify.header') }}</h2></flux:heading>
        <flux:button variant="primary" icon-trailing="check"
            wire:click="verify">
            {{ __('mails.mailing_list.verify.btn') }}
        </flux:button>
    @else
        <h2 class="text-xl font-semibold mb-4">{{ __('mails.mailing_list.show.change') }}</h2>
        <form wire:submit.prevent="updatePreferences" class="space-y-4">
            <div class="space-y-2">
                <flux:checkbox.group>
                <flux:checkbox.all label="{{ __('mails.mailing_list.options.all_label') }}"
                                   description="{{ __('mails.mailing_list.options.all_description') }}"
                />
                <flux:checkbox wire:model="update_on_events"
                               label="{{ __('mails.mailing_list.options.events_label') }}"
                               description="{{ __('mails.mailing_list.options.events_description') }}"

                />
                <flux:checkbox wire:model="update_on_articles"
                               label="{{ __('mails.mailing_list.options.posts_label') }}"
                               description="{{ __('mails.mailing_list.options.posts_description') }}"
                />
                <flux:checkbox wire:model="update_on_notifications"
                               label="{{ __('mails.mailing_list.options.update_notifications_label') }}"
                               description="{{ __('mails.mailing_list.options.update_notifications_description') }}"
                />
                </flux:checkbox.group>
            </div>
            <flux:button variant="primary" type="submit">
               {{ __('mails.mailing_list.show.btn.save') }}
            </flux:button>
        </form>
    @endif

    <p class="mt-4 text-sm">
        <a href="{{ route('mailing-list.unsubscribe', $mailingList->verification_token) }}" class="underline text-gray-600 hover:text-emerald-600">
            {{ __('mails.mailing_list.unsubscribe.label') }}
        </a>
    </p>
</div>
