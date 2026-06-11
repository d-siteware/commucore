
<div class="space-y-6">

    <flux:heading size="lg">{{__('post.backend.index.page.title')}}</flux:heading>

    <nav class="flex gap-3 items-center">
        @can('create',\App\Models\Blog\Post::class)
            <flux:button href="{{ route('backend.posts.create') }}"
                         variant="primary"
                         icon-trailing="plus"
                         size="sm"
            ><span class="hidden lg:inline">{{ __('post.backend.index.btn.start_new') }}</span>
            </flux:button>
            <flux:separator vertical/>
        @endcan


        <flux:input size="sm"
                    wire:model.live.debounce="search"
                    clearable
                    icon="magnifying-glass"
                    placeholder="{{ __('common.search') }}"
        />
        <flux:separator vertical/>
        <flux:select variant="listbox"
                     multiple
                     placeholder="{{ __('post.backend.index.filter_status') }}"
                     size="sm"
                     wire:model.live="filteredByStatus"
                     selected-suffix="{{ __('common.selected') }}"
        >
            @foreach(App\Enums\EventStatus::options() as $status => $label)
                <flux:select.option value="{{ $status }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select variant="listbox"
                     multiple
                     placeholder="{{ __('post.backend.index.filter_type') }}"
                     size="sm"
                     wire:model.live="filteredByType"
                     selected-suffix="{{ __('common.selected') }}"
        >
            @foreach(\App\Models\Blog\PostType::query()->select('id', 'name')->get() as $type)
                <flux:select.option value="{{ $type->id }}">{{ $type->name[$locale] }}</flux:select.option>
            @endforeach
        </flux:select>

    </nav>

    <flux:table :paginate="$this->posts">
        <flux:table.columns>
            <flux:table.column sortable
                               :sorted="$sortBy === 'label'"
                               :direction="$sortDirection"
                               wire:click="sort('label')"
            >{{ __('post.backend.index.header_label') }}
            </flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'date'"
                               :direction="$sortDirection"
                               wire:click="sort('published_at')"
            >{{ __('post.backend.index.header_published') }}
            </flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'status'"
                               :direction="$sortDirection"
                               wire:click="sort('status')"
            >{{ __('post.backend.index.header_status') }}
            </flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'type'"
                               :direction="$sortDirection"
                               wire:click="sort('type')"
            >{{ __('post.backend.index.header_type') }}
            </flux:table.column>
            <flux:table.column sortable
                               :sorted="$sortBy === 'title'"
                               :direction="$sortDirection"
                               wire:click="sort('title')"
            >{{ __('post.backend.index.header_title') }}
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->posts as $post)

                <flux:table.row :key="$post->id">
                    <flux:table.cell class="text-wrap hyphens-auto">
                        {{ $post->label }}
                    </flux:table.cell>

                    <flux:table.cell>{{ $post->published_at??'-' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm"
                                    :color="$post->status_color()"
                                    inset="top bottom"
                        >{{ $post->status }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>

                        <flux:badge size="sm"
                                    :color="$post->typeColor()"
                                    inset="top bottom"
                        >{{ $post->type->name[$locale] }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell variant="strong">{{ $post->title[$locale] }}</flux:table.cell>
                    @can('update',$post)
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost"
                                             size="sm"
                                             icon="ellipsis-horizontal"
                                             inset="top bottom"
                                ></flux:button>
                                <flux:menu>
                                    <flux:menu.item wire:navigate
                                                    href="{{ route('backend.posts.show',$post) }}"
                                                    icon="pencil-square"
                                    >{{ __('post.backend.index.action_edit') }}
                                    </flux:menu.item>
                                    @can('delete',$post)
                                        <flux:menu.item variant="danger"
                                                        wire:click="confirmDeletion({{ $post->id }})"
                                                        wire:confirm="{{__('post.show.delete.confirm_prompt')}}"
                                                        icon="trash"
                                        >{{ __('post.backend.index.action_delete') }}
                                        </flux:menu.item>
                                    @endcan
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    @endcan
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>


</div>
