<div>
    <flux:file-upload wire:model="image" label="">
        <flux:file-upload.dropzone
                heading="{{ __('app.image_upload.dropzone_heading') }}"
                text="{{ __('app.image_upload.dropzone_text') }}"
        />
    </flux:file-upload>

    <div class="mt-3 flex flex-col gap-2">
        @if ($image)
            <div wire:loading wire:target="image">
                <flux:badge color="teal">{{ __('app.image_upload.uploading') }}</flux:badge>
            </div>

            <div wire:loading.remove wire:target="image">
                <flux:file-item
                        :heading="$image->getClientOriginalName()"
                        :image="$image->temporaryUrl()"
                        :size="$image->getSize()"
                >
                    <x-slot name="actions">
                        <flux:file-item.remove
                                wire:click="removeImage"
                                aria-label="{{ __('app.image_upload.remove') . ': ' . $image->getClientOriginalName() }}"
                        />
                    </x-slot>
                </flux:file-item>
            </div>
        @endif
    </div>
</div>