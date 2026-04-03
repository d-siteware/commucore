<div>
    <flux:modal name="cancellation-request-create" class="w-full max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('cancellation_request.modal.title') }}</flux:heading>
                <flux:text class="mt-1">{{ __('cancellation_request.modal.description') }}</flux:text>
            </div>

            <flux:date-picker wire:model="requested_leave_date"
                              label="{{ __('cancellation_request.leave_date.label') }}"
                              :description="__('cancellation_request.leave_date.description')"
                              selectable-header
                              start-day="1"
                              clearable
            />

            <flux:textarea wire:model="reason"
                           rows="4"
                           label="{{ __('cancellation_request.reason.label') }}"
                           placeholder="{{ __('cancellation_request.reason.placeholder') }}"
            />

            <flux:callout variant="warning" icon="exclamation-triangle">
                <flux:callout.heading>{{ __('cancellation_request.modal.warning.heading') }}</flux:callout.heading>
                <flux:callout.text>{{ __('cancellation_request.modal.warning.text') }}</flux:callout.text>
            </flux:callout>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('app.btn.cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="danger"
                             wire:click="submit"
                >{{ __('cancellation_request.modal.submit') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
    <flux:modal.trigger name="cancellation-request-create">
        <flux:button variant="danger">Mitgliedschaft kündigen</flux:button>
    </flux:modal.trigger>
</div>