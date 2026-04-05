<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\SharedImage\Index;

use App\Livewire\Traits\HasPrivileges;
use App\Models\SharedImage;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class Page extends Component
{
    use HasPrivileges;
    use WithPagination;

    public string $viewMode = 'grid';

    public function toggleViewMode(): void
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'table' : 'grid';
        $this->resetPage();
    }

    #[Computed]
    public function images(): LengthAwarePaginator
    {
        return SharedImage::latest()
            ->where(function ($query) {
                // Alle freigegebenen Bilder
                $query->where('is_approved', true)
                    // plus eigene noch nicht freigegebene
                    ->orWhere(function ($q) {
                        $q->where('user_id', Auth::id())
                            ->where('is_approved', false);
                    });
            })
            ->paginate(20);
    }

 #[Computed]
    public function unapprovedImages(): LengthAwarePaginator
    {
        return SharedImage::latest()
            ->where(function ($query) {
                // Alle freigegebenen Bilder
                $query->where('is_approved', false);
            })
            ->paginate(20);
    }

    public function approveImage(int $id): void
    {
        $this->checkPrivilege(SharedImage::class);

        $image = SharedImage::findOrFail($id);

        if (! $image->is_approved) {
            $image->update([
                'is_approved' => true,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        }
    }

    public function deleteImage(int $id): void
    {
        $this->checkPrivilege(SharedImage::class);

        $image = SharedImage::findOrFail($id);

        $originalDeleted = Storage::disk('local')->exists($image->path)
            && Storage::disk('local')->delete($image->path);

        $thumbDeleted = $image->thumbnail_path
            && Storage::disk('local')->exists($image->thumbnail_path)
            && Storage::disk('local')->delete($image->thumbnail_path);

        if ($originalDeleted && $thumbDeleted) {
            $image->delete();

            Flux::toast(
                text: 'Das Bild wurde erfolgreich gelöscht.',
                heading: 'Erfolg',
                variant: 'success',
            );
        }
    }

    public function downloadImage(int $id): StreamedResponse|null
    {
        $this->checkPrivilege(SharedImage::class);

        $image = SharedImage::findOrFail($id);

        if (! $image->is_approved || ! Storage::disk('local')->exists($image->path)) {
            return null;
        }

        $label = match(true) {
            $image->user !== null => $image->label.'_'.$image->user->name,
            $image->invitation !== null => $image->label.'_'.explode('@', $image->invitation->email)[0],
            default => $image->label,
        };

        return Storage::download($image->path, $label);
    }

    public function render(): View
    {
        return view('livewire.app.tool.shared-image.index.page')
            ->title('Bildergalerie');
    }
}