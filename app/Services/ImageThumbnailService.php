<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

final class ImageThumbnailService
{
    private ImageManager $manager;

    private array $sizes = [
        'small' => 150,
        'medium' => 300,
        'large' => 600,
    ];

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    public function generate(string $disk, string $originalPath, string $targetDir): array
    {
        $variants = [];
        $fullPath = Storage::disk($disk)->path($originalPath);
        $basename = basename($originalPath);
        $thumbDir = $targetDir . '/thumbs';

        if (! is_dir(Storage::disk($disk)->path($thumbDir))) {
            Storage::disk($disk)->makeDirectory($thumbDir);
        }

        foreach ($this->sizes as $name => $width) {
            $thumbPath = $thumbDir . '/' . $name . '-' . $basename;

            try {
                $image = $this->manager->read($fullPath);
                $image->scale(width: $width);
                $image->save(Storage::disk($disk)->path($thumbPath));
                $variants[$name] = $thumbPath;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $variants;
    }

    public function delete(string $disk, ?string ...$paths): void
    {
        $storage = Storage::disk($disk);

        foreach (array_filter($paths) as $path) {
            if ($storage->exists($path)) {
                $storage->delete($path);
            }
        }
    }
}
