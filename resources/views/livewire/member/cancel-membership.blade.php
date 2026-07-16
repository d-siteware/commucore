<div>
    <flux:modal name="cancel-membership" class="space-y-6 max-w-md">
        <div>
            <flux:heading size="lg">{{ __('members.backend.cancel.modal.title') }}</flux:heading>
            <flux:subheading>
                {{ __('members.backend.cancel.modal.subtitle', ['name' => $selectedMember?->fullName()]) }}
            </flux:subheading>
        </div>

        <flux:date-picker locale="{{ app()->getLocale() }}" with-today first-day="1"
                wire:model.blur="cancelDate"
                :label="__('members.backend.cancel.modal.date_label')"
        />

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('app.btn.cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button
                    wire:click="confirmCancelMembership"
                    variant="danger"
            >
                {{ __('members.backend.cancel.modal.confirm') }}
            </flux:button>
        </div>
    </flux:modal>
</div>
