<div class="space-y-4">
    @forelse($pendingRequests as $request)
        <flux:card wire:key="cancellation-review-{{ $request->id }}" class="space-y-3">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:badge color="orange" size="sm">
                        {{ __('cancellation_request.status.pending') }}
                    </flux:badge>
                    <flux:heading size="sm">
                        {{ $request->member->fullName() }}
                    </flux:heading>
                    <flux:text size="sm" class="text-zinc-500">
                        {{ $request->reason }}
                    </flux:text>
                    @if($request->requested_leave_date)
                        <flux:text size="sm" class="text-zinc-400">
                            {{ __('cancellation_request.leave_date.label') }}:
                            {{ $request->requested_leave_date->format('d.m.Y') }}
                        </flux:text>
                    @endif
                    <flux:text size="sm" class="text-zinc-400">
                        {{ $request->created_at->diffForHumans() }}
                    </flux:text>
                </div>
                <flux:button size="sm"
                             variant="filled"
                             wire:click="startReview({{ $request->id }})"
                >{{ __('change_request.btn.review') }}
                </flux:button>
            </div>
        </flux:card>
    @empty
        <flux:text class="text-zinc-400">
            {{ __('cancellation_request.review.empty') }}
        </flux:text>
    @endforelse

    <flux:modal name="cancellation-request-review" class="w-full max-w-lg">
        @if($reviewingRequest)
            <div class="space-y-6">
                <flux:heading size="lg">
                    {{ __('cancellation_request.review.modal.title') }}
                </flux:heading>

                <div class="space-y-2 rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4">
                    <div class="flex justify-between text-sm">
                        <flux:text>{{ __('cancellation_request.review.modal.member') }}</flux:text>
                        <span class="font-medium">{{ $reviewingRequest->member->fullName() }}</span>
                    </div>
                    @if($reviewingRequest->requested_leave_date)
                        <div class="flex justify-between text-sm">
                            <flux:text>{{ __('cancellation_request.leave_date.label') }}</flux:text>
                            <span>{{ $reviewingRequest->requested_leave_date->format('d.m.Y') }}</span>
                        </div>
                    @else
                        <div class="flex justify-between text-sm">
                            <flux:text>{{ __('cancellation_request.leave_date.label') }}</flux:text>
                            <span class="text-zinc-400">{{ __('cancellation_request.review.modal.leave_date_immediate') }}</span>
                        </div>
                    @endif
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 text-sm">
                        <flux:text>{{ __('cancellation_request.reason.label') }}</flux:text>
                        <p class="mt-1 text-zinc-700 dark:text-zinc-300">
                            {{ $reviewingRequest->reason }}
                        </p>
                    </div>
                </div>

                <flux:callout variant="warning" icon="exclamation-triangle">
                    <flux:callout.heading>
                        {{ __('cancellation_request.review.modal.warning.heading') }}
                    </flux:callout.heading>
                    <flux:callout.text>
                        {{ __('cancellation_request.review.modal.warning.text') }}
                    </flux:callout.text>
                </flux:callout>

                <flux:textarea wire:model="rejection_reason"
                               rows="3"
                               label="{{ __('change_request.review.modal.rejection_reason') }}"
                               :placeholder="__('change_request.review.modal.rejection_reason_placeholder')"
                               :description="__('cancellation_request.review.modal.rejection_reason_hint')"
                />

                <div class="flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('common.cancel') }}</flux:button>
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