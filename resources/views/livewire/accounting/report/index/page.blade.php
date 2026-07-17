<div>
    <header class="flex items-center justify-between gap-3">
        <flux:heading size="lg">{{ __('reports.index.title') }}</flux:heading>
        <flux:button wire:click="openCreateReport"
                     variant="primary"
                     size="sm"
                     icon-trailing="plus"
        >{{ __('reports.create_report_btn') }}
        </flux:button>
    </header>
    <flux:table :paginate="$this->reports">
        <flux:table.columns>
            <flux:table.column sortable
                               :sorted="$sortBy === 'account_id'"
                               :direction="$sortDirection"
                               wire:click="sort('account')"
            >{{ __('reports.table.header.name') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'created'"
                               :direction="$sortDirection"
                               wire:click="sort('created_at')"
                               class="hidden md:table-cell"
            >{{ __('reports.table.header.date') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'status'"
                               :direction="$sortDirection"
                               wire:click="sort('status')"
                               class="hidden md:table-cell"
            >{{ __('reports.table.header.status') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'range'"
                               :direction="$sortDirection"
                               wire:click="sort('period_start')"
                               class="hidden lg:table-cell"
            >{{ __('reports.table.header.range') }}</flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'audited'"
                               :direction="$sortDirection"
                               wire:click="sort('audited')"
                               class="hidden lg:table-cell"
            >{{ __('reports.table.header.audited') }}</flux:table.column>

        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->reports as $item)
                <flux:table.row :key="$item->id">
                    <flux:table.cell>
                        <span>{{ $item->account->name }}</span>
                        <aside class="lg:hidden space-y-3 mt-2">
                            @if($item->checkAuditStatus())
                                <span>{{ __('reports.auditor') }}</span>
                                @foreach($item->getReportAudits() as $key => $audit)
                                    <x-table-cell-audit-item :audit="$audit"/>
                                @endforeach

                            @else
                                <flux:icon.x-circle color="orange"
                                                    class="size-5"
                                />
                            @endif
                        </aside>
                    </flux:table.cell>
                    <flux:table.cell class="hidden md:table-cell">
                        {{ $item->created_at->isoFormat('MMMM Y') }}
                    </flux:table.cell>
                    <flux:table.cell variant="strong"
                                     class="hidden md:table-cell"
                    >
                        {{ $item->status->label() }}
                    </flux:table.cell>
                    <flux:table.cell class="hidden lg:table-cell">
                        {{ $item->period_start->format('Y') }} //
                        {{ $item->period_start->isoFormat('Do MMMM') }} -
                        {{ $item->period_end->isoFormat('Do MMMM') }}
                    </flux:table.cell>
                    <flux:table.cell class="hidden lg:table-cell space-y-3">
                        @if($item->checkAuditStatus())
                            @foreach($item->getReportAudits() as $key => $audit)
                                <x-table-cell-audit-item :audit="$audit"/>
                            @endforeach

                        @else
                            <flux:icon.x-circle color="orange"
                                                class="size-5"
                            />
                        @endif
                    </flux:table.cell>
                    @can('create', \App\Models\Accounting\Account::class)
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost"
                                             size="sm"
                                             icon="ellipsis-horizontal"
                                             inset="top bottom"
                                ></flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="printer"
                                                    href="{{ route('accounts.report.print',$item->id) }}"
                                                    target="_blank"
                                    >{{ __('reports.index.actions.print') }}
                                    </flux:menu.item>
                                    <flux:menu.item icon="shield-check"
                                                    wire:click="initiateAudit({{ $item->id }})"
                                    >{{ __('reports.index.actions.audit') }}
                                    </flux:menu.item>
                                    @if ($item->status === \App\Enums\ReportStatus::audited)
                                        <flux:menu.item
                                                wire:click="exportDatev({{ $item->id }})"
                                                icon="arrow-down-tray"
                                        >
                                            {{ __('reports.index.actions.datev_export') }}
                                        </flux:menu.item>
                                    @endif
                                    @if(!$item->checkAuditStatus())
                                        <flux:menu.separator/>
                                        <flux:menu.item icon="pencil-square"
                                                        wire:click="editReport({{ $item->id }})"
                                        >{{ __('reports.index.actions.edit') }}
                                        </flux:menu.item>
                                        <flux:menu.item icon="trash"
                                                        variant="danger"
                                                        wire:click="deleteAudit({{ $item->id }})"
                                        >{{ __('reports.index.actions.delete') }}
                                        </flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    @endcan
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>


    <flux:modal name="delete-report-found-audits">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('reports.audits_found_heading') }}</flux:heading>
                <flux:text class="mt-2">{{ __('reports.audits_delete_warning') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('common.cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteSelectedReport"
                             variant="danger"
                >{{ __('reports.delete_all') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>


    <flux:modal name="initiate-report-audit"
                variant="flyout"
                position="right"
    >
        <form wire:submit="sendInvitations">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('reports.initiate-report-audit-modal.title') }}</flux:heading>
                    <flux:text>{{ __('reports.initiate-report-audit-modal.content') }}</flux:text>
                </div>

                <flux:input.group>
                    <flux:select variant="listbox"
                                 searchable
                                 placeholder="{{ __('reports.select_member_placeholder') }}"
                                 wire:model.blur="selectedMember"
                    >
                        @foreach(App\Models\Membership\Member::getAccountAuditingMembers() as $member)
                            <flux:select.option value="{{ $member->id }}">{{ $member->fullName() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:button icon-trailing="plus"
                                 wire:click="addAuditor"
                    >{{ __('reports.add_auditor') }}
                    </flux:button>
                </flux:input.group>


                <section class="space-y-6 px-3">
                    @forelse($auditorList as $key => $auditor)
                        <div class="flex justify-between items-center text-sm"
                             wire:key="{{$key}}"
                        >
                            <span>{{ $auditor->fullName() }}</span>
                            <flux:icon.trash color="red"
                                             class="size-4"
                                             wire:click="removeAuditor({{ $auditor->id }})"
                            />
                        </div>
                    @empty
                        <div class="flex justify-between items-center text-sm">
                            {{ __('reports.nobody') }}
                        </div>
                    @endforelse
                </section>


                <div class="flex">
                    <flux:spacer/>

                    <flux:button type="submit"
                                 variant="primary"
                    >{{ __('reports.initiate-report-audit-modal.btn.submit') }}</flux:button>
                </div>
            </div>
        </form>
        <x-debug/>
    </flux:modal>


    <flux:modal name="create-account-report"
                variant="flyout"
                position="right"
                class="space-y-6"
    >
        <div>
            <flux:heading size="lg">{{ __('reports.account.new.header') }}</flux:heading>
        </div>

        @if($selectedAccountId === null)
            <flux:select wire:model.live="selectedAccountId"
                         variant="listbox"
                         searchable
                         placeholder="{{ __('account.select_placeholder') }}"
            >
                @foreach($this->accounts as $account)
                    <flux:select.option value="{{ $account->id }}">
                        {{ $account->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        @else
            <livewire:accounting.report.create.form
                :account-id="$selectedAccountId"
                :wire:key="'report-create-'.$selectedAccountId"
            />
        @endif
    </flux:modal>

    <flux:modal name="edit-account-report"
                variant="flyout"
                position="right"
    >
        <h3 class="my-6">{{ __('reports.account.edit.heading') }}</h3>
        @if($report)
            <form class="space-y-6"
                  wire:submit="updateReport"
            >

                <flux:input wire:model.live.debounce="report.starting_amount"
                            x-mask:dynamic="$money($input, ',', '.')"
                            label="{{ __('reports.account.starting_amount') }}"
                />
                <flux:input wire:model.live.debounce="report.total_income"
                            x-mask:dynamic="$money($input, ',', '.')"
                            label="{{ __('reports.account.total_income') }}"
                />
                <flux:input wire:model.live.debounce="report.total_expenditure"
                            x-mask:dynamic="$money($input, ',', '.')"
                            label="{{ __('reports.account.total_expenditure') }}"
                />
                <flux:input wire:model.live.debounce="report.end_amount"
                            x-mask:dynamic="$money($input, ',', '.')"
                            label="{{ __('reports.account.end_amount') }}"
                />

                <flux:textarea label="{{ __('reports.account.notes') }}"
                               rows="auto"
                               wire:model.blur="report.notes"
                />

                <flux:button type="submit"
                             variant="primary"
                >{{ __('reports.account.btn.store_data') }}</flux:button>

            </form>

        @endif

    </flux:modal>


    <flux:modal name="reject-exported-report-confirm"
                class="max-w-md"
    >
        <div class="p-6 space-y-4">
            <div class="flex items-start gap-3">
                <div class="flex-none flex items-center justify-center size-10 rounded-full bg-amber-100">
                    <flux:icon.exclamation-triangle class="size-5 text-amber-600"/>
                </div>
                <div>
                    <flux:heading size="lg">{{ __('reports.index.export_warning.title') }}</flux:heading>
                    <flux:text class="mt-1 text-gray-600">
                        {{ __('reports.index.export_warning.body') }}
                    </flux:text>
                </div>
            </div>

            {{-- Export-Historie --}}
            @if (isset($selectedReport) && $selectedReport->datevExports->isNotEmpty())
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm space-y-1">
                    @foreach ($selectedReport->datevExports->sortByDesc('exported_at') as $export)
                        <div class="flex items-center justify-between gap-2 text-amber-800">
                            <span class="font-mono text-xs truncate">{{ $export->filename }}</span>
                            <span class="flex-none text-xs text-amber-600">
                            {{ $export->exported_at->format('d.m.Y H:i') }}
                            · {{ $export->user->name }}
                        </span>
                        </div>
                    @endforeach
                </div>
            @endif

            <flux:text class="text-sm text-gray-500">
                {{ __('reports.index.export_warning.steuerberater_hint') }}
            </flux:text>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost"
                             x-on:click="$flux.modal('reject-exported-report-confirm').close()"
                >
                    {{ __('common.cancel') }}
                </flux:button>
                <flux:button variant="danger"
                             wire:click="confirmAuditDespiteExport"
                >
                    {{ __('reports.index.export_warning.confirm') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>


    <flux:modal name="datev-export-checklist"
                variant="flyout"
                position="right"
    >
        <div class="p-6 space-y-6">
            <div>
                <flux:heading size="lg">{{ __('reports.index.datev_export.checklist.heading') }}</flux:heading>
                <flux:text class="mt-1 text-gray-600">
                    {{ __('reports.index.datev_export.checklist.subheading') }}
                </flux:text>
            </div>

            <div class="space-y-3">
                @foreach($datevValidationChecks as $check)
                    <div class="flex items-start gap-3 p-4 rounded-xl border
                        @if($check['passed']) border-green-200 bg-green-50
                        @elseif($check['type'] === 'error') border-red-200 bg-red-50
                        @else border-amber-200 bg-amber-50
                        @endif
                    "
                    >
                        @if($check['passed'])
                            <flux:icon.check-circle class="size-5 text-green-600 mt-0.5 shrink-0"/>
                        @elseif($check['type'] === 'error')
                            <flux:icon.x-circle class="size-5 text-red-600 mt-0.5 shrink-0"/>
                        @else
                            <flux:icon.exclamation-triangle class="size-5 text-amber-600 mt-0.5 shrink-0"/>
                        @endif
                        <div>
                            <p class="font-medium text-sm">{{ $check['label'] }}</p>
                            @if(!$check['passed'] && $check['message'])
                                <p class="text-xs mt-1 text-gray-600">{{ $check['message'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div>
                @php
                    $allGreen = collect($datevValidationChecks)->every(fn($c) => $c['passed']);
                @endphp
            </div>
            @if($allGreen)
                <flux:text class="text-sm text-green-700 font-medium">
                    {{ __('reports.index.datev_export.checklist.all_ok') }}
                </flux:text>
                <div class="flex items-center justify-between pt-2 w-full mt-4">

                    <div class="flex gap-2">
                        <flux:button wire:click="sendDatevExportByEmail"
                                     variant="primary"
                                     icon="envelope"
                        >
                            {{ __('reports.index.actions.datev_email') }}
                        </flux:button>
                        <flux:button wire:click="confirmExportDatev"
                                     variant="primary"
                                     icon="arrow-down-tray"
                        >
                            {{ __('reports.index.actions.datev_export') }}
                        </flux:button>
                    </div>
                </div>
            @else
                <flux:text class="text-sm text-gray-500">
                    {{ __('reports.index.datev_export.checklist.not_ready') }}
                </flux:text>
            @endif
        </div>
    </flux:modal>


</div>
