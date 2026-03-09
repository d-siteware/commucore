<div class="space-y-6">

    <flux:heading size="xl">{{ __('projects.page.title') }}</flux:heading>

    <nav class="flex flex-wrap lg:flex-nowrap gap-3 items-center">

        <flux:input size="sm"
                    wire:model.live.debounce="search"
                    clearable
                    icon="magnifying-glass"
                    placeholder="{{ __('projects.index.search_placeholder') }}"
        />

        @can('create', \App\Models\Project\Project::class)
            <flux:button href="{{ route('project.create') }}"
                         variant="primary"
                         icon="plus"
                         size="sm"
            >
                <span class="hidden lg:inline">{{ __('projects.index.btn.create') }}</span>
            </flux:button>
        @endcan

        <flux:select variant="listbox"
                     multiple
                     placeholder="{{ __('app.filter.placeholder') }}"
                     size="sm"
                     wire:model.live="filteredBy"
                     selected-suffix="{{ __('app.filter.selected') }}"
                     class="flex-1 lg:flex lg:shrink-2"
        >
            @foreach(\App\Enums\ProjectStatus::options() as $value => $label)
                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

    </nav>

    <flux:table :paginate="$this->projects">
        <flux:table.columns>
            <flux:table.column sortable
                               :sorted="$sortBy === 'title'"
                               :direction="$sortDirection"
                               wire:click="sort('title')"
            >{{ __('projects.index.table.title') }}</flux:table.column>

            <flux:table.column sortable
                               :sorted="$sortBy === 'status'"
                               :direction="$sortDirection"
                               wire:click="sort('status')"
            >{{ __('projects.index.table.status') }}</flux:table.column>

            <flux:table.column sortable
                               :sorted="$sortBy === 'start_date'"
                               :direction="$sortDirection"
                               wire:click="sort('start_date')"
                               class="hidden lg:table-cell"
            >{{ __('projects.index.table.start_date') }}</flux:table.column>

            <flux:table.column sortable
                               :sorted="$sortBy === 'end_date'"
                               :direction="$sortDirection"
                               wire:click="sort('end_date')"
                               class="hidden lg:table-cell"
            >{{ __('projects.index.table.end_date') }}</flux:table.column>

            <flux:table.column class="hidden sm:table-cell">
                {{ __('projects.index.table.fundings') }}
            </flux:table.column>

            <flux:table.column class="hidden sm:table-cell">
                {{ __('projects.index.table.transactions') }}
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($this->projects as $project)
                <flux:table.row :key="$project->id">

                    <flux:table.cell variant="strong">
                        <a class="underline text-emerald-600"
                           href="{{ route('project.show', $project) }}"
                        >{{ \Illuminate\Support\Str::limit($project->title, 45, preserveWords: true) }}</a>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm"
                                    color="{{ $project->status->color() }}"
                        >{{ $project->status->label() }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="hidden lg:table-cell">
                        {{ $project->start_date?->isoFormat('LL') ?? '-' }}
                    </flux:table.cell>

                    <flux:table.cell class="hidden lg:table-cell">
                        {{ $project->end_date?->isoFormat('LL') ?? '-' }}
                    </flux:table.cell>

                    <flux:table.cell class="hidden sm:table-cell">
                        <flux:badge size="sm"
                                    color="{{ $project->fundings_count > 0 ? 'lime' : 'zinc' }}"
                        >{{ $project->fundings_count }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="hidden sm:table-cell">
                        <flux:badge size="sm"
                                    color="{{ $project->project_transactions_count > 0 ? 'blue' : 'zinc' }}"
                        >{{ $project->project_transactions_count }}</flux:badge>
                    </flux:table.cell>

                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

</div>