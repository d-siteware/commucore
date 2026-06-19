<div class="flex items-center justify-between lg:justify-start text-xs gap-3 ">
    <span>{{ App\Models\User::find($audit->user_id)->name }}</span>
    @if ($audit->approved_at)
        <span class="flex gap-3">
            @if($audit->is_approved)
                <flux:icon.check-circle color="green"
                                        class="size-4"
                />
            @else
                <flux:icon.exclamation-circle color="red"
                                              class="size-4"
                />
            @endif
            {{ $audit->approved_at }}
        </span>
    @else
        <span class="flex">
        @if(Auth::user()->id === $audit->user_id)
                <flux:button href="{{ route('account-report.audit',$audit->id) }}"
                             icon="document-check"
                             size="xs"
                             variant="primary"
                >{{ __('account_report_audit.check_now') }}
            </flux:button>
            @else
                <flux:icon.x-circle color="zinc"
                                    class="size-4"
                />
            @endif
        </span>
    @endif
    @if ($audit->reason)

        <flux:tooltip toggleable>
            <flux:button icon="document-text"
                         size="xs"
                         variant="ghost"
            />
            <flux:tooltip.content class="max-w-[20rem] space-y-2">
                <p>{{ $audit->reason }}</p>
            </flux:tooltip.content>
        </flux:tooltip>
    @endif


</div>



