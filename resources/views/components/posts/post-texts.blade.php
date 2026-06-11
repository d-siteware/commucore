@props([
    'locale' => 'de'
])
<section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="space-y-6">
        <x-input-with-counter
                model="form.title.{{ $locale }}"
                label="{{ __('post.title') }}"
                max-length="60"
        />
        <flux:text>{{ __('post.create.title_explanation') }}</flux:text>
    </div>

    <div class="space-y-6">
        <flux:input wire:model="form.slug.{{ $locale }}"
                    label="{{ __('post.slug') }}"
                    class="mb-6"
        />
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
    </div>


</section>
<flux:fieldset>
    <flux:error name="form.body.{{ $locale }}"/>
    <flux:label>{{ __('post.body') }}</flux:label>
    <flux:editor wire:model="form.body.{{ $locale }}"
                  description="{{ __('post.editor_description', ['locale' => $locale]) }}"
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
                    <flux:menu.item>{{ __('post.editor_help') }}</flux:menu.item>
                </flux:modal.trigger>
            </flux:menu>
        </flux:dropdown>
    </flux:editor.toolbar>
    <flux:editor.content/>
</flux:editor>
</flux:fieldset>