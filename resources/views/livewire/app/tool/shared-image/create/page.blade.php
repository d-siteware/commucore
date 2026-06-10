<div>
    <flux:header :title="__('shared_image.create.title')">
        <flux:button tone="neutral" size="sm" href="{{ route('shared-image.index') }}" icon="arrow-left">
            {{ __('shared_image.create.back_to_index') }}
        </flux:button>
    </flux:header>

    <livewire:app.tool.shared-image.create.form />
</div>
