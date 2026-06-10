<?php

declare(strict_types=1);

use App\Enums\EventStatus;
use App\Livewire\Activity\Blog\Post\Form;
use App\Models\Blog\Post;
use App\Models\Locale;
use App\Models\Membership\Member;
use App\Models\User;
use App\Services\MailingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('can access the create post page', function (): void {
    $member = Member::factory()->withUser()->create([
        'user_id' => User::factory()->create(['email_verified_at' => now()])->id,
    ]);
    $this->actingAs($member->user);

    $this->get(route('backend.posts.create'))->assertStatus(200);
});

describe('Blog Post Form - Authorization', function (): void {
    it('prevents unauthorized users from accessing the form', function (): void {
        $post = Post::factory()->create();

        $this->get('/backend/posts/', [$post])->assertRedirect('/login');
    });

    it('allows authorized users to access the form', function (): void {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(Form::class, ['post' => $post])->assertOk();
    });
});

describe('Blog Post Form - Creation', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->member = Member::factory()->create(['user_id' => $this->user->id]);
        $this->post = Post::factory()->create([
            'user_id' => $this->user->id,
            'label' => 'Test Post',
            'status' => 'draft',
            'post_type_id' => 1,
        ]);
    });

    it('can create a new blog post with valid data', function (): void {
        Livewire::test(Form::class, ['post' => $this->post])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'label' => 'Test Post',
            'status' => 'draft',
        ]);
    });

    it('validates required fields when creating a post', function (): void {
        $locales = Locale::getNames();

        $assertions = ['form.label' => 'required'];
        foreach ($locales as $locale) {
            $assertions["form.title.{$locale}"] = 'required';
            $assertions["form.slug.{$locale}"] = 'required';
        }

        Livewire::test(Form::class)
            ->call('save')
            ->assertHasErrors($assertions);
    });
});

describe('Blog Post Form - Image Uploads', function (): void {
    beforeEach(function (): void {
        $user = User::factory()->create();
        Member::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        Storage::fake('public');
    });

    it('can upload multiple images with captions', function (): void {
        $locales = Locale::getNames();

        $images = [
            UploadedFile::fake()->image('post-image-1.jpg', 800, 600),
            UploadedFile::fake()->image('post-image-2.jpg', 1024, 768),
        ];

        $captions = [];
        foreach ($locales as $locale) {
            $captions[$locale] = [
                0 => "First Caption {$locale}",
                1 => "Second Caption {$locale}",
            ];
        }

        $titles = array_fill_keys($locales, 'Test Titel');
        $slugs = array_fill_keys($locales, 'test-titel');
        Livewire::test(Form::class)
            ->set('newImages', $images)
            ->set('captions', $captions)
            ->set('form.title', $titles)
            ->set('form.slug', $slugs)
            ->set('form.label', 'what label')
            ->set('form.post_type_id', 1)
            ->set('form.status', EventStatus::DRAFT)
            ->call('save')
            ->assertHasNoErrors();
        Storage::disk('public')->assertExists(
            collect($images)
                ->map(fn ($image): string => 'post-images/'.$image->hashName())
                ->toArray()
        );

        $post = Post::whereJsonContains('title->de', 'Test Titel')->latest()->first();
        $this->assertNotNull($post);
        $this->assertCount(2, $post->images);

        foreach ($locales as $locale) {
            $this->assertEquals("First Caption {$locale}", $post->images[0]->caption[$locale]);
        }
    });

    it('can remove images before saving', function (): void {
        $locales = Locale::getNames();

        $images = [
            UploadedFile::fake()->image('post-image-1.jpg'),
            UploadedFile::fake()->image('post-image-2.jpg'),
        ];

        $titles = array_fill_keys($locales, 'Test Titel');
        $slugs = array_fill_keys($locales, 'test-titel');

        Livewire::test(Form::class)
            ->set('newImages', $images)
            ->call('removeImage', 0)
            ->set('form.title', $titles)
            ->set('form.slug', $slugs)
            ->set('form.label', 'Test Post')
            ->set('form.post_type_id', 2)
            ->set('form.status', EventStatus::DRAFT)
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::whereJsonContains('title->de', 'Test Titel')->latest()->first();
        $this->assertNotNull($post);
        $this->assertCount(1, $post->images);
    });
});

describe('Blog Post Form - Publishing', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    it('can publish a draft post', function (): void {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'draft',
        ]);

        Livewire::test(Form::class, ['post' => $post])
            ->call('publishPost');

        $post->refresh();
        expect($post->status)->toBe('published')
            ->and($post->published_at)->not()->toBeNull();
    });

    it('can retract a published post', function (): void {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        Livewire::test(Form::class, ['post' => $post])
            ->call('resetPublication');

        $post->refresh();
        expect($post->status)->toBe('retracted')
            ->and($post->published_at)->toBeNull();
    });
});

describe('Blog Post Form - Notifications', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    it('can send publication notification', function (): void {
        $mailingService = Mockery::mock(MailingService::class)->makePartial();
        $mailingService->shouldReceive('sendNotificationsToSubscribers')
            ->once()
            ->with('posts', Mockery::type(Post::class), Mockery::type('string'), 'emails.new_post_notification', []);

        $this->app->instance(MailingService::class, $mailingService);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        Livewire::test(Form::class, ['post' => $post])
            ->call('sendPublicationNotification');
    });
});
