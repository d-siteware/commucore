<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Blog\Post;

use App\Enums\EventStatus;
use App\Livewire\Forms\Blog\PostForm;
use App\Livewire\Traits\HandlesErrors;
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
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

final class Form extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    use PersistsTabs;
    use WithFileUploads;

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

    public array $captions = []; // Captions in Hungarian

    public array $authors = [];

    public ?array $locals;

    public bool $isMultiLanguage = false;

    public function mount(?Post $post, MailingService $mailingService): void
    {
        $this->form = new PostForm($this, $post);
        $this->locale = app()->getLocale();
        $this->selectedTab = $this->getSelectedTab();
        $this->tabsBody = 'body-de';

        $this->locals = Locale::getNames();
        $this->isMultiLanguage = Locale::isMultiLanguage();

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

    public function updatedNewImages(): void
    {
        $this->checkPrivilege(Post::class);

        $this->validate(['newImages.*' => 'image|max:10240']);

        if ($this->newImages) {
            $newStartIndex = count($this->images);

            foreach ($this->newImages as $i => $image) {
                $this->images[] = $image;
                foreach (Locale::getNames() as $locale) {
                    $this->captions[$locale] ??= [];
                    $this->captions[$locale][$newStartIndex + $i] ??= '';
                }
            }

            $this->newImages = [];
        }
    }

    public function makeSlugs(): void
    {
        foreach ($this->locals as $locale) {
            $this->form->slug[$locale] = Str::slug($this->form->title[$locale] ?? '');
        }
    }

    public function save(): void
    {
        try {
            $this->checkPrivilege(Post::class);

            $rules = [
                'form.label' => 'required|string|max:50',
                'form.post_type_id' => 'required|exists:post_types,id',
                'form.status' => ['required', Rule::enum(EventStatus::class)],
                'images.*' => 'nullable|image|max:10240',
                'authors.*' => 'nullable|string|max:100',
            ];

            foreach (Locale::getNames() as $locale) {
                $rules['form.title.'.$locale] = 'required|string|max:60';
                $rules['form.slug.'.$locale] = ['required', 'string', 'max:255', Rule::unique('posts', 'slug->'.$locale)];
                $rules['form.body.'.$locale] = 'nullable|string';
                $rules['captions.'.$locale.'.*'] = 'nullable|string|max:255';
                $rules['form.slug.'.$locale] = [
                    'required', 'string', 'max:255',
                    Rule::unique('posts', 'slug->'.$locale)->ignore($this->form->id),
                ];
            }

            $this->validate($rules);

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
        } catch (\Throwable $e) {
            $this->handleError('Beitrag speichern fehlgeschlagen', $e);
        }
    }

    protected function handleImages(Post $post): void
    {
        foreach ($this->images as $index => $image) {
            $filename = $image->store('post-images', 'public');

            $caption = [];
            foreach (Locale::getNames() as $locale) {
                $caption[$locale] = $this->captions[$locale][$index] ?? '';
            }

            $post->images()->create([
                'filename' => $filename,
                'original_filename' => $image->getClientOriginalName(),
                'caption' => $caption,
                'author' => $this->authors[$index] ?? null,
            ]);
        }

        $this->images = [];
        $this->newImages = [];
    }

    public function addDummyData(): void
    {
        $this->form->label = fake()->realText(50);

        foreach (Locale::getNames() as $locale) {
            $this->form->title[$locale] = fake()->realText(50);
            $this->form->slug[$locale] = Str::slug(fake()->realText(50));
            $this->form->body[$locale] = fake()->randomHtml(20, 8);
        }
    }

    public function removeImage(int $index): void
    {
        // Aus beiden Arrays entfernen
        unset($this->images[$index]);
        unset($this->newImages[$index]);
        unset($this->authors[$index]);

        foreach (Locale::getNames() as $locale) {
            unset($this->captions[$locale][$index]);
            $this->captions[$locale] = array_values($this->captions[$locale] ?? []);
        }

        $this->images = array_values($this->images);
        $this->newImages = array_values($this->newImages);
        $this->authors = array_values($this->authors);
    }

    public function deleteImage($imageId): void
    {
        try {
            $this->checkPrivilege(Post::class);

            if ($this->editPost && $this->post) {
                /** @var PostImage|null $image */
                $image = $this->post->images()
                    ->find($imageId);
                if ($image && Storage::disk('public')
                    ->exists($image->filename)) {
                    Storage::disk('public')
                        ->delete($image->filename);
                    $image->delete();
                    $this->post->refresh();
                    Flux::toast(text: __('post.form.toasts.msg.image_removed'), heading: __('post.form.toasts.heading.success'), duration: 3000, variant: 'success');
                } elseif ($image) {
                    $image->delete();
                    $this->post->refresh();
                    Flux::toast(text: __('post.form.toasts.msg.image_removed_file_missing'), heading: __('post.form.toasts.heading.warning'), duration: 3000, variant: 'warning');
                }
            }
        } catch (\Throwable $e) {
            $this->handleError('Bild löschen fehlgeschlagen', $e);
        }
    }

    public function publishPost(): void
    {
        try {
            $this->checkPrivilege(Post::class);

            $this->form->published_at = Carbon::now('Europe/Berlin');

            $this->form->status = EventStatus::PUBLISHED->value;

            $this->form->update();

            Flux::toast(text: __('post.form.toasts.msg.post_published'), heading: __('post.form.toasts.heading.success'), duration: 3000, variant: 'success');
        } catch (\Throwable $e) {
            $this->handleError('Beitrag veröffentlichen fehlgeschlagen', $e);
        }
    }

    public function resetPublication(): void
    {
        try {
            $this->checkPrivilege(Post::class);

            $this->form->published_at = null;

            $this->form->status = EventStatus::RETRACTED->value;

            $this->form->update();

            Flux::toast(text: __('post.form.toasts.msg.post_retracted'), heading: __('post.form.toasts.heading.success'), duration: 3000, variant: 'warning');
        } catch (\Throwable $e) {
            $this->handleError('Veröffentlichung zurücknehmen fehlgeschlagen', $e);
        }
    }

    public function sendPublicationNotification(): void
    {
        try {
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
        } catch (\Throwable $e) {
            $this->handleError('Benachrichtigung senden fehlgeschlagen', $e);
        }
    }

    public function detachFromEvent(): void
    {
        try {
            $this->checkPrivilege(Post::class);

            $this->form->event_id = null;

            $this->form->update();

            Flux::toast(text: __('post.form.toasts.eventDetachedSuccess'), heading: __('post.form.toasts.heading.success'), variant: 'success');
        } catch (\Throwable $e) {
            $this->handleError('Event-Verknüpfung trennen fehlgeschlagen', $e);
        }
    }

    public function render(): View
    {
        return view('livewire.blog.post.form');
    }
}
