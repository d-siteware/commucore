<div class="space-y-4">
    @forelse($pendingRequests as $request)
        <flux:card wire:key="review-{{ $request->id }}" class="space-y-3">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:badge color="yellow" size="sm">
                        {{ __('change_request.status.pending') }}
                    </flux:badge>
                    <flux:heading size="sm">{{ $request->field->label() }}</flux:heading>
                    <div class="flex items-center gap-2 text-sm text-zinc-500">
                        <span>{{ $request->old_value ?: '–' }}</span>
                        <flux:icon.arrow-right variant="micro" />
                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $request->requested_value }}
                        </span>
                    </div>
                    <flux:text size="sm" class="text-zinc-500">{{ $request->reason }}</flux:text>
                    <flux:text size="sm" class="text-zinc-400">{{ $request->created_at->diffForHumans() }}</flux:text>
                </div>
                <flux:button size="sm"
                             variant="filled"
                             wire:click="startReview({{ $request->id }})"
                >{{ __('change_request.btn.review') }}
                </flux:button>
            </div>
        </flux:card>
    @empty
        <flux:text class="text-zinc-400">{{ __('change_request.review.empty') }}</flux:text>
    @endforelse

    {{-- Review Modal --}}
    <flux:modal name="change-request-review" class="w-full max-w-lg">
        @if($reviewingRequest)
            <div class="space-y-6">
                <flux:heading size="lg">{{ __('change_request.review.modal.title') }}</flux:heading>

                <div class="space-y-2 rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4">
                    <div class="flex justify-between text-sm">
                        <flux:text>{{ __('change_request.table.col.field') }}</flux:text>
                        <span class="font-medium">{{ $reviewingRequest->field->label() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <flux:text>{{ __('change_request.review.modal.old_value') }}</flux:text>
                        <span>{{ $reviewingRequest->old_value ?: '–' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <flux:text>{{ __('change_request.review.modal.requested_value') }}</flux:text>
                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $reviewingRequest->requested_value }}
                        </span>
                    </div>
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 text-sm">
                        <flux:text>{{ __('change_request.reason.label') }}</flux:text>
                        <p class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $reviewingRequest->reason }}</p>
                    </div>
                </div>

                @if($reviewingRequest->field === \App\Enums\MemberChangeField::FEE_TYPE)
                    <flux:textarea wire:model.blur="deduction_reason"
                                   rows="3"
                                   label="{{ __('members.apply.discount.reason.label') }}"
                                   :placeholder="__('change_request.review.modal.deduction_reason_placeholder')"
                                   :description="__('change_request.review.modal.deduction_reason_hint')"
                    />
                @endif

                <flux:textarea wire:model.blur="rejection_reason"
                               rows="3"
                               label="{{ __('change_request.review.modal.rejection_reason') }}"
                               :placeholder="__('change_request.review.modal.rejection_reason_placeholder')"
                               :description="__('change_request.review.modal.rejection_reason_hint')"
                />

                <div class="flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('app.btn.cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button variant="danger"
                                 wire:click="reject"
                    >{{ __('change_request.btn.reject') }}
                    </flux:button>
                    <flux:button variant="primary"
                                 wire:click="approve"
                    >{{ __('change_request.btn.approve') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>