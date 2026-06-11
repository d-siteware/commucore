<?php

declare(strict_types=1);

use App\Services\ImageThumbnailService;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
});

it('generates thumbnail variants for an image', function (): void {
    $disk = Storage::disk('public');

    // Create a real GD image for testing
    $testDir = 'images/events';
    $disk->makeDirectory($testDir);

    $originalPath = $testDir . '/test-event.jpg';
    $fullPath = $disk->path($originalPath);

    $img = imagecreatetruecolor(1200, 800);
    $bg = imagecolorallocate($img, 255, 0, 0);
    imagefill($img, 0, 0, $bg);
    imagejpeg($img, $fullPath, 90);
    imagedestroy($img);

    $service = new ImageThumbnailService;
    $variants = $service->generate('public', $originalPath, 'images');

    expect($variants)->toHaveKeys(['small', 'medium', 'large']);
    expect($disk->exists($variants['small']))->toBeTrue();
    expect($disk->exists($variants['medium']))->toBeTrue();
    expect($disk->exists($variants['large']))->toBeTrue();

    // Verify dimensions are scaled down
    [$smallW] = getimagesize($disk->path($variants['small']));
    expect($smallW)->toBeLessThanOrEqual(150);

    [$mediumW] = getimagesize($disk->path($variants['medium']));
    expect($mediumW)->toBeLessThanOrEqual(300);

    [$largeW] = getimagesize($disk->path($variants['large']));
    expect($largeW)->toBeLessThanOrEqual(600);

    // Cleanup
    $disk->delete([$variants['small'], $variants['medium'], $variants['large'], $originalPath]);
});

it('returns empty array when source image does not exist', function (): void {
    $service = new ImageThumbnailService;
    $variants = $service->generate('public', 'images/missing.jpg', 'images');

    expect($variants)->toBeEmpty();
});

it('deletes multiple variant files from disk', function (): void {
    $disk = Storage::disk('public');

    $disk->put('events/thumbs/small-test.jpg', 'content');
    $disk->put('events/thumbs/medium-test.jpg', 'content');
    $disk->put('events/thumbs/large-test.jpg', 'content');

    $service = new ImageThumbnailService;
    $service->delete(
        'public',
        'events/thumbs/small-test.jpg',
        'events/thumbs/medium-test.jpg',
        'events/thumbs/large-test.jpg',
    );

    $disk->assertMissing('events/thumbs/small-test.jpg');
    $disk->assertMissing('events/thumbs/medium-test.jpg');
    $disk->assertMissing('events/thumbs/large-test.jpg');
});

it('gracefully handles deleting non-existent files', function (): void {
    $service = new ImageThumbnailService;

    $service->delete('public', 'events/thumbs/nonexistent.jpg');
    // No exception should be thrown
    expect(true)->toBeTrue();
});
