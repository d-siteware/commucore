<div>
    <section class="lg:flex lg:flex-row gap-6">

        <flux:card class="lg:basis-2/3">
            <flux:heading size="lg">{{ __('account_report_audit.overview') }}</flux:heading>

            <dl class="divide-y divide-zinc-100">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm/6 font-medium text-zinc-900">{{ __('account_report_audit.account') }}</dt>
                    <dd class="mt-1 text-zinc-700 sm:col-span-2 sm:mt-0">{{$report->account->name }}</dd>
                    <dt class="text-sm/6 font-medium text-zinc-900">{{ __('account_report_audit.type') }}</dt>
                    <dd class="mt-1 text-zinc-700 sm:col-span-2 sm:mt-0">{{$report->account->type }}</dd>

                    <dt class="text-sm/6 font-medium text-zinc-900">{{ __('account_report_audit.starting_balance') }}</dt>
                    <dd class="mt-1 text-zinc-700 sm:col-span-2 sm:mt-0">{{ \App\Models\Accounting\Account::formatedAmount($report->starting_amount) }} <span class="text-xs">{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</span></dd>
                </div>
            </dl>


            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('account_report_audit.date') }}</flux:table.column>
                    <flux:table.column class="hidden md:table-cell">{{ __('account_report_audit.transaction') }}</flux:table.column>
                    <flux:table.column class="hidden lg:table-cell">{{ __('account_report_audit.reference') }}</flux:table.column>
                    <flux:table.column align="right">{{ __('account_report_audit.income') }}</flux:table.column>
                    <flux:table.column align="right">{{ __('account_report_audit.expense') }}</flux:table.column>
                    <flux:table.column class="hidden md:table-cell">{{ __('account_report_audit.type') }}</flux:table.column>
                    <flux:table.column align="right">{{ __('account_report_audit.balance') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell></flux:table.cell>
                        <flux:table.cell class="text-wrap">
                            {{ __('account_report_audit.carryover') }}
                        </flux:table.cell>
                        <flux:table.cell class="hidden md:table-cell"></flux:table.cell>
                        <flux:table.cell class="hidden lg:table-cell"></flux:table.cell>
                        <flux:table.cell class="hidden md:table-cell"></flux:table.cell>
                        <flux:table.cell></flux:table.cell>

                        <flux:table.cell align="end">{{ \App\Models\Accounting\Account::formatedAmount($report->starting_amount) }}</flux:table.cell>


                    </flux:table.row>
                    @php
                        $sub = $report->starting_amount;
                               $total_in = 0;
                               $total_out = 0;
                    @endphp
                    @foreach($transactions as $transaction)
                        @php
                            if ($transaction->type === \App\Enums\TransactionType::Deposit->value) {
                               $in = $transaction->amount_gross * $transaction->type->multiplier();
                               $out = 0;
                               $sub += $in;
                               $total_in += $in;
                           } else {
                               $out = $transaction->amount_gross * $transaction->type->multiplier();
                               $in = 0;
                               $sub += $out;
                               $total_out += $out;
                           }
                        @endphp
                        <flux:table.row>
                            <flux:table.cell>{{ $transaction->date->isoFormat('Do MMMM') }}</flux:table.cell>
                            <flux:table.cell class="hidden md:table-cell text-wrap hyphens-auto">{{ $transaction->label }}</flux:table.cell>
                            <flux:table.cell class="hidden lg:table-cell text-wrap hyphens-auto">{{ $transaction->reference }}</flux:table.cell>
                            <flux:table.cell align="end"><span class="{{ $transaction->type->color() }}">{{  \App\Models\Accounting\Account::formatedAmount($in) }}</span></flux:table.cell>
                            <flux:table.cell align="end"><span class="{{ $transaction->type->color() }}">{{  \App\Models\Accounting\Account::formatedAmount($out) }}</span></flux:table.cell>
                            <flux:table.cell class="hidden md:table-cell">{{ $transaction->type }}</flux:table.cell>
                            <flux:table.cell align="end">{{ \App\Models\Accounting\Account::formatedAmount($sub) }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>


        </flux:card>

        <flux:card class="lg:basis-1/3">
            <section class="space-y-6">
                <flux:heading size="lg">{{ __('account_report_audit.approval') }}</flux:heading>

                @can('audit',$audit)
                    <flux:button wire:click="approveAuditReport"
                                 variant="primary"
                    >{{ __('account_report_audit.approved') }}
                    </flux:button>

                    <flux:separator text="{{ __('account_report_audit.or') }}"/>


                    <flux:button wire:click="rejectAuditReport"
                                 variant="danger"
                    >{{ __('account_report_audit.not_approved') }}
                    </flux:button>
                    <flux:textarea wire:model="form.reason"
                                   rows="auto"
                                   :label="__('account_report_audit.reason')"
                    />
                @elsecan
                    <flux:text>{{ __('account_report_audit.no_permission') }}</flux:text>
                @endcan
            </section>

        </flux:card>

    </section>
</div>
