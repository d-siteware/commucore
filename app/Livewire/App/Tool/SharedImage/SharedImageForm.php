<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\SharedImage;

use App\Models\Membership\Member;
use App\Models\SharedImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithFileUploads;

final class SharedImageForm extends Form
{
    use WithFileUploads;

    #[Validate('required|image|max:10240')] // 10 MB
    public $image;

    public $images = []; // Changed from single $image to array

    #[Validate('required|string|max:255')]
    public string $label = '';

    public ?int $userId;

    public ?string $path;

    public ?string $thumbnailPath;

    public ?bool $isApproved;

    public ?bool $approvedBy;

    public ?string $approvedAt;

    public $fileSize;

    public ?int $invitationId = null;

    public function save(): array
    {
        $this->validate([
            'images' => ['required', 'array'],
        ]);

        $savedImages = [];

        foreach ($this->images as $image) {
            $uuid = (string) Str::uuid();
            $path = $image->storeAs('shared-images/originals', "{$uuid}.jpg", 'local');

            // Thumbnail erstellen
            $thumbPath = null;
            if (function_exists('imagecreatefromstring')) {
                $thumbnail = $this->generateThumbnail($image->getRealPath(), 400);
                if ($thumbnail) {
                    $thumbPath = "shared-images/thumbs/{$uuid}.jpg";
                    Storage::disk('local')
                        ->put($thumbPath, $thumbnail);
                }
            }

            // Save to database
            $sharedImage = SharedImage::create([
                'label' => $this->label,
                'user_id' => Auth::check() ? Auth::id() : null,
                'invitation_id' => $this->invitationId,
                'path' => $path,
                'thumbnail_path' => $thumbPath,
                'file_size' => $image->getSize(),
                'dimensions' => $this->getImageDimensions($image->getRealPath()),
                'is_approved' => Auth::check() && Auth::user()
                    ->isBoardMember(),
                'approved_by' => Auth::check() && Auth::user()
                    ->isBoardMember() ? Auth::id() : null,
                'approved_at' => Auth::check() && Auth::user()
                    ->isBoardMember() ? now() : null,
            ]);

            // Notification an Board-Mitglieder (wenn noch nicht freigegeben)
            if (! $sharedImage->is_approved) {
                $this->notifyBoard($sharedImage);
            }

            $savedImages[] = $sharedImage;
            Log::debug('Saved shared image id: '.$sharedImage->id);
        }

        return $savedImages;
    }

    protected function getImageDimensions(string $path): array
    {
        [$width, $height] = getimagesize($path) ?: [null, null];

        return ['width' => $width, 'height' => $height];
    }

    protected function generateThumbnail(string $path, int $targetWidth): ?string
    {
        try {
            [$width, $height] = getimagesize($path);
            $ratio = $targetWidth / $width;
            $targetHeight = intval($height * $ratio);

            $source = imagecreatefromstring(file_get_contents($path));
            $thumb = imagecreatetruecolor($targetWidth, $targetHeight);
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

            ob_start();
            imagejpeg($thumb, null, 80);

            return ob_get_clean();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function notifyBoard(SharedImage $image): void
    {
        // Beispielhafte Notification (du kannst die Notification-Klasse selbst definieren)

        Member::getBoardMembers()
            ->each(function ($member) use ($image): void {
                $member->notify(new \App\Notifications\SharedImageUploaded($image));
            });
    }
}
