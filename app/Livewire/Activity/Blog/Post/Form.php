<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Blog\Post;

use App\Enums\EventStatus;
use App\Livewire\Forms\Blog\PostForm;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\PersistsTabs;
use App\Models\Blog\Post;
use App\Models\Blog\PostImage;
use App\Models\Locale;
use App\Services\MailingService;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

final class Form extends Component
{
    use HasPrivileges, PersistsTabs, WithFileUploads;

    protected $listeners = ['event-id-updated' => 'updatedEventId'];

    public ?Post $post = null;

    public PostForm $form;

    public $defaultTab = 'post-create-head-section-panel';

    public string $selectedTab;

    public string $tabsBody;

    public bool $editPost = false;

    public $locale;

    public $images = [];

    public $newImages = [];

    public array $captionsDe = []; // Captions in German

    public array $captionsHu = []; // Captions in Hungarian

    public array $authors = [];

    public $locals;

    public function mount(?Post $post, MailingService $mailingService): void
    {
        $this->form = new PostForm($this, $post);
        $this->locale = app()->getLocale();
        $this->selectedTab = $this->getSelectedTab();
        $this->tabsBody = 'body-de';

        $this->locals = Locale::getNames();

        if ($post->id) {
            $this->form->set($post->id);
            $this->post = $post;
            $this->editPost = true;
        } else {
            $this->form->post_type_id = 2;
            $this->form->status = EventStatus::DRAFT;
            $this->form->user_id = auth()->id();

        }
    }

    public function updatedEventId(int $eventId): void
    {
        $this->form->event_id = $eventId;

    }

    public function updatedNewImages($value): void
    {
        $this->checkPrivilege(Post::class);

        $this->validate([
            'newImages.*' => 'image|max:10240', // 10MB max per image
        ]);

        // Merge new uploads into the main images array
        if ($this->newImages) {
            $this->images = array_merge($this->images, $this->newImages);
            $this->newImages = []; // Reset for the next upload
        }

    }

    public function makeSlugs(): void
    {
        $this->form->slug['de'] = Str::slug($this->form->title['de']);
        $this->form->slug['hu'] = Str::slug($this->form->title['hu']);
    }

    public function save(): void
    {
        $this->checkPrivilege(Post::class);

        $this->validate([
            'form.label' => 'required|string|max:50',
            'form.title.de' => 'required|string|max:60',
            'form.title.hu' => 'required|string|max:60',
            'form.slug.de' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug->de')->ignore($this->form->id)],
            'form.slug.hu' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug->hu')->ignore($this->form->id)],
            'form.body.de' => 'nullable|string',
            'form.body.hu' => 'nullable|string',
            'form.post_type_id' => 'required|exists:post_types,id',
            'form.status' => ['required', Rule::enum(EventStatus::class)],
            'images.*' => 'nullable|image|max:10240', // 10MB max per image
            'captionsDe.*' => 'nullable|string|max:255',
            'captionsHu.*' => 'nullable|string|max:255',
            'authors.*' => 'nullable|string|max:100',
        ]);

        if ($this->editPost) {
            $post = $this->form->update();
            $this->handleImages($post);
            Flux::toast(text: __('post.form.toasts.edit_success', ['num' => count($post->images)]), heading: __('post.form.toasts.heading.success'), duration: 8000, variant: 'success');
        } else {
            $post = $this->form->create();
            $this->handleImages($post);
            Flux::toast(text: __('post.form.toasts.create_success', ['num' => count($post->images)]), heading: __('post.form.toasts.heading.success'), duration: 8000, variant: 'success');
            $this->redirect(route('backend.posts.show', $post), true);
        }

    }

    protected function handleImages(Post $post): void
    {
        if ($this->images && count($this->images) > 0) {
            foreach ($this->images as $index => $image) {
                $filename = $image->store('post-images', 'public');
                $post->images()->create([
                    'filename' => $filename,
                    'original_filename' => $image->getClientOriginalName(),
                    'caption' => [
                        'de' => $this->captionsDe[$index] ?? '',
                        'hu' => $this->captionsHu[$index] ?? '',
                    ],
                    'author' => $this->authors[$index] ?? null,
                ]);
            }
        }
    }

    public function addDummyData(): void
    {
        $this->form->label = fake()->realText(50);
        $this->form->title['de'] = fake()->realText(50);
        $this->form->slug['de'] = Str::slug(fake()->realText(50));
        $this->form->body['de'] = fake()->randomHtml(20, 8);
        $this->form->title['hu'] = fake()->realText(50);
        $this->form->slug['hu'] = Str::slug(fake()->realTextBetween(20));
        $this->form->body['hu'] = fake()->randomHtml(20, 8);

    }

    public function removeImage($index): void
    {
        unset($this->images[$index]);
        unset($this->captionsDe[$index]);
        unset($this->captionsHu[$index]);
        unset($this->authors[$index]);
        $this->images = array_values($this->images);
        $this->captionsDe = array_values($this->captionsDe);
        $this->captionsHu = array_values($this->captionsHu);
        $this->authors = array_values($this->authors);
    }

    public function deleteImage($imageId): void
    {
        $this->checkPrivilege(Post::class);

        if ($this->editPost && $this->post) {
            /** @var PostImage|null $image */
            $image = $this->post->images()->find($imageId);
            if ($image && Storage::disk('public')->exists($image->filename)) {
                Storage::disk('public')->delete($image->filename);
                $image->delete();
                $this->post->refresh();
                Flux::toast(text: __('post.form.toasts.msg.image_removed'), heading: __('post.form.toasts.heading.success'), duration: 3000, variant: 'success');
            } elseif ($image) {
                // File missing, but still delete DB entry
                $image->delete();
                $this->post->refresh();
                Flux::toast(text: __('post.form.toasts.msg.image_removed_file_missing'), heading: __('post.form.toasts.heading.warning'), duration: 3000, variant: 'warning');
            }
        }
    }

    public function publishPost(): void
    {
        $this->checkPrivilege(Post::class);

        $this->form->published_at = Carbon::now('Europe/Berlin');

        $this->form->status = EventStatus::PUBLISHED->value;

        $this->form->update();

        Flux::toast(text: __('post.form.toasts.msg.post_published'), heading: __('post.form.toasts.heading.success'), duration: 3000, variant: 'success');

    }

    public function resetPublication(): void
    {
        $this->checkPrivilege(Post::class);

        $this->form->published_at = null;

        $this->form->status = EventStatus::RETRACTED->value;

        $this->form->update();

        Flux::toast(text: __('post.form.toasts.msg.post_retracted'), heading: __('post.form.toasts.heading.success'), duration: 3000, variant: 'warning');
    }

    public function sendPublicationNotification(): void
    {
        $this->checkPrivilege(Post::class);

        $mailingService = app(MailingService::class);

        $mailingService->sendNotificationsToSubscribers(
            'posts',
            $this->post,
            __('post.notification_mail.subject'),
            'emails.new_post_notification',
            []
        );
        Flux::toast(text: __('post.form.toasts.notification_sent_success'), heading: __('post.form.toasts.heading.success'), duration: 8000, variant: 'success');

    }

    public function detachFromEvent(): void
    {
        $this->checkPrivilege(Post::class);

        $this->form->event_id = null;

        $this->form->update();

        Flux::toast(text: __('post.form.toasts.eventDetachedSuccess'), heading: __('post.form.toasts.heading.success'), variant: 'success');

    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.blog.post.form');
    }
}
