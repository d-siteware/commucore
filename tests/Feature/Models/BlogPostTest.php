<?php

declare(strict_types=1);

use App\Enums\EventStatus;
use App\Models\Blog\Post;
use App\Models\Blog\PostType;
use App\Models\Event\Event;
use App\Models\Project\Project;
use App\Models\User;
use Carbon\Carbon;

describe('Blog Post model', function (): void {
    it('can be created with factory', function (): void {
        $post = Post::factory()->create();

        expect($post)->toBeInstanceOf(Post::class)
            ->and($post->user)->toBeInstanceOf(User::class)
            ->and($post->type)->toBeInstanceOf(PostType::class);
    });

    it('has fillable attributes', function (): void {
        $user = User::factory()->create();
        $type = PostType::factory()->create();

        $post = Post::create([
            'title' => ['de' => 'Beitrag', 'en' => 'Post'],
            'slug' => ['de' => 'beitrag', 'en' => 'post'],
            'body' => ['de' => 'Inhalt', 'en' => 'Content'],
            'user_id' => $user->id,
            'status' => EventStatus::PUBLISHED,
            'post_type_id' => $type->id,
            'published_at' => '2024-06-01 10:00:00',
        ]);

        expect($post->title)->toBe(['de' => 'Beitrag', 'en' => 'Post'])
            ->and($post->user_id)->toBe($user->id);
    });

    it('casts title, slug, body as array', function (): void {
        $post = Post::factory()->create([
            'title' => ['de' => 'Test'],
            'slug' => ['de' => 'test'],
            'body' => ['de' => 'Body'],
        ]);

        expect($post->title)->toBeArray()
            ->and($post->slug)->toBeArray()
            ->and($post->body)->toBeArray();
    });

    it('casts status as EventStatus enum', function (): void {
        $post = Post::factory()->create(['status' => EventStatus::PUBLISHED]);

        expect($post->status)->toBeInstanceOf(EventStatus::class)
            ->and($post->status)->toBe(EventStatus::PUBLISHED);
    });

    it('casts published_at as datetime', function (): void {
        $post = Post::factory()->create(['published_at' => '2024-06-01 10:00:00']);

        expect($post->published_at)->toBeInstanceOf(Carbon::class)
            ->and($post->published_at->format('Y-m-d H:i:s'))->toBe('2024-06-01 10:00:00');
    });

    it('belongs to a user', function (): void {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        expect($post->user)->toBeInstanceOf(User::class)
            ->and($post->user->id)->toBe($user->id);
    });

    it('belongs to a post type', function (): void {
        $type = PostType::factory()->create();
        $post = Post::factory()->create(['post_type_id' => $type->id]);

        expect($post->type)->toBeInstanceOf(PostType::class)
            ->and($post->type->id)->toBe($type->id);
    });

    it('belongs to an event (nullable)', function (): void {
        $event = Event::factory()->create();
        $post = Post::factory()->create(['event_id' => $event->id]);

        expect($post->event)->toBeInstanceOf(Event::class)
            ->and($post->event->id)->toBe($event->id);
    });

    it('belongs to a project (nullable)', function (): void {
        $project = Project::factory()->create();
        $post = Post::factory()->create(['project_id' => $project->id]);

        expect($post->project)->toBeInstanceOf(Project::class)
            ->and($post->project->id)->toBe($project->id);
    });

    it('scope forEvent filters correctly', function (): void {
        $event = Event::factory()->create();
        Post::factory()->create(['event_id' => $event->id]);
        Post::factory()->create();

        expect(Post::forEvent($event->id)->count())->toBe(1);
    });

    it('scope forProject filters correctly', function (): void {
        $project = Project::factory()->create();
        Post::factory()->create(['project_id' => $project->id]);
        Post::factory()->create();

        expect(Post::forProject($project->id)->count())->toBe(1);
    });

    it('scope standalone returns posts without event or project', function (): void {
        Post::factory()->create(['event_id' => Event::factory()->create()->id, 'project_id' => null]);
        Post::factory()->create(['event_id' => null, 'project_id' => Project::factory()->create()->id]);
        Post::factory()->create(['event_id' => null, 'project_id' => null]);

        expect(Post::standalone()->count())->toBe(1);
    });

    it('scope published filters by published status', function (): void {
        Post::factory()->create(['status' => EventStatus::PUBLISHED]);
        Post::factory()->create(['status' => EventStatus::DRAFT]);

        expect(Post::published()->count())->toBe(1);
    });

    it('isPublished returns true for published posts', function (): void {
        $post = Post::factory()->create(['status' => EventStatus::PUBLISHED]);

        expect($post->isPublished())->toBeTrue();
    });

    it('isStandalone returns true when no event or project', function (): void {
        $post = Post::factory()->create(['event_id' => null, 'project_id' => null]);

        expect($post->isStandalone())->toBeTrue();
    });

    it('contextTitle returns event title', function (): void {
        $event = Event::factory()->create(['title' => ['de' => 'Eventname']]);
        $post = Post::factory()->create(['event_id' => $event->id]);

        expect($post->contextTitle('de'))->toBe('Eventname');
    });

    it('contextTitle returns null for standalone posts', function (): void {
        $post = Post::factory()->create(['event_id' => null, 'project_id' => null]);

        expect($post->contextTitle('de'))->toBeNull();
    });

    it('excerpt returns truncated text', function (): void {
        $post = Post::factory()->create([
            'body' => ['de' => 'Dies ist ein langer Text für den Test.'],
        ]);

        expect($post->excerpt(10))->toContain('...');
    });
});
