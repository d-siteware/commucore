<?php

declare(strict_types=1);

use App\Models\Blog\Post;
use App\Models\Blog\PostType;

describe('PostType model', function (): void {
    it('can be created with factory', function (): void {
        $type = PostType::factory()->create();

        expect($type)->toBeInstanceOf(PostType::class)
            ->and($type->name)->toBeArray()
            ->and($type->slug)->not->toBeNull();
    });

    it('has fillable attributes', function (): void {
        $type = PostType::create([
            'name' => ['de' => 'News', 'en' => 'News'],
            'slug' => 'news',
            'description' => 'News posts',
            'color' => 'blue',
        ]);

        expect($type->name)->toBe(['de' => 'News', 'en' => 'News'])
            ->and($type->slug)->toBe('news')
            ->and($type->description)->toBe('News posts')
            ->and($type->color)->toBe('blue');
    });

    it('casts name as array', function (): void {
        $type = PostType::factory()->create([
            'name' => ['de' => 'Ankündigung', 'en' => 'Announcement'],
        ]);

        expect($type->name)->toBeArray()
            ->and($type->name['de'])->toBe('Ankündigung');
    });

    it('returns color', function (): void {
        $type = PostType::factory()->create(['color' => 'indigo']);

        expect($type->color())->toBe('indigo');
    });
});
