<?php

declare(strict_types=1);

use App\Enums\EventStatus;
use App\Models\Blog\Post;
use App\Models\Blog\PostImage;
use App\Models\Event\Event;
use App\Models\Event\EventTimeline;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
});

// ── Authentifizierung ─────────────────────────────────────────────────────────

it('rejects requests without token', function () {
    $this->getJson('/api/public/v1/events')
        ->assertStatus(401);
});

it('rejects tokens without read scope', function () {
    Sanctum::actingAs($this->user, ['create']);

    $this->getJson('/api/public/v1/events')
        ->assertStatus(403);
});

it('allows tokens with read scope', function () {
    Sanctum::actingAs($this->user, ['read']);

    $this->getJson('/api/public/v1/events')
        ->assertStatus(200);
});

// ── Events Index ──────────────────────────────────────────────────────────────

it('returns only published events', function () {
    Sanctum::actingAs($this->user, ['read']);

    Event::factory()->create(['status' => EventStatus::PUBLISHED]);
    Event::factory()->create(['status' => EventStatus::DRAFT]);

    $this->getJson('/api/public/v1/events')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

it('returns upcoming events only when filter is set', function () {
    Sanctum::actingAs($this->user, ['read']);

    Event::factory()->create([
        'status' => EventStatus::PUBLISHED,
        'event_date' => now()->addDays(7),
    ]);
    Event::factory()->create([
        'status' => EventStatus::PUBLISHED,
        'event_date' => now()->subDays(7),
    ]);

    $this->getJson('/api/public/v1/events?upcoming=true')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

it('respects locale parameter for events', function () {
    Sanctum::actingAs($this->user, ['read']);

    Event::factory()->create([
        'status' => EventStatus::PUBLISHED,
        'title' => ['de' => 'Deutsches Titel', 'en' => 'English Title'],
    ]);

    $this->getJson('/api/public/v1/events?locale=en')
        ->assertStatus(200)
        ->assertJsonPath('data.0.title', 'English Title');
});

it('falls back to first available locale', function () {
    Sanctum::actingAs($this->user, ['read']);

    Event::factory()->create([
        'status' => EventStatus::PUBLISHED,
        'title' => ['de' => 'Nur Deutsch'],
    ]);

    $this->getJson('/api/public/v1/events?locale=hu')
        ->assertStatus(200)
        ->assertJsonPath('data.0.title', 'Nur Deutsch');
});

it('caps limit at 50', function () {
    Sanctum::actingAs($this->user, ['read']);

    Event::factory()->count(60)->create(['status' => EventStatus::PUBLISHED]);

    $this->getJson('/api/public/v1/events?limit=100')
        ->assertStatus(200)
        ->assertJsonPath('meta.per_page', 50);
});

// ── Events Show ───────────────────────────────────────────────────────────────

it('returns event detail with venue and program', function () {
    Sanctum::actingAs($this->user, ['read']);

    $event = Event::factory()
        ->has(EventTimeline::factory()->count(2), 'timelines')
        ->create(['status' => EventStatus::PUBLISHED]);

    $this->getJson("/api/public/v1/events/{$event->id}")
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id', 'title', 'event_date',
                'venue',
                'program' => [['start', 'end', 'title']],
            ],
        ]);
});

it('returns 404 for unpublished event', function () {
    Sanctum::actingAs($this->user, ['read']);

    $event = Event::factory()->create(['status' => EventStatus::DRAFT]);

    $this->getJson("/api/public/v1/events/{$event->id}")
        ->assertStatus(404);
});

// ── Posts Index ───────────────────────────────────────────────────────────────

it('returns only published posts', function () {
    Sanctum::actingAs($this->user, ['read']);

    Post::factory()->create(['status' => 'published']);
    Post::factory()->create(['status' => 'draft']);

    $this->getJson('/api/public/v1/posts')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

it('includes linked event in post list', function () {
    Sanctum::actingAs($this->user, ['read']);

    $event = Event::factory()->create(['status' => EventStatus::PUBLISHED]);
    Post::factory()->create([
        'status' => 'published',
        'event_id' => $event->id,
    ]);

    $this->getJson('/api/public/v1/posts')
        ->assertStatus(200)
        ->assertJsonPath('data.0.event.id', $event->id);
});

// ── Posts Show ────────────────────────────────────────────────────────────────

it('returns post detail with images and event', function () {
    Sanctum::actingAs($this->user, ['read']);

    $post = Post::factory()
        ->has(PostImage::factory()->count(2), 'images')
        ->create(['status' => 'published']);

    $this->getJson("/api/public/v1/posts/{$post->id}")
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id', 'title', 'body',
                'images' => [['url', 'caption', 'author']],
            ],
        ]);
});

it('returns 404 for unpublished post', function () {
    Sanctum::actingAs($this->user, ['read']);

    $post = Post::factory()->create(['status' => EventStatus::DRAFT]);

    $this->getJson("/api/public/v1/posts/{$post->id}")
        ->assertNotFound();
});
