<div>
    <flux:modal name="change-request-create" class="w-full max-w-xl">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('change_request.modal.title') }}</flux:heading>
            <flux:text>{{ __('change_request.modal.description') }}</flux:text>

            <flux:select wire:model.live="field"
                         label="{{ __('change_request.field.label') }}"
                         placeholder="{{ __('change_request.field.placeholder') }}"
            >
                @foreach($availableFields as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            @if($field)
                @if($field === \App\Enums\MemberChangeField::TYPE->value)
                    <flux:radio.group wire:model="requested_value"
                                      label="{{ __('change_request.requested_value.label') }}"
                                      variant="cards"
                                      class="flex-col"
                    >
                        @foreach(\App\Enums\MemberType::options() as $value => $label)
                            @if($value !== $member->type->value)
                                <flux:radio value="{{ $value }}" label="{{ $label }}" />
                            @endif
                        @endforeach
                    </flux:radio.group>

                @elseif($field === \App\Enums\MemberChangeField::FEE_TYPE->value)
                    <flux:radio.group wire:model="requested_value"
                                      label="{{ __('change_request.requested_value.label') }}"
                                      variant="cards"
                                      class="flex-col"
                    >
                        @foreach(\App\Enums\MemberFeeType::options() as $value => $label)
                            @if($value !== $member->fee_type->value)
                                <flux:radio value="{{ $value }}" label="{{ $label }}" />
                            @endif
                        @endforeach
                    </flux:radio.group>
                @endif

                <flux:textarea wire:model.blur="reason"
                               rows="3"
                               label="{{ __('change_request.reason.label') }}"
                               placeholder="{{ __('change_request.reason.placeholder') }}"
                />
            @endif


            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('app.btn.cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="primary"
                             wire:click="submit"
                             :disabled="!$field"
                >{{ __('change_request.modal.submit') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>