<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\SharedImage\Index;

use App\Livewire\Traits\HasPrivileges;
use App\Models\SharedImage;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class Content extends Component
{
    use HasPrivileges;

    public string $viewMode = 'table';

    public $images;

    public function mount(string $viewMode = 'table'): void
    {
        $this->viewMode = $viewMode;
    }

    public function getImagesProperty()
    {
        $query = SharedImage::query();

        if (! Auth::user()->isBoardMember()) {
            // Nur freigegebene Bilder für normale User
            $query->where('is_approved', true);
        }

        return $query->latest()->get();
    }

    protected $listeners = ['approveImage'];

    public function approveImage(int $id): void
    {

        $this->checkPrivilege(SharedImage::class);

        $image = \App\Models\SharedImage::findOrFail($id);

        if (! $image->is_approved) {
            $image->update([
                'is_approved' => true,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->dispatch('image-updated');

        }
    }

    /**
     * @return StreamedResponse|void
     */
    public function downloadImage(int $id)
    {
        $this->checkPrivilege(SharedImage::class);

        $image = \App\Models\SharedImage::findOrFail($id);

        $filename = $image->path;

        if ($image->is_approved && Storage::disk('local')->exists($filename)) {
            if ($image->user) {
                $dowloadLabel = $image->label.'_'.$image->user->name;
            } elseif ($image->invitation && $image->invitation->email) {
                $email = $image->invitation->email;
                $name = explode('@', $email)[0];
                $dowloadLabel = $image->label.'_'.$name;
            } else {
                $dowloadLabel = $image->label;
            }

            return Storage::download($filename, $dowloadLabel);
        }

    }

    public function deleteImage(int $id): void
    {
        $originalDeleted = false;
        $thumpDeleted = false;
        $this->checkPrivilege(SharedImage::class);
        $image = \App\Models\SharedImage::findOrFail($id);

        $originalPath = $image->path;
        $thumbPath = $image->thumbnail_path;

        if (Storage::disk('local')->exists($originalPath)) {
            $originalDeleted = Storage::disk('local')->delete($originalPath);
        }

        if (Storage::disk('local')->exists($thumbPath)) {
            $thumpDeleted = Storage::disk('local')->delete($thumbPath);
        }

        if ($originalDeleted && $thumpDeleted) {
            $image->delete();

            Flux::toast(
                text: 'Das Bild wurde erfolgreich gelöscht',
                heading: 'Erfolg',
                variant: 'success',
            );

            $this->dispatch('image-updated');
        }

    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('livewire.app.tool.shared-image.index.content');
    }
}
