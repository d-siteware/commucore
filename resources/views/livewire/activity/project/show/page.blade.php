<div>
    <flux:heading class="lg:mb-9 lg:hidden" size="lg">{{ __('projects.show.page.title') }}</flux:heading>
    <flux:heading class="lg:mb-9 lg:flex hidden">{{ __('projects.show.page.title') }}</flux:heading>
    <flux:subheading size="xl">{{ $form->title }}</flux:subheading>

    <flux:tab.group class="mt-3">
        <flux:tabs wire:model="selectedTab">
            <flux:tab name="project-show-details"
                      icon="folder-open"
                      wire:click="setSelectedTab('project-show-details')"
            ><span class="hidden md:inline">{{ __('projects.tabs.details') }}</span></flux:tab>

            <flux:tab name="project-show-fundings"
                      icon="building-library"
                      wire:click="setSelectedTab('project-show-fundings')"
            ><span class="hidden md:inline">{{ __('projects.tabs.fundings') }}</span></flux:tab>

            <flux:tab name="project-show-financials"
                      icon="banknotes"
                      wire:click="setSelectedTab('project-show-financials')"
            ><span class="hidden md:inline">{{ __('projects.tabs.financials') }}</span></flux:tab>

            <flux:tab name="project-show-posts"
                      icon="document-text"
                      wire:click="setSelectedTab('project-show-posts')"
            ><span class="hidden md:inline">{{ __('projects.tabs.posts') }}</span></flux:tab>
            <flux:tab name="project-show-documents" icon="paper-clip" wire:click="setSelectedTab('project-show-documents')">
                {{ __('projects.tabs.documents') }}
            </flux:tab>
        </flux:tabs>

        {{-- ================================================================ --}}
        {{-- Tab: Details                                                     --}}
        {{-- ================================================================ --}}
        <flux:tab.panel name="project-show-details">
            <form wire:submit="updateProject">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <section class="space-y-6">
                        <flux:input wire:model="form.title"
                                    label="{{ __('projects.form.title') }}"
                        />
                        <flux:textarea wire:model="form.description"
                                       rows="auto"
                                       label="{{ __('projects.form.description') }}"
                        />
                        <flux:select wire:model="form.status"
                                     variant="listbox"
                                     label="{{ __('projects.form.status') }}"
                        >
                            @foreach(\App\Enums\ProjectStatus::cases() as $s)
                                <flux:select.option value="{{ $s->value }}">
                                    <flux:badge color="{{ $s->color() }}">{{ $s->label() }}</flux:badge>
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </section>
                    <section class="space-y-6">
                        <flux:date-picker locale="{{ app()->getLocale() }}" wire:model="form.start_date"
                                          with-today
                                          selectable-header
                                          label="{{ __('projects.form.start_date') }}"
                        />
                        <flux:date-picker locale="{{ app()->getLocale() }}" wire:model="form.end_date"
                                          with-today
                                          selectable-header
                                          label="{{ __('projects.form.end_date') }}"
                        />
                    </section>
                </div>
                <div class="flex gap-3 mt-6">
                    @can('update', $project)
                        <flux:button type="submit" variant="primary">
                            {{ __('projects.form.btn.save') }}
                        </flux:button>
                    @endcan
                    @can('delete', $project)
                        <flux:button variant="danger"
                                     icon="trash"
                                     wire:click="deleteProject"
                                     wire:confirm="{{ __('projects.form.confirm.delete') }}"
                        >{{ __('projects.form.btn.delete') }}</flux:button>
                    @endcan
                </div>
            </form>
        </flux:tab.panel>

        {{-- ================================================================ --}}
        {{-- Tab: Förderungen                                                 --}}
        {{-- ================================================================ --}}
        <flux:tab.panel name="project-show-fundings">
            <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-zinc-900 rounded-lg mb-6">
                <div>
                    <div class="text-sm text-gray-500">{{ __('projects.fundings.stat.allocated') }}</div>
                    <div class="text-lg font-semibold text-blue-600">
                        {{ \App\Helpers\MoneyHelper::formatCents($this->fundingAllocated) }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">{{ __('projects.fundings.stat.expense') }}</div>
                    <div class="text-lg font-semibold text-red-600">
                        {{ \App\Helpers\MoneyHelper::formatCents($this->totalExpense) }}
                    </div>
                </div>
                <div>
                    @php
                        $coverage = $this->totalExpense > 0
                            ? round(($this->fundingAllocated / $this->totalExpense) * 100, 1)
                            : 0;
                    @endphp
                    <div class="text-sm text-gray-500">{{ __('projects.fundings.stat.coverage') }}</div>
                    <div class="text-lg font-semibold {{ $coverage >= 100 ? 'text-green-600' : 'text-amber-600' }}">
                        {{ $coverage }} %
                    </div>
                </div>
            </div>

            @can('update', $project)
                <flux:modal.trigger name="link-funding-modal">
                    <flux:button variant="primary" size="sm" icon="plus">
                        {{ __('projects.link_funding.btn.open') }}
                    </flux:button>
                </flux:modal.trigger>
            @endcan

            <flux:table class="mt-4">
                <flux:table.columns>
                    <flux:table.column>{{ __('projects.fundings.table.title') }}</flux:table.column>
                    <flux:table.column class="hidden sm:table-cell">{{ __('projects.fundings.table.funder') }}</flux:table.column>
                    <flux:table.column>{{ __('projects.fundings.table.status') }}</flux:table.column>
                    <flux:table.column align="right">{{ __('projects.fundings.table.allocated') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($this->fundings as $funding)
                        <flux:table.row :key="$funding->id">
                            <flux:table.cell variant="strong">
                                <a class="underline text-emerald-600"
                                   href="{{ route('funding.show', $funding) }}"
                                >{{ $funding->title }}</a>
                            </flux:table.cell>
                            <flux:table.cell class="hidden sm:table-cell">
                                {{ $funding->funder }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="{{ $funding->status->color() }}">
                                    {{ $funding->status->label() }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="end" variant="strong">
                                @if($funding->pivot->allocated_amount)
                                    {{ \App\Helpers\MoneyHelper::formatCents($funding->pivot->allocated_amount) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @can('update', $project)
                                    <flux:dropdown>
                                        <flux:button variant="ghost"
                                                     size="sm"
                                                     icon="ellipsis-horizontal"
                                                     inset="top bottom"
                                        />
                                        <flux:menu>
                                            <flux:menu.item
                                                    icon="pencil-square"
                                                    wire:click="openEditFunding({{ $funding->id }}, {{ $funding->pivot->allocated_amount ?? 0 }})"
                                            >{{ __('projects.link_funding.menu.edit') }}</flux:menu.item>
                                            <flux:menu.separator/>
                                            <flux:menu.item
                                                    variant="danger"
                                                    icon="link-slash"
                                                    wire:click="detachFunding({{ $funding->id }})"
                                                    wire:confirm="{{ __('projects.link_funding.menu.detach_confirm') }}"
                                            >{{ __('projects.link_funding.menu.detach') }}</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">{{ __('projects.fundings.empty') }}</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:tab.panel>

        {{-- ================================================================ --}}
        {{-- Tab: Finanzen                                                    --}}
        {{-- ================================================================ --}}
        <flux:tab.panel name="project-show-financials">
            <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-zinc-900 rounded-lg mb-6">
                <div>
                    <div class="text-sm text-gray-500">{{ __('projects.financials.income') }}</div>
                    <div class="text-lg font-semibold text-green-600">
                        {{ \App\Helpers\MoneyHelper::formatCents($this->totalIncome) }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">{{ __('projects.financials.expense') }}</div>
                    <div class="text-lg font-semibold text-red-600">
                        {{ \App\Helpers\MoneyHelper::formatCents($this->totalExpense) }}
                    </div>
                </div>
                <div>
                    @php $balance = $this->totalIncome - $this->totalExpense; @endphp
                    <div class="text-sm text-gray-500">{{ __('projects.financials.balance') }}</div>
                    <div class="text-lg font-semibold {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ \App\Helpers\MoneyHelper::formatCents($balance) }}
                    </div>
                </div>
            </div>

            <flux:table :paginate="$this->transactions">
                <flux:table.columns>
                    <flux:table.column sortable
                                       :sorted="$sortBy === 'created_at'"
                                       :direction="$sortDirection"
                                       wire:click="sort('created_at')"
                    >{{ __('projects.financials.table.date') }}</flux:table.column>
                    <flux:table.column>{{ __('projects.financials.table.label') }}</flux:table.column>
                    <flux:table.column class="hidden sm:table-cell">{{ __('projects.financials.table.type') }}</flux:table.column>
                    <flux:table.column class="hidden lg:table-cell">{{ __('projects.financials.table.allocated') }}</flux:table.column>
                    <flux:table.column align="right">{{ __('projects.financials.table.amount') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($this->transactions as $pt)
                        @if($pt->transaction)
                            <flux:table.row :key="$pt->id">
                                <flux:table.cell>{{ $pt->transaction->date?->isoFormat('LL') ?? '-' }}</flux:table.cell>
                                <flux:table.cell variant="strong">{{ $pt->transaction->label }}</flux:table.cell>
                                <flux:table.cell class="hidden sm:table-cell">
                                    <flux:badge size="sm" color="{{ $pt->transaction->type->color() }}">
                                        {{ $pt->transaction->type->label() }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="hidden lg:table-cell">
                                    @if($pt->allocated_amount !== null)
                                        <flux:badge size="sm" color="blue">
                                            {{ \App\Helpers\MoneyHelper::formatCents($pt->allocated_amount) }}
                                        </flux:badge>
                                    @else
                                        <span class="text-gray-400 text-sm">{{ __('projects.financials.table.full_amount') }}</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell align="end" variant="strong">
                                    <span class="text-{{ $pt->transaction->type->color() }}-600">
                                        {{ \App\Helpers\MoneyHelper::formatCents($pt->effectiveAmount()) }}
                                    </span>
                                </flux:table.cell>
                            </flux:table.row>
                        @endif
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">{{ __('projects.financials.empty') }}</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:tab.panel>

        {{-- ================================================================ --}}
        {{-- Tab: Blog-Posts                                                  --}}
        {{-- ================================================================ --}}
        <flux:tab.panel name="project-show-posts">
            @can('create', \App\Models\Blog\Post::class)
                <flux:button variant="primary" size="sm" icon="plus"
                             href="{{ route('backend.posts.create', ['project_id' => $project->id]) }}"
                >{{ __('projects.posts.btn.create') }}</flux:button>
            @endcan
            <flux:table :paginate="$this->posts" class="mt-4">
                <flux:table.columns>
                    <flux:table.column>{{ __('projects.posts.table.title') }}</flux:table.column>
                    <flux:table.column class="hidden sm:table-cell">{{ __('projects.posts.table.author') }}</flux:table.column>
                    <flux:table.column>{{ __('projects.posts.table.status') }}</flux:table.column>
                    <flux:table.column class="hidden lg:table-cell">{{ __('projects.posts.table.published_at') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($this->posts as $post)
                        <flux:table.row :key="$post->id">
                            <flux:table.cell variant="strong">
                                <a class="underline text-emerald-600"
                                   href="{{ route('backend.posts.show', $post) }}"
                                >{{ $post->title[app()->getLocale()] ?? '-' }}</a>
                            </flux:table.cell>
                            <flux:table.cell class="hidden sm:table-cell">{{ $post->user->name ?? '-' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="{{ $post->isPublished() ? 'green' : 'zinc' }}">
                                    {{ $post->isPublished() ? __('post.status.published') : __('post.status.draft') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="hidden lg:table-cell">
                                {{ $post->published_at?->isoFormat('LL') ?? '-' }}
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4">{{ __('projects.posts.empty') }}</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:tab.panel>

        {{-- ================================================================ --}}
        {{-- Tab: Dokumente                                                   --}}
        {{-- ================================================================ --}}
        <flux:tab.panel name="project-show-documents">
            <livewire:app.global.documents
                    :model="$project"
                    :category-enum="\App\Enums\ProjectDocumentCategory::class"
                    :key="'project-documents-'.$project->id"
            />
        </flux:tab.panel>

    </flux:tab.group>

    {{-- Modal: Förderung verknüpfen / bearbeiten --}}
    <flux:modal name="link-funding-modal"
                variant="flyout"
                position="right"
                class="space-y-6"
    >
        <livewire:activity.project.link-funding-form :project="$project"/>
    </flux:modal>

</div>