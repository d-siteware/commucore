<?php

declare(strict_types=1);

namespace App\Livewire\App\Global;

use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

final class ImageUpload extends Component
{
    use WithFileUploads;

    #[Validate('image|max:10240')]
    public mixed $image = null;

    /** Storage disk path where the file will be saved (e.g. 'images', 'images/events') */
    public string $storagePath = 'images';

    /** Livewire event dispatched after successful upload, carrying `file: basename` */
    public string $dispatchEvent = 'image-uploaded';

    public function updatedImage(): void
    {
        if (! $this->image) {
            return;
        }

        $this->validate();

        $imagePath = $this->image->store($this->storagePath, 'public');

        $this->dispatch($this->dispatchEvent, file: basename($imagePath));
    }

    public function removeImage(): void
    {
        $this->image = null;
    }

    public function render(): View
    {
        return view('livewire.app.global.image-upload');
    }
}
