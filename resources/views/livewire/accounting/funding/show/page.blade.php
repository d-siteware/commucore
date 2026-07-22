<div>
    <flux:heading class="lg:mb-9 lg:hidden" size="lg">{{ __('fundings.show.page.title') }}</flux:heading>
    <flux:heading class="lg:mb-9 lg:flex hidden">{{ __('fundings.show.page.title') }}</flux:heading>
    <flux:subheading size="xl">{{ $form->title }}</flux:subheading>

    <flux:tab.group class="mt-3">
        <flux:tabs wire:model="selectedTab">
            <flux:tab name="funding-show-details"
                      icon="document-text"
                      wire:click="setSelectedTab('funding-show-details')"
            ><span class="hidden md:inline">{{ __('fundings.tabs.details') }}</span></flux:tab>

            <flux:tab name="funding-show-receipts"
                      icon="banknotes"
                      wire:click="setSelectedTab('funding-show-receipts')"
            ><span class="hidden md:inline">{{ __('fundings.tabs.receipts') }}</span></flux:tab>

            <flux:tab name="funding-show-projects"
                      icon="folder-open"
                      wire:click="setSelectedTab('funding-show-projects')"
            ><span class="hidden md:inline">{{ __('fundings.tabs.projects') }}</span></flux:tab>

            <flux:tab name="funding-show-positions"
                      icon="rectangle-stack"
                      wire:click="setSelectedTab('funding-show-positions')"
            ><span class="hidden md:inline">{{ __('fundings.tabs.positions') }}</span></flux:tab>

            <flux:tab name="funding-show-documents"
                      icon="folder-open"
                      wire:click="setSelectedTab('funding-show-documents')"
            ><span class="hidden md:inline">{{ __('fundings.tabs.documents') }}</span></flux:tab>
        </flux:tabs>

        {{-- ================================================================ --}}
        {{-- Tab: Details                                                      --}}
        {{-- ================================================================ --}}
        <flux:tab.panel name="funding-show-details">
            <form wire:submit="updateFunding">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

                    <section class="space-y-6">

                        <flux:input wire:model.blur="form.title"
                                    label="{{ __('fundings.form.title') }}"
                        />

                        <flux:input wire:model.blur="form.funder"
                                    label="{{ __('fundings.form.funder') }}"
                        />

                        <flux:input wire:model.blur="form.reference"
                                    label="{{ __('fundings.form.reference') }}"
                                    description="{{ __('fundings.form.reference_hint') }}"
                        />

                        <flux:select wire:model="form.status"
                                     variant="listbox"
                                     label="{{ __('fundings.form.status') }}"
                        >
                            @foreach(\App\Enums\FundingStatus::cases() as $s)
                                <flux:select.option value="{{ $s->value }}">
                                    <flux:badge color="{{ $s->color() }}">{{ $s->label() }}</flux:badge>
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:textarea wire:model.blur="form.description"
                                       rows="auto"
                                       label="{{ __('fundings.form.description') }}"
                        />

                    </section>

                    <section class="space-y-6">

                        <flux:field>
                            <flux:label>{{ __('fundings.form.approved_amount') }}</flux:label>
                            <flux:input.group>
                                <flux:input wire:model.blur="form.approved_amount"
                                            placeholder="0,00"
                                            x-mask:dynamic="$money($input, ',', '.')"
                                />
                                <flux:input.group.suffix>{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</flux:input.group.suffix>
                            </flux:input.group>
                            <flux:error name="form.approved_amount"/>
                        </flux:field>

                        <flux:date-picker locale="{{ app()->getLocale() }}" wire:model.blur="form.funding_period_start"
                                          with-today
                                          selectable-header
                                          label="{{ __('fundings.form.period_start') }}"
                        />

                        <flux:date-picker locale="{{ app()->getLocale() }}" wire:model.blur="form.funding_period_end"
                                          with-today
                                          selectable-header
                                          label="{{ __('fundings.form.period_end') }}"
                        />

                    </section>
                </div>

                <div class="flex gap-3 mt-6">
                    @can('update', $funding)
                        <flux:button type="submit" variant="primary">
                            {{ __('fundings.form.btn.save') }}
                        </flux:button>
                    @endcan

                    @can('delete', $funding)
                        <flux:button variant="danger"
                                     icon="trash"
                                     wire:click="deleteFunding"
                                     wire:confirm="{{ __('fundings.form.confirm.delete') }}"
                        >{{ __('fundings.form.btn.delete') }}</flux:button>
                    @endcan
                </div>
            </form>
        </flux:tab.panel>

        {{-- ================================================================ --}}
        {{-- Tab: Zahlungseingänge                                             --}}
        {{-- ================================================================ --}}
        <flux:tab.panel name="funding-show-receipts">

            <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-zinc-900 rounded-lg mb-6">
                <div>
                    <div class="text-sm text-gray-500">{{ __('fundings.receipts.stat.approved') }}</div>
                    <div class="text-lg font-semibold text-blue-600">
                        {{ \App\Helpers\MoneyHelper::formatCents($this->approvedAmount) }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">{{ __('fundings.receipts.stat.received') }}</div>
                    <div class="text-lg font-semibold text-green-600">
                        {{ \App\Helpers\MoneyHelper::formatCents($this->totalReceived) }}
                    </div>
                </div>
                <div>
                    @php $remaining = $this->approvedAmount - $this->totalReceived; @endphp
                    <div class="text-sm text-gray-500">{{ __('fundings.receipts.stat.remaining') }}</div>
                    <div class="text-lg font-semibold {{ $remaining <= 0 ? 'text-green-600' : 'text-amber-600' }}">
                        {{ \App\Helpers\MoneyHelper::formatCents($remaining) }}
                    </div>
                </div>
            </div>

            <flux:table :paginate="$this->transactions">
                <flux:table.columns>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'created_at'"
                                       :direction="$sortDirection"
                                       wire:click="sort('created_at')"
                    >{{ __('fundings.receipts.table.date') }}</flux:table.column>

                    <flux:table.column>{{ __('fundings.receipts.table.label') }}</flux:table.column>

                    <flux:table.column class="hidden lg:table-cell">
                        {{ __('fundings.receipts.table.allocated') }}
                    </flux:table.column>

                    <flux:table.column align="right">
                        {{ __('fundings.receipts.table.amount') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->transactions as $ft)
                        @if($ft->transaction)
                            <flux:table.row :key="$ft->id">

                                <flux:table.cell>
                                    {{ $ft->transaction->date?->isoFormat('LL') ?? '-' }}
                                </flux:table.cell>

                                <flux:table.cell variant="strong">
                                    {{ $ft->transaction->label }}
                                    @if($ft->fundingPosition)
                                        <div class="mt-1">
                                            <flux:badge size="sm" color="blue">{{ $ft->fundingPosition->title }}</flux:badge>
                                        </div>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell class="hidden lg:table-cell">
                                    @if($ft->allocated_amount !== null)
                                        <flux:badge size="sm" color="blue">
                                            {{ \App\Helpers\MoneyHelper::formatCents($ft->allocated_amount) }}
                                        </flux:badge>
                                    @else
                                        <span class="text-gray-400 text-sm">{{ __('fundings.receipts.table.full_amount') }}</span>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell align="end" variant="strong">
                                    <span class="text-green-600">
                                        {{ \App\Helpers\MoneyHelper::formatCents($ft->effectiveAmount()) }}
                                    </span>
                                </flux:table.cell>

                            </flux:table.row>
                        @endif
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4">{{ __('fundings.receipts.empty') }}</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

        </flux:tab.panel>

        {{-- ================================================================ --}}
        {{-- Tab: Projekte (Verwendungsnachweis)                               --}}
        {{-- ================================================================ --}}
        <flux:tab.panel name="funding-show-projects">

            <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-zinc-900 rounded-lg mb-6">
                <div>
                    <div class="text-sm text-gray-500">{{ __('fundings.projects.stat.approved') }}</div>
                    <div class="text-lg font-semibold text-blue-600">
                        {{ \App\Helpers\MoneyHelper::formatCents($this->approvedAmount) }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">{{ __('fundings.projects.stat.allocated') }}</div>
                    <div class="text-lg font-semibold text-amber-600">
                        {{ \App\Helpers\MoneyHelper::formatCents($this->totalAllocated) }}
                    </div>
                </div>
                <div>
                    @php $unallocated = $this->approvedAmount - $this->totalAllocated; @endphp
                    <div class="text-sm text-gray-500">{{ __('fundings.projects.stat.unallocated') }}</div>
                    <div class="text-lg font-semibold {{ $unallocated <= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ \App\Helpers\MoneyHelper::formatCents($unallocated) }}
                    </div>
                </div>
            </div>

            @can('update', $funding)
                <flux:modal.trigger name="link-project-modal">
                    <flux:button variant="primary" size="sm" icon="plus">
                        {{ __('fundings.link_project.btn.open') }}
                    </flux:button>
                </flux:modal.trigger>
            @endcan

            <flux:table class="mt-4">
                <flux:table.columns>
                    <flux:table.column>{{ __('fundings.projects.table.title') }}</flux:table.column>
                    <flux:table.column>{{ __('fundings.projects.table.status') }}</flux:table.column>
                    <flux:table.column class="hidden lg:table-cell">{{ __('fundings.projects.table.period') }}</flux:table.column>
                    <flux:table.column align="right">{{ __('fundings.projects.table.allocated') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->projects as $project)
                        <flux:table.row :key="$project->id">

                            <flux:table.cell variant="strong">
                                <a class="underline text-emerald-600"
                                   href="{{ route('project.show', $project) }}"
                                >{{ $project->title }}</a>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" color="{{ $project->status->color() }}">
                                    {{ $project->status->label() }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell class="hidden lg:table-cell">
                                @if($project->start_date)
                                    <span class="text-sm">
                                        {{ $project->start_date->isoFormat('DD.MM.YY') }}
                                        @if($project->end_date)
                                            – {{ $project->end_date->isoFormat('DD.MM.YY') }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="end" variant="strong">
                                @if($project->pivot->allocated_amount)
                                    {{ \App\Helpers\MoneyHelper::formatCents($project->pivot->allocated_amount) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                @can('update', $funding)
                                    <flux:dropdown>
                                        <flux:button variant="ghost"
                                                     size="sm"
                                                     icon="ellipsis-horizontal"
                                                     inset="top bottom"
                                        />
                                        <flux:menu>
                                            <flux:menu.item
                                                    icon="pencil-square"
                                                    wire:click="openEditProject({{ $project->id }}, {{ $project->pivot->allocated_amount ?? 0 }})"
                                            >{{ __('fundings.link_project.menu.edit') }}</flux:menu.item>

                                            <flux:menu.separator/>

                                            <flux:menu.item
                                                    variant="danger"
                                                    icon="link-slash"
                                                    wire:click="detachProject({{ $project->id }})"
                                                    wire:confirm="{{ __('fundings.link_project.menu.detach_confirm') }}"
                                            >{{ __('fundings.link_project.menu.detach') }}</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                @endcan
                            </flux:table.cell>

                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">{{ __('fundings.projects.empty') }}</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

        </flux:tab.panel>

        {{-- ================================================================ --}}
        {{-- Tab: Positionen (Plan/Ist je Förderposition)                      --}}
        {{-- ================================================================ --}}
        <flux:tab.panel name="funding-show-positions">

            @if($this->positionsBudgetExceeded)
                <flux:callout variant="warning" icon="exclamation-triangle" class="mb-6">
                    <flux:callout.heading>{{ __('fundings.positions.warning.budget_exceeded.heading') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('fundings.positions.warning.budget_exceeded.text', [
                            'sum' => \App\Helpers\MoneyHelper::formatCents($this->positionsBudgetSum),
                            'approved' => \App\Helpers\MoneyHelper::formatCents($this->approvedAmount),
                        ]) }}
                    </flux:callout.text>
                </flux:callout>
            @endif

            @can('update', $funding)
                <div class="flex flex-wrap gap-2 mb-4">
                    <flux:button variant="primary" size="sm" icon="plus" wire:click="editPosition">
                        {{ __('fundings.positions.btn.create') }}
                    </flux:button>
                </div>
            @endcan

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('fundings.positions.table.title') }}</flux:table.column>
                    <flux:table.column class="hidden lg:table-cell">{{ __('fundings.positions.table.category') }}</flux:table.column>
                    <flux:table.column align="right">{{ __('fundings.positions.table.budget') }}</flux:table.column>
                    <flux:table.column align="right">{{ __('fundings.positions.table.actual') }}</flux:table.column>
                    <flux:table.column align="right" class="hidden md:table-cell">{{ __('fundings.positions.table.remaining') }}</flux:table.column>
                    <flux:table.column class="hidden lg:table-cell">{{ __('fundings.positions.table.due_date') }}</flux:table.column>
                    <flux:table.column class="hidden lg:table-cell">{{ __('fundings.positions.table.responsible') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->positions as $position)
                        @php
                            $actual = $position->actualAmount();
                            $remaining = $position->budget - $actual;
                        @endphp
                        <flux:table.row :key="$position->id">

                            <flux:table.cell variant="strong">
                                {{ $position->title }}
                                @if($position->description)
                                    <div class="text-xs text-gray-400 font-normal">{{ \Illuminate\Support\Str::limit($position->description, 60) }}</div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell class="hidden lg:table-cell">
                                @if($position->category)
                                    <flux:badge size="sm" color="{{ $position->category->is_system ? 'zinc' : 'blue' }}">
                                        {{ $position->category->name }}
                                    </flux:badge>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="end">
                                {{ \App\Helpers\MoneyHelper::formatCents($position->budget) }}
                            </flux:table.cell>

                            <flux:table.cell align="end" variant="strong">
                                {{ \App\Helpers\MoneyHelper::formatCents($actual) }}
                            </flux:table.cell>

                            <flux:table.cell align="end" class="hidden md:table-cell">
                                <span class="{{ $remaining < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ \App\Helpers\MoneyHelper::formatCents($remaining) }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell class="hidden lg:table-cell">
                                {{ $position->due_date?->isoFormat('DD.MM.YY') ?? '-' }}
                            </flux:table.cell>

                            <flux:table.cell class="hidden lg:table-cell">
                                {{ $position->responsible?->fullName() ?? '-' }}
                            </flux:table.cell>

                            <flux:table.cell>
                                @can('update', $funding)
                                    <flux:dropdown>
                                        <flux:button variant="ghost"
                                                     size="sm"
                                                     icon="ellipsis-horizontal"
                                                     inset="top bottom"
                                        />
                                        <flux:menu>
                                            <flux:menu.item
                                                    icon="pencil-square"
                                                    wire:click="editPosition({{ $position->id }})"
                                            >{{ __('fundings.positions.menu.edit') }}</flux:menu.item>

                                            <flux:menu.separator/>

                                            <flux:menu.item
                                                    variant="danger"
                                                    icon="trash"
                                                    wire:click="deletePosition({{ $position->id }})"
                                                    wire:confirm="{{ __('fundings.positions.menu.delete_confirm') }}"
                                            >{{ __('fundings.positions.menu.delete') }}</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                @endcan
                            </flux:table.cell>

                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8">{{ __('fundings.positions.empty') }}</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            {{-- Kategorie-Verwaltung (Admin): System read-only, Custom ergänzbar --}}
            @if(auth()->user()?->is_admin)
                <flux:separator class="my-8"/>

                <flux:heading size="sm" class="mb-4">{{ __('fundings.positions.categories.heading') }}</flux:heading>

                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($this->positionCategories as $category)
                        <flux:badge size="sm" color="{{ $category->is_system ? 'zinc' : 'blue' }}">
                            {{ $category->name }}
                            @if($category->is_system)
                                <span class="text-xs opacity-70">({{ __('fundings.positions.categories.system_badge') }})</span>
                            @endif
                        </flux:badge>
                        @if(! $category->is_system)
                            <flux:button variant="ghost"
                                         size="sm"
                                         icon="trash"
                                         wire:click="deleteCategory({{ $category->id }})"
                                         wire:confirm="{{ __('fundings.positions.categories.delete_confirm') }}"
                            />
                        @endif
                    @endforeach
                </div>

                <form wire:submit="addCategory" class="flex items-end gap-3 max-w-md">
                    <flux:input wire:model="newCategoryName"
                                label="{{ __('fundings.positions.categories.new_label') }}"
                                placeholder="{{ __('fundings.positions.categories.new_placeholder') }}"
                    />
                    <flux:button type="submit" size="sm" variant="primary" icon="plus">
                        {{ __('fundings.positions.categories.btn.add') }}
                    </flux:button>
                </form>
            @endif

        </flux:tab.panel>

        <flux:tab.panel name="funding-show-documents">
            @can('update', $funding)
                <div class="flex flex-wrap gap-2 mb-4">
                    <flux:button size="sm" variant="primary" icon="document-text" wire:click="createExecutiveReport">
                        {{ __('fundings.reports.actions.executive') }}
                    </flux:button>
                    <flux:button size="sm" variant="ghost" icon="document-chart-bar" wire:click="createDetailedReport">
                        {{ __('fundings.reports.actions.detailed') }}
                    </flux:button>
                    <flux:button size="sm" variant="ghost" icon="rectangle-stack" wire:click="createStatusReport">
                        {{ __('fundings.reports.actions.statusbericht') }}
                    </flux:button>
                </div>
            @endcan

            <livewire:app.global.documents
                    :model="$funding"
                    :category-enum="\App\Enums\FundingDocumentCategory::class"
                    :key="'funding-documents-'.$funding->id.'-'.$funding->documents()->count()"
            />
        </flux:tab.panel>

    </flux:tab.group>

    {{-- Modal: Projekt verknüpfen / bearbeiten --}}
    <flux:modal name="link-project-modal"
                variant="flyout"
                position="right"
                class="space-y-6"
    >
        <livewire:accounting.funding.link-project-form :funding="$funding"/>
    </flux:modal>

    {{-- Modal: Position anlegen / bearbeiten --}}
    <flux:modal name="funding-position-modal"
                variant="flyout"
                position="right"
                class="space-y-6"
    >
        <flux:heading class="my-4">
            {{ $positionForm->id ? __('fundings.positions.modal.heading_edit') : __('fundings.positions.modal.heading_create') }}
        </flux:heading>

        <form wire:submit="savePosition" class="space-y-6">

            <flux:input wire:model.blur="positionForm.title"
                        label="{{ __('fundings.positions.form.title') }}"
            />
            <flux:error name="positionForm.title"/>

            <flux:field>
                <flux:label>{{ __('fundings.positions.form.budget') }}</flux:label>
                <flux:description>{{ __('fundings.positions.form.budget_hint') }}</flux:description>
                <flux:input.group>
                    <flux:input wire:model.blur="positionForm.budget"
                                placeholder="0,00"
                                x-mask:dynamic="$money($input, ',', '.')"
                    />
                    <flux:input.group.suffix>{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</flux:input.group.suffix>
                </flux:input.group>
                <flux:error name="positionForm.budget"/>
            </flux:field>

            <flux:select wire:model="positionForm.funding_position_category_id"
                         variant="listbox"
                         label="{{ __('fundings.positions.form.category') }}"
                         placeholder="{{ __('fundings.positions.form.category_placeholder') }}"
            >
                @foreach($this->positionCategories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="positionForm.funding_position_category_id"/>

            <flux:select wire:model="positionForm.member_id"
                         variant="listbox"
                         searchable
                         label="{{ __('fundings.positions.form.responsible') }}"
                         placeholder="{{ __('fundings.positions.form.responsible_placeholder') }}"
            >
                @foreach($this->members as $member)
                    <flux:select.option value="{{ $member->id }}">{{ $member->fullName() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="positionForm.member_id"/>

            <flux:date-picker locale="{{ app()->getLocale() }}" wire:model.blur="positionForm.due_date"
                              with-today
                              selectable-header
                              label="{{ __('fundings.positions.form.due_date') }}"
                              clearable
            />
            <flux:error name="positionForm.due_date"/>

            <flux:textarea wire:model.blur="positionForm.description"
                           rows="auto"
                           label="{{ __('fundings.positions.form.description') }}"
            />
            <flux:error name="positionForm.description"/>

            <flux:button variant="primary" type="submit">
                {{ __('fundings.positions.form.btn.save') }}
            </flux:button>
        </form>
    </flux:modal>

</div>
