<div>
    <flux:heading size="lg" class="mb-3 lg:mb-6">
        {{ __('post.create.page.title') }}
    </flux:heading>

    {{-- Progress Bar --}}
    <nav aria-label="Progress" class="mb-10">
        <ol role="list"
            class="divide-y divide-gray-300 dark:divide-white/15 rounded-xl border border-gray-300 dark:border-white/15 md:flex md:divide-y-0 overflow-hidden"
        >
            <li class="relative md:flex md:flex-1">
                @if($step > 1)
                    <x-steps.completed :item="1" step="01" label="{{ __('post.create.steps.head') }}"/>
                @elseif($step === 1)
                    <x-steps.current   :item="1" step="01" label="{{ __('post.create.steps.head') }}"/>
                @else
                    <x-steps.upcomming :item="1" step="01" label="{{ __('post.create.steps.head') }}"/>
                @endif
            </li>
            <li class="relative md:flex md:flex-1">
                @if($step > 2)
                    <x-steps.completed :item="2" step="02" label="{{ __('post.create.steps.content') }}"/>
                @elseif($step === 2)
                    <x-steps.current   :item="2" step="02" label="{{ __('post.create.steps.content') }}"/>
                @else
                    <x-steps.upcomming :item="2" step="02" label="{{ __('post.create.steps.content') }}"/>
                @endif
            </li>
            <li class="relative md:flex md:flex-1">
                @if($step > 3)
                    <x-steps.completed :item="3" step="03" label="{{ __('post.create.steps.images') }}" last/>
                @elseif($step === 3)
                    <x-steps.current   :item="3" step="03" label="{{ __('post.create.steps.images') }}" last/>
                @else
                    <x-steps.upcomming :item="3" step="03" label="{{ __('post.create.steps.images') }}" last="true"/>
                @endif
            </li>
        </ol>
    </nav>

    <form wire:submit="save">

        {{-- ── Step 1: Kerndaten ──────────────────────────────────── --}}
        @if($step === 1)
            <section class="max-w-2xl space-y-6">
                <flux:card class="space-y-6">

                    <x-input-with-counter
                            model="form.label"
                            label="{{ __('post.label') }}"
                            max-length="40"
                    />

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <flux:field>
                            <flux:label>{{ __('post.type.label') }}</flux:label>
                            <flux:select wire:model.blur="form.post_type_id">
                                @foreach(\App\Models\Blog\PostType::query()->select('id','name')->get() as $type)
                                    <flux:select.option value="{{ $type->id }}">
                                        {{ $type->name[app()->getLocale()] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="form.post_type_id"/>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('post.status') }}</flux:label>
                            <flux:select wire:model.blur="form.status">
                                @foreach(\App\Enums\EventStatus::options() as $value => $label)
                                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="form.status"/>
                        </flux:field>
                    </div>

                </flux:card>
            </section>
        @endif

        {{-- ── Step 2: Inhalte pro Sprache ───────────────────────── --}}
        @if($step === 2)
            <section class="space-y-6">
                @isMultiLang
                <flux:tab.group>
                    <flux:tabs>
                        @foreach($locales as $locale)
                            <flux:tab name="post-content-{{ $locale }}">
                                {{ $locale }}
                                @if($errors->hasAny(["form.title.{$locale}", "form.slug.{$locale}", "form.body.{$locale}"]))
                                    <flux:badge color="red" size="sm">!</flux:badge>
                                @endif
                            </flux:tab>
                        @endforeach
                    </flux:tabs>

                    @foreach($locales as $locale)
                        <flux:tab.panel name="post-content-{{ $locale }}">
                            <x-posts.post-texts :locale="$locale"/>
                        </flux:tab.panel>
                    @endforeach
                </flux:tab.group>
                @else
                    <x-posts.post-texts :locale="$locales[0]"/>
                @endIsMultiLang
            </section>
        @endif

        {{-- ── Step 3: Bilder ─────────────────────────────────────── --}}
        @if($step === 3)
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <section class="space-y-6">
                    <flux:text size="lg">{{ __('post.images.upload_explanation') }}</flux:text>
                    <flux:file-upload wire:model.live="newImages" multiple label="{{ __('post.images.upload') }}"
                                      accept="image/*"
                    >
                        <flux:file-upload.dropzone
                                heading="{{ __('post.images.dropzone.heading') }}"
                                text="{{ __('post.images.dropzone.text') }}"
                        />
                    </flux:file-upload>
                    @error('newImages.*')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </section>

                <aside class="p-3 border-dashed border-2 rounded-xl min-h-24 lg:min-h-64">
                    @if(!empty($images))
                        <div class="space-y-6">
                            <flux:text size="lg">{{ __('post.images.preview') }}</flux:text>
                            <div class="columns-3 gap-6 divide-y divide-zinc-200">
                                @foreach($images as $index => $image)
                                    <div class="flex flex-col space-y-2 break-inside-avoid pt-2">
                                        <flux:text size="xs">{{ $image->getClientOriginalName() }}</flux:text>
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="max-w-full h-auto">

                                        <div class="space-y-2">
                                            @foreach($locales as $locale)
                                                <flux:input
                                                        size="xs"
                                                        wire:model.blur="captions.{{ $locale }}.{{ $index }}"
                                                        label="{{ __('post.images.image_caption') }} ({{ $locale }})"
                                                />
                                                <flux:error name="captions.{{ $locale }}.{{ $index }}"/>
                                            @endforeach

                                            <flux:input
                                                    size="xs"
                                                    wire:model.blur="authors.{{ $index }}"
                                                    label="{{ __('post.images.image_author') }}"
                                            />
                                            <flux:error name="authors.{{ $index }}"/>
                                        </div>

                                        <flux:button
                                                wire:click="removeImage({{ $index }})"
                                                size="xs"
                                                variant="danger"
                                        >{{ __('post.images.image_btn_remove') }}</flux:button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <flux:subheading>{{ __('post.images.empty_list') }}</flux:subheading>
                    @endif
                </aside>
            </section>
        @endif

        {{-- ── Navigation ─────────────────────────────────────────── --}}
        <div class="mt-6 flex justify-between">
            @if($step > 1)
                <flux:button type="button" wire:click="previousStep" variant="filled">
                    {!! __('pagination.previous') !!}
                </flux:button>
            @else
                <span></span>
            @endif

            @if($step < $totalSteps)
                <flux:button type="button" wire:click="nextStep" variant="primary">
                    {!! __('pagination.next') !!}
                </flux:button>
            @else
                <flux:button type="submit" variant="primary">
                    {{ __('post.show.btn.save') }}
                </flux:button>
            @endif
        </div>

    </form>
</div>