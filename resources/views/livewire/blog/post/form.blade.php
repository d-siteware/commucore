<div>
    <form wire:submit="save">
        <flux:tab.group>
            <flux:tabs wire:model="selectedTab">
                <flux:tab name="post-create-head-section-panel"
                          wire:click="setSelectedTab('post-create-head-section-panel')"
                >{{ __('post.show.tabs.header.main') }}
                </flux:tab>
                <flux:tab name="post-create-text-panel"
                          wire:click="setSelectedTab('post-create-text-panel')"
                >{{ __('post.show.tabs.header.content') }}
                </flux:tab>
                <flux:tab name="post-create-images-panel"
                          wire:click="setSelectedTab('post-create-images-panel')"
                >{{ __('post.show.tabs.header.images') }}
                </flux:tab>
            </flux:tabs>

            <flux:tab.panel name="post-create-head-section-panel">

                <section class="grid grid-cols-1 gap-3 lg:grid-cols-2 lg:gap-9">
                    <section class="space-y-6">

                        <x-input-with-counter
                            model="form.label"
                            label="{{ __('post.label') }}"
                            max-length="40"
                        />
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            <flux:select wire:model="form.post_type_id"
                                         label="{{ __('post.type.label') }}"
                            >
                                @foreach(\App\Models\Blog\PostType::query()->select('id','name')->get() as $type)
                                    <flux:select.option value="{{ $type->id }}">{{ $type->name[$locale] }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:select wire:model="form.status"
                                         label="{{ __('post.status') }}"
                            >
                                @foreach(\App\Enums\EventStatus::options() as $value => $label)
                                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                @endforeach
                            </flux:select>

                        </div>

                        @if($editPost)
                            @if($form->event_id === null)

                                <livewire:blog.post.event-selector :post="$post"/>

                            @else

                                <flux:callout icon="calendar-days">
                                    <flux:callout.heading>{{ __('post.show.tab.main.attached_event.header') }}</flux:callout.heading>
                                    <flux:callout.text>{{ __('post.show.tab.main.attached_event.status_msg',['title' => \App\Models\Event\Event::findOrFail($form->event_id)->name ] ) }}</flux:callout.text>
                                    <x-slot name="actions">
                                        <flux:button size="sm"
                                                     icon-trailing="arrow-uturn-left"
                                                     variant="ghost"
                                                     wire:click="detachFromEvent"
                                                     wire:confirm="{{ __('post.show.tab.main.detach_from_event.confirmation_msg') }}"
                                        >{{ __('post.show.tab.main.detach.btn_reset') }}</flux:button>
                                    </x-slot>
                                </flux:callout>

                            @endif

                        @endif

                        @if($form->published_at)

                            <flux:callout icon="check"
                                          variant="success"
                            >
                                <flux:callout.heading>{{ __('post.show.tab.main.published.header') }}</flux:callout.heading>
                                <flux:callout.text>{{ __('post.show.tab.main.published.status_msg', ['datum' => \Carbon\Carbon::createFromDate($form->published_at)->locale($locale)->isoFormat('LLLL')] ) }}</flux:callout.text>
                                <x-slot name="actions">
                                    <flux:button size="sm"
                                                 icon-trailing="arrow-uturn-left"
                                                 variant="ghost"
                                                 wire:click="resetPublication"
                                                 wire:confirm="{{ __('post.show.tab.main.published.confirmation_msg') }}"
                                    >{{ __('post.show.tab.main.published.btn_reset') }}</flux:button>
                                    <flux:button size="sm"
                                                 icon-trailing="megaphone"
                                                 variant="filled"
                                                 wire:click="sendPublicationNotification"
                                    >{{ __('post.show.tab.main.published.btn_sendMails') }}</flux:button>
                                </x-slot>
                            </flux:callout>

                        @elseif($form->id)

                            <flux:button variant="primary"
                                         icon-trailing="cloud-arrow-up"
                                         wire:click="publishPost"
                            >{{ __('post.show.tab.main.published.btn_publish_now') }}</flux:button>

                        @endif

                    </section>

                    <section class="space-y-6">
                        <flux:separator text="Titel"/>
                        <flux:text size="lg">{{ __('post.create.title_explanation') }}</flux:text>

                        <flux:tab.group>
                            <flux:tabs variant="segmented"
                            size="sm">
                                @foreach($this->locals as $locale)
                                    <flux:tab name="title-{{ $locale }}">{{ $locale }}</flux:tab>
                                @endforeach
                            </flux:tabs>
                            @foreach($this->locals as $locale)
                                <flux:tab.panel name="title-{{ $locale }}">
                                    <x-input-with-counter
                                            model="form.title.{{ $locale }}"
                                            label="{{ __('post.title') }}"
                                            max-length="60"
                                    />
                                </flux:tab.panel>
                            @endforeach

                        </flux:tab.group>


                        <flux:separator text="Slugs"/>

                        <flux:callout icon="exclamation-triangle"
                                      variant="warning"
                        >
                            <flux:callout.heading>{{ __('post.show.tab.main.btn_make_slug') }}</flux:callout.heading>
                            <flux:callout.text>{{ __('post.create.slug_explanation') }}</flux:callout.text>
                            <x-slot name="actions">
                                <flux:button wire:click="makeSlugs"
                                             variant="filled"
                                             size="sm"
                                >{{ __('post.show.tab.main.btn_make_slug') }}</flux:button>
                            </x-slot>
                        </flux:callout>

                        <flux:tab.group>
                            <flux:tabs variant="segmented"
                                       size="sm">
                                @foreach($this->locals as $locale)
                                    <flux:tab name="slug-{{ $locale }}">{{ $locale }}</flux:tab>
                                @endforeach
                            </flux:tabs>
                            @foreach($this->locals as $locale)
                                <flux:tab.panel name="slug-{{ $locale }}">
                                    <flux:input wire:model="form.slug.{{ $locale }}"
                                                label="{{ __('post.slug') }}"
                                    />
                                </flux:tab.panel>
                            @endforeach

                        </flux:tab.group>


                        @if(!app()->isProduction())
                            <x-debug/>
                            <flux:button wire:click="addDummyData">dummies</flux:button>
                        @endif
                        <flux:button variant="primary"
                                     type="submit"
                        >{{ __('post.show.btn.save') }}</flux:button>
                    </section>
                </section>
            </flux:tab.panel>

            <flux:tab.panel name="post-create-text-panel">
                <flux:tab.group class="mb-6">
                    <flux:tabs wire:model="tabsBody"
                               variant="segmented"
                               size="sm"
                    >
                        @foreach($this->locals as $locale)
                        <flux:tab name="body-{{$locale}}">{{$locale}}</flux:tab>
                        @endforeach
                    </flux:tabs>
                    @foreach($this->locals as $locale)
                    <flux:tab.panel name="body-{{ $locale }}">
                        <flux:error name="form.body.{{ $locale }}"/>
                        <flux:editor wire:model="form.body.{{ $locale }}"
                                     description="Editor für {{ $locale }} Text mit markdown Funktionalität"
                        >
                            <flux:editor.toolbar>
                                <flux:editor.heading/>
                                <flux:editor.separator/>
                                <flux:editor.bold/>
                                <flux:editor.italic/>
                                <flux:editor.separator/>
                                <flux:editor.align/>
                                <flux:editor.bullet/>
                                <flux:editor.blockquote/>
                                <flux:editor.spacer/>
                                <flux:dropdown position="bottom end"
                                               offset="-15"
                                >
                                    <flux:editor.button icon="ellipsis-horizontal"
                                                        tooltip="More"
                                    />
                                    <flux:menu>
                                        <flux:editor.strike/>
                                        <flux:editor.ordered/>
                                        <flux:editor.link/>
                                        <flux:modal.trigger name="show-md-keys">
                                            <flux:menu.item>Hilfe</flux:menu.item>
                                        </flux:modal.trigger>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:editor.toolbar>
                            <flux:editor.content/>
                        </flux:editor>
                    </flux:tab.panel>
                    @endforeach
                </flux:tab.group>
                <flux:button variant="primary"
                             type="submit"
                >{{__('post.show.btn.save')}}
                </flux:button>
            </flux:tab.panel>
            <flux:tab.panel name="post-create-images-panel">

                <!-- Existing Images (Edit Mode Only) -->
                @if ($editPost && $post)
                    <flux:text size="lg">{{ __('post.images.existing') }}</flux:text>
                    <div class="mb-6 space-y-6 border-dashed border-2 rounded-xl p-3 min-h-24 lg:min-h-64">

                        <div class="columns-3xs gap-6">
                            @forelse ($post->images as $image)

                                <div class="flex flex-col mb-4 break-inside-avoid">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($image->filename) }}"
                                         alt="{{ $image->caption[app()->getLocale()] ?? $image->original_filename }}"
                                         class="max-w-xs h-auto"
                                    >
                                    <flux:text size="xs">{{ __('post.images.image_filename') }}: {{ $image->original_filename }}</flux:text>
                                    <flux:text size="xs">{{ __('post.images.image_caption_de') }} (DE): {{ $image->caption['de'] ?? 'Kein Titel' }}</flux:text>
                                    <flux:text size="xs">{{ __('post.images.image_caption_hu') }} (HU): {{ $image->caption['hu'] ?? 'Nincs cím' }}</flux:text>
                                    <flux:text size="xs">{{ __('post.images.image_author') }}: {{ $image->author ?? 'na' }}</flux:text>
                                    <flux:button wire:click="deleteImage({{ $image->id }})"
                                                 size="xs"
                                                 variant="danger"
                                    >{{ __('post.images.image_btn_remove') }}
                                    </flux:button>

                                </div>
                            @empty
                                <flux:text size="sm">{{ __('post.images.no_existing') }}</flux:text>
                            @endforelse
                        </div>
                    </div>
                @endif

                <flux:separator text="{{ __('post.section.images.header') }}"
                                class="my-12"
                />

                <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <section class="space-y-6">
                        <flux:text size="lg">{{ __('post.images.upload_explanation') }}</flux:text>
                        <flux:input.file
                            wire:model="newImages"
                            multiple
                            accept="image/*"
                            label="{{ __('post.images.upload') }}"
                        />
                        <flux:button variant="primary"
                                     type="submit"
                        >{{ __('post.show.btn.save') }}
                        </flux:button>
                    </section>
                    <aside class="p-3 border-dashed border-2 rounded-xl min-h-24 lg:min-h-64">
                        @if (!empty($images))
                            <div class="space-y-6">
                                <flux:text size="lg">{{ __('post.images.preview') }}</flux:text>
                                <div class="columns-3 divide-y divide-zinc-200 gap-6">
                                    @foreach ($images as $index => $image)
                                        <div class="flex flex-col space-y-2 break-inside-avoid">
                                            <flux:text size="xs">{{ $image->getClientOriginalName() }}</flux:text>
                                            <img src="{{ $image->temporaryUrl() }}"
                                                 alt="Preview"
                                                 class="max-w-full h-auto"
                                            >
                                            <aside class="flex justify-between items-center">
                                                <div>

                                                    <flux:input size="xs"
                                                                wire:model="captionsDe.{{ $index }}"
                                                                label="Bildunterschrift (DE)"
                                                    />
                                                    <flux:input size="xs"
                                                                wire:model="captionsHu.{{ $index }}"
                                                                label="Képaláírás (HU)"
                                                    />
                                                    <flux:input size="xs"
                                                                wire:model="authors.{{ $index }}"
                                                                label="Autor"
                                                    />
                                                </div>

                                                <flux:button wire:click="removeImage({{ $index }})"
                                                             size="xs"
                                                             variant="danger"
                                                >Remove
                                                </flux:button>
                                            </aside>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <flux:subheading>{{ __('post.images.empty_list') }}</flux:subheading>
                        @endif
                    </aside>

                    @error('newImages.*') <span class="text-red-500">{{ $message }}</span> @enderror
                </section>

            </flux:tab.panel>
        </flux:tab.group>
    </form>


    <flux:modal name="show-md-keys">
        <table class="[:where(&amp;)]:min-w-full table-fixed text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20 whitespace-nowrap [&amp;_dialog]:whitespace-normal [&amp;_[popover]]:whitespace-normal mt-2"
               data-flux-table=""
        >
            <thead data-flux-columns="">
            <tr>
                <th class="py-3 px-3 first:pl-0 last:pr-0 text-left text-sm font-medium text-zinc-800 dark:text-white  **:data-flux-table-sortable:last:mr-0"
                    data-flux-column=""
                >
                    <div class="flex in-[.group\/center-align]:justify-center in-[.group\/right-align]:justify-end">Markdown</div>
                </th>
                <th class="py-3 px-3 first:pl-0 last:pr-0 text-left text-sm font-medium text-zinc-800 dark:text-white  **:data-flux-table-sortable:last:mr-0"
                    data-flux-column=""
                >
                    <div class="flex in-[.group\/center-align]:justify-center in-[.group\/right-align]:justify-end">Operation</div>
                </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-zinc-800/10 dark:divide-white/20 [&amp;:not(:has(*))]:border-t-0!"
                   data-flux-rows=""
            >
            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">#</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Apply heading level 1
                </td>
            </tr>

            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">##</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Apply heading level 2
                </td>
            </tr>

            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">###</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Apply heading level 3
                </td>
            </tr>

            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">**</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Bold
                </td>
            </tr>

            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">*</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Italic
                </td>
            </tr>

            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">~~</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Strikethrough
                </td>
            </tr>

            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">-</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Bullet list
                </td>
            </tr>

            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">1.</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Ordered list
                </td>
            </tr>

            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">&gt;</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Blockquote
                </td>
            </tr>

            {{--      <tr data-flux-row="">
                      <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5" data-flux-cell="">
                          <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">`</span>
                      </td>
                      <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300" data-flux-cell="">
                          Inline code
                      </td>
                  </tr>

                  <tr data-flux-row="">
                      <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5" data-flux-cell="">
                          <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">```</span>
                      </td>
                      <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300" data-flux-cell="">
                          Code block
                      </td>
                  </tr>

                  <tr data-flux-row="">
                      <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5" data-flux-cell="">
                          <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">```?</span>
                      </td>
                      <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300" data-flux-cell="">
                          Code block (with <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">class="language-?"</span>)
                      </td>
                  </tr>--}}

            <tr data-flux-row="">
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300 *:mr-0.5"
                    data-flux-cell=""
                >
                    <span class="font-mono font-medium text-sm text-zinc-700 dark:text-zinc-200 inline-block whitespace-nowrap rounded-md px-1.5 py-[0rem] bg-zinc-600/10 dark:bg-white/15">---</span>
                </td>
                <td class="py-3 px-3 first:pl-0 last:pr-0 text-sm  text-zinc-500 dark:text-zinc-300"
                    data-flux-cell=""
                >
                    Horizontal rule
                </td>
            </tr>
            </tbody>
        </table>
    </flux:modal>
</div>

