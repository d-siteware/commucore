<?php

declare(strict_types=1);

use App\Models\SharedImage;
use App\Models\User;
use App\Models\Membership\Invitation;
use Carbon\Carbon;

describe('SharedImage model', function (): void {
    it('can be created', function (): void {
        $user = User::factory()->create();

        $image = SharedImage::create([
            'user_id' => $user->id,
            'path' => 'shared/test.jpg',
            'label' => 'Test Image',
            'is_approved' => false,
        ]);

        expect($image)->toBeInstanceOf(SharedImage::class)
            ->and($image->label)->toBe('Test Image');
    });

    it('has fillable attributes', function (): void {
        $user = User::factory()->create();

        $image = SharedImage::create([
            'user_id' => $user->id,
            'path' => 'shared/photo.jpg',
            'thumbnail_path' => 'shared/thumb_photo.jpg',
            'label' => 'Photo',
            'is_approved' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'file_size' => 1024000,
            'dimensions' => ['width' => 1920, 'height' => 1080],
        ]);

        expect($image->path)->toBe('shared/photo.jpg')
            ->and($image->thumbnail_path)->toBe('shared/thumb_photo.jpg')
            ->and($image->label)->toBe('Photo');
    });

    it('casts is_approved as boolean', function (): void {
        $image = SharedImage::create([
            'path' => 'shared/test.jpg',
            'label' => 'Test',
            'is_approved' => 1,
        ]);

        expect($image->is_approved)->toBeTrue();
    });

    it('casts approved_at as datetime', function (): void {
        $image = SharedImage::create([
            'path' => 'shared/test.jpg',
            'label' => 'Test',
            'approved_at' => '2024-06-15 14:30:00',
        ]);

        expect($image->approved_at)->toBeInstanceOf(Carbon::class)
            ->and($image->approved_at->format('Y-m-d H:i:s'))->toBe('2024-06-15 14:30:00');
    });

    it('casts dimensions as array', function (): void {
        $image = SharedImage::create([
            'path' => 'shared/test.jpg',
            'label' => 'Test',
            'dimensions' => ['width' => 1920, 'height' => 1080],
        ]);

        expect($image->dimensions)->toBeArray()
            ->and($image->dimensions['width'])->toBe(1920);
    });

    it('belongs to a user (nullable)', function (): void {
        $user = User::factory()->create();
        $image = SharedImage::create([
            'user_id' => $user->id,
            'path' => 'shared/test.jpg',
            'label' => 'Test',
        ]);

        expect($image->user)->toBeInstanceOf(User::class)
            ->and($image->user->id)->toBe($user->id);
    });

    it('belongs to an invitation (nullable)', function (): void {
        $invitation = Invitation::factory()->create();
        $image = SharedImage::create([
            'invitation_id' => $invitation->id,
            'path' => 'shared/test.jpg',
            'label' => 'Test',
        ]);

        expect($image->invitation)->toBeInstanceOf(Invitation::class)
            ->and($image->invitation->id)->toBe($invitation->id);
    });

    it('belongs to an approver (user, nullable)', function (): void {
        $approver = User::factory()->create();
        $image = SharedImage::create([
            'approved_by' => $approver->id,
            'path' => 'shared/test.jpg',
            'label' => 'Test',
        ]);

        expect($image->approver)->toBeInstanceOf(User::class)
            ->and($image->approver->id)->toBe($approver->id);
    });

    it('returns author name from user', function (): void {
        $user = User::factory()->create(['name' => 'John Doe']);
        $image = SharedImage::create([
            'user_id' => $user->id,
            'path' => 'shared/test.jpg',
            'label' => 'Test',
        ]);

        expect($image->author)->toBe('John Doe');
    });
});
