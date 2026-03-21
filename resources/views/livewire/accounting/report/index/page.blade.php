<div>
    <flux:heading size="lg">Monatsberichte</flux:heading>

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
                                <span>Prüfer</span>
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
                                    >{{ __('drucken') }}
                                    </flux:menu.item>
                                    <flux:menu.item icon="shield-check"
                                                    wire:click="initiateAudit({{ $item->id }})"
                                    >{{ __('prüfen') }}
                                    </flux:menu.item>
                                    @if(!$item->checkAuditStatus())
                                        <flux:menu.separator/>
                                        <flux:menu.item icon="pencil-square"
                                                        wire:click="editReport({{ $item->id }})"
                                        >{{ __('bearbeiten') }}
                                        </flux:menu.item>
                                        <flux:menu.item icon="trash"
                                                        variant="danger"
                                                        wire:click="deleteAudit({{ $item->id }})"
                                        >{{ __('löschen') }}
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
                <flux:heading size="lg">Prüfungen gefunden</flux:heading>
                <flux:text class="mt-2">Der zu löschende Bericht hat verknnüpfte Prüfungen. Diese gehen mit der Löschung des Berichtes verloren.</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button variant="ghost">Abbruch</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteSelectedReport"
                             variant="danger"
                >Alles löschen
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
                                 placeholder="Mitglied wählen"
                                 wire:model="selectedMember"
                    >
                        @foreach(App\Models\Membership\Member::select()->get() as $member)
                            <flux:select.option value="{{ $member->id }}">{{ $member->fullName() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:button icon-trailing="plus"
                                 wire:click="addAuditor"
                    >hinzu
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
                            niemand
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


    <flux:modal name="edit-account-report"
                variant="flyout"
                position="right"
    >
        <h3 class="my-6">{{ __('reports.account.edit.heading') }}</h3>
        @if($report)
            <form class="space-y-6" wire:submit="updateReport">

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

                <flux:textarea label="{{ __('reports.account.notes') }}" rows="auto"  wire:model="report.notes" />

                <flux:button type="submit" variant="primary">{{ __('reports.account.btn.store_data') }}</flux:button>

            </form>

        @endif

    </flux:modal>


</div>
