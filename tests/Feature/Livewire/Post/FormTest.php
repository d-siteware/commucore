<?php

declare(strict_types=1);

use App\Livewire\Activity\Blog\Post\Form;
use App\Models\Blog\Post;
use App\Models\Membership\Member;
use App\Models\User;
use App\Services\MailingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('can access the create post page', function (): void {
    $this->member = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id]);
    $this->user = $this->member->user;
    $this->actingAs($this->user); // Authenticate the user
    $response = $this->get(route('backend.posts.create'));
    $response->assertStatus(200);
});

// Authentication and Authorization Tests
describe('Blog Post Form - Authorization', function (): void {
    it('prevents unauthorized users from accessing the form', function (): void {
        $post = Post::factory()->create();

        // Attempt to access form without authentication
        $response = $this->get('/backend/posts/', [$post])
            ->assertRedirect('/login');
    });

    it('allows authorized users to access the form', function (): void {
        $user = User::factory()
            ->create();
        $post = Post::factory()
            ->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $component = Livewire::test(Form::class, ['post' => $post]);
        $component->assertOk();
    });
});

// Post Creation Tests
describe('Blog Post Form - Creation', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()
            ->create();
        $this->actingAs($this->user);
        $this->post = Post::factory()
            ->create(['user_id' => $this->user->id,
                'label' => 'Test Post',
                'status' => 'draft',
                'post_type_id' => 1,
            ]);
        $this->member = Member::factory()->create(['user_id' => $this->user->id]);
    });

    it('can create a new blog post with valid data', function (): void {

        $component = Livewire::test(Form::class, ['post' => $this->post, 'user' => $this->user, 'member' => $this->member])
            ->call('save');

        $component->assertHasNoErrors();

        $this->post->refresh();

        $this->assertDatabaseHas('posts', [
            'label' => 'Test Post',
            'status' => 'draft',
        ]);
    });

    it('validates required fields when creating a post', function (): void {
        $component = Livewire::test(Form::class)
            ->call('save');

        $component->assertHasErrors([
            'form.label' => 'required',
            'form.title.de' => 'required',
            'form.title.hu' => 'required',
            'form.slug.de' => 'required',
            'form.slug.hu' => 'required',
            //            'form.post_type_id' => 'required',
            //            'form.status' => 'required',
        ]);
    });
});

// Image Upload Tests
describe('Blog Post Form - Image Uploads', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->member = Member::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
        Storage::fake('public');
        //        \App\Models\Blog\PostType::factory()->create(['id' => 1]);
        //        \App\Models\Blog\PostType::factory()->create(['id' => 2]);
    });

    it('can upload multiple images with captions', function (): void {
        $images = [
            UploadedFile::fake()->image('post-image-1.jpg', 800, 600),
            UploadedFile::fake()->image('post-image-2.jpg', 1024, 768),
        ];

        $component = Livewire::test(Form::class)
            ->set('newImages', $images)
            ->set('captionsDe', ['First Image Caption', 'Second Image Caption'])
            ->set('captionsHu', ['Első kép felirata', 'Második kép felirata'])
            ->set('form.title', ['de' => 'Test Titel', 'hu' => 'Test Cím'])
            ->set('form.slug', ['de' => 'Test Slug', 'hu' => 'Test Cím-Slug'])
            ->set('form.label', 'what label')
            ->set('form.post_type_id', 1)
            ->set('form.status', \App\Enums\EventStatus::DRAFT)
            ->call('save');

        $component->assertHasNoErrors();

        $storedPaths = collect($images)->map(fn ($image): string => 'post-images/'.$image->hashName())->toArray();
        Storage::disk('public')->assertExists($storedPaths);

        $post = Post::latest()->first();
        $this->assertCount(2, $post->images);
        $this->assertEquals('First Image Caption', $post->images[0]->caption['de']);
        $this->assertEquals('Első kép felirata', $post->images[0]->caption['hu']);
    });

    it('can remove images before saving', function (): void {
        $images = [
            UploadedFile::fake()->image('post-image-1.jpg'),
            UploadedFile::fake()->image('post-image-2.jpg'),
        ];

        $component = Livewire::test(Form::class)
            ->set('newImages', $images)
            ->call('removeImage', 0)
            ->set('form.title', ['de' => 'Test Titel', 'hu' => 'Test Cím'])
            ->set('form.slug', ['de' => 'Test Slug', 'hu' => 'Test Cím-Slug'])
            ->set('form.label', 'Test Post')
            ->set('form.post_type_id', 2)
            ->set('form.status', \App\Enums\EventStatus::DRAFT)
            ->call('save');

        $component->assertHasNoErrors();

        $post = Post::latest()->first();
        $this->assertCount(1, $post->images);
    });
});

// Post Publishing Tests
describe('Blog Post Form - Publishing', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()
            ->create();
        $this->actingAs($this->user);
        $this->member = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id]);
    });

    it('can publish a draft post', function (): void {
        $post = Post::factory()
            ->create([
                'user_id' => $this->user->id,
                'status' => 'draft',
            ]);

        $component = Livewire::test(Form::class, ['post' => $post])
            ->call('publishPost')
            ->assertJson([
                'components' => [
                    [
                        'effects' => [
                            'dispatches' => [
                                [
                                    'name' => 'toast-show',
                                    'params' => [
                                        'duration' => 5000,
                                        'slots' => [
                                            'text' => 'You have no persmission to access App\\Models\\Blog\\Post. => This action is unauthorized.',
                                            'heading' => 'Forbidden',
                                        ],
                                        'dataset' => [
                                            'variant' => 'danger',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'toast-show',
                                    'params' => [
                                        'duration' => '3000',
                                        'slots' => [
                                            'text' => __('post.form.toasts.msg.post_published'),
                                            'heading' => 'Erfolg!',
                                        ],
                                        'dataset' => [
                                            'variant' => 'success',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $post->refresh();
        expect($post->status)
            ->toBe('published')
            ->and($post->published_at)
            ->not()
            ->toBeNull();
    });

    it('can retract a published post', function (): void {
        $post = Post::factory()
            ->create([
                'user_id' => $this->user->id,
                'status' => 'published',
                'published_at' => now(),
            ]);

        $component = Livewire::test(Form::class, ['post' => $post])
            ->call('resetPublication')
            ->assertJson([
                'components' => [
                    [
                        'effects' => [
                            'dispatches' => [
                                [
                                    'name' => 'toast-show',
                                    'params' => [
                                        'duration' => 5000,
                                        'slots' => [
                                            'text' => 'You have no persmission to access App\\Models\\Blog\\Post. => This action is unauthorized.',
                                            'heading' => 'Forbidden',
                                        ],
                                        'dataset' => [
                                            'variant' => 'danger',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'toast-show',
                                    'params' => [
                                        'duration' => '3000',
                                        'slots' => [
                                            'text' => 'Der Artikel wurde zurückgezogen!',
                                            'heading' => 'Erfolg!',
                                        ],
                                        'dataset' => [
                                            'variant' => 'warning',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $post->refresh();
        expect($post->status)
            ->toBe('retracted')
            ->and($post->published_at)
            ->toBeNull();
    });
});

// Notification Tests
describe('Blog Post Form - Notifications', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()
            ->create();
        $this->actingAs($this->user);
        $this->member = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id]);
    });

    it('can send publication notification', function (): void {
        $mailingService = \Mockery::mock(MailingService::class, [])->makePartial();
        $mailingService->shouldReceive('sendNotificationsToSubscribers')
            ->once()
            ->with('posts', Mockery::type(Post::class), Mockery::type('string'), 'emails.new_post_notification', []);

        $this->app->instance(MailingService::class, $mailingService);

        $post = Post::factory()
            ->create([
                'user_id' => $this->user->id,
                'status' => 'published',
            ]);

        Livewire::test(Form::class, ['post' => $post])
            ->call('sendPublicationNotification')
            ->assertJson([
                'components' => [
                    [
                        'effects' => [
                            'dispatches' => [
                                [
                                    'name' => 'toast-show',
                                    'params' => [
                                        'duration' => 5000,
                                        'slots' => [
                                            'text' => 'You have no persmission to access App\\Models\\Blog\\Post. => This action is unauthorized.',
                                            'heading' => 'Forbidden',
                                        ],
                                        'dataset' => [
                                            'variant' => 'danger',
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'toast-show',
                                    'params' => [
                                        'duration' => 8000,
                                        'slots' => [
                                            'text' => __('post.form.toasts.notification_sent_success'),
                                            'heading' => __('post.form.toasts.heading.success'),
                                        ],
                                        'dataset' => [
                                            'variant' => 'success',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    });
});
