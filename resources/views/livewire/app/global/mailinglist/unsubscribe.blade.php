<div>
    @if($is_deleted)
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
               <flux:card>
                  <section class="flex gap-10 items-center">
                      <flux:icon.check-circle color="green" class="size-16" />
                      <div>
                          <flux:subheading>{{ __('mails.mailing_list.unsubscribe.success_msg') }}</flux:subheading>
                          <flux:heading size="lg">{{ __('mails.mailing_list_unsubscribe_greeting') }}</flux:heading>
                      </div>
                  </section>
               </flux:card>
            </div>
        </div>
    @else
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <flux:card>
                    <section class="flex gap-10 items-center">
                        <flux:icon.face-frown color="pink" class="size-16" />
                        <div>
                            <flux:heading size="lg">{{ __('mails.mailing_list.unsubscribe.error_heading') }} :(</flux:heading>
                            <flux:subheading>{{ __('mails.mailing_list.unsubscribe.error_msg') }}</flux:subheading>
                        </div>
                    </section>
                </flux:card>
            </div>
        </div>
    @endif
</div>
