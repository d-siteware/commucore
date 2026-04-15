<div class="space-y-4">

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    <flux:card class="max-w-xl mx-auto p-4">
        <form wire:submit.prevent="save" class="space-y-4">

            <flux:file-upload wire:model="form.images" label="{{ __('shared_image.form.images_label') }}" multiple>
                <flux:file-upload.dropzone
                        heading="{{ __('shared_image.form.dropzone_heading') }}"
                        text="{{ __('shared_image.form.dropzone_text') }}"
                />
            </flux:file-upload>

            @error('form.images.*')
            <flux:callout variant="warning" icon="exclamation-circle">{{ $message }}</flux:callout>
            @enderror

            @if (!empty($form->images))
                <div class="mt-2 flex flex-col gap-2">
                    <div wire:loading wire:target="form.images">
                        <flux:badge color="teal">{{ __('shared_image.form.uploading') }}</flux:badge>
                    </div>
                    <div wire:loading.remove wire:target="form.images">
                        @foreach($form->images as $image)
                            <flux:file-item
                                    :heading="$image->getClientOriginalName()"
                                    :image="$image->temporaryUrl()"
                                    :size="$image->getSize()"
                            />
                        @endforeach
                    </div>
                </div>
            @endif

            <flux:textarea
                    rows="auto"
                    label="{{ __('shared_image.form.label') }}"
                    wire:model.defer="form.label"
                    placeholder="{{ __('shared_image.form.label_placeholder') }}"
                    required
            />

            @error('form.label')
            <flux:callout variant="warning" icon="exclamation-circle">{{ $message }}</flux:callout>
            @enderror

            <flux:button type="submit" variant="primary">
                {{ __('shared_image.form.save') }}
            </flux:button>

        </form>
    </flux:card>

</div>