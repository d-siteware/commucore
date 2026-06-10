<div class="space-y-6">
    {{-- Pending --}}
    @if($pendingRequests->isNotEmpty())
        <div class="space-y-3">
            <flux:heading size="sm">{{ __('change_request.table.pending_heading') }}</flux:heading>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('change_request.table.col.field') }}</flux:table.column>
                    <flux:table.column>{{ __('change_request.table.col.requested_value') }}</flux:table.column>
                    <flux:table.column>{{ __('change_request.table.col.reason') }}</flux:table.column>
                    <flux:table.column>{{ __('change_request.table.col.status') }}</flux:table.column>
                    <flux:table.column>{{ __('change_request.table.col.date') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($pendingRequests as $request)
                        <flux:table.row wire:key="pending-{{ $request->id }}">
                            <flux:table.cell>{{ $request->field->label() }}</flux:table.cell>
                            <flux:table.cell>{{ $request->requested_value }}</flux:table.cell>
                            <flux:table.cell class="max-w-xs truncate">{{ $request->reason }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $request->statusColor() }}" size="sm">
                                    {{ $request->statusLabel() }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $request->created_at->diffForHumans() }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    {{-- History --}}
    @if($historyRequests->isNotEmpty())
        <div class="space-y-3">
            <flux:heading size="sm">{{ __('change_request.table.history_heading') }}</flux:heading>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('change_request.table.col.field') }}</flux:table.column>
                    <flux:table.column>{{ __('change_request.table.col.requested_value') }}</flux:table.column>
                    <flux:table.column>{{ __('change_request.table.col.status') }}</flux:table.column>
                    <flux:table.column>{{ __('change_request.table.col.reviewed_by') }}</flux:table.column>
                    <flux:table.column>{{ __('change_request.table.col.date') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($historyRequests as $request)
                        <flux:table.row wire:key="history-{{ $request->id }}">
                            <flux:table.cell>{{ $request->field->label() }}</flux:table.cell>
                            <flux:table.cell>{{ $request->requested_value }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $request->statusColor() }}" size="sm">
                                    {{ $request->statusLabel() }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $request->reviewedBy?->name ?? '-' }}</flux:table.cell>
                            <flux:table.cell>{{ $request->reviewed_at?->diffForHumans() ?? '-' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    <flux:modal.trigger name="change-request-create">
        <flux:button>{{ __('change_request.trigger_btn') }}</flux:button>
    </flux:modal.trigger>
</div>