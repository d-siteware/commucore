<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Blog\Post;

use App\Enums\EventStatus;
use App\Livewire\Forms\Blog\PostForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Blog\Post;
use App\Models\Locale;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

final class Create extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    use WithFileUploads;

    public PostForm $form;

    public int $step = 1;

    public int $totalSteps = 3;

    /** @var array<string> */
    public array $locales = [];

    /** @var array<int, mixed> */
    public array $images = [];

    /** @var array<int, mixed> */
    public array $newImages = [];

    /**
     * Captions keyed by locale then index: ['de' => [0 => 'caption'], ...]
     *
     * @var array<string, array<int, string>>
     */
    public array $captions = [];

    /** @var array<int, string> */
    public array $authors = [];

    public function mount(): void
    {
        $this->locales = Locale::getNames();

        $this->form->post_type_id = 2;
        $this->form->status = EventStatus::DRAFT;
        $this->form->user_id = auth()->id();

        // Initialize caption arrays per locale
        foreach ($this->locales as $locale) {
            $this->captions[$locale] = [];
        }
    }

    public function updatedNewImages(): void
    {
        $this->checkPrivilege(Post::class);

        $this->validate([
            'newImages.*' => 'image|max:10240',
        ]);

        if ($this->newImages) {
            $this->images = array_merge($this->images, $this->newImages);

            // Extend caption arrays for new images
            foreach ($this->locales as $locale) {
                foreach (array_keys($this->newImages) as $i) {
                    $newIndex = count($this->images) - count($this->newImages) + $i;
                    $this->captions[$locale][$newIndex] ??= '';
                }
            }

            $this->newImages = [];
        }
    }

    public function removeImage(int $index): void
    {
        unset($this->images[$index]);
        unset($this->authors[$index]);

        foreach ($this->locales as $locale) {
            unset($this->captions[$locale][$index]);
            $this->captions[$locale] = array_values($this->captions[$locale]);
        }

        $this->images = array_values($this->images);
        $this->authors = array_values($this->authors);
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->step++;
    }

    public function previousStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function goToStep(int $step): void
    {
        $this->validateCurrentStep();
        $this->step = $step;
    }

    public function makeSlugs(): void
    {
        foreach ($this->locales as $locale) {
            $this->form->slug[$locale] = Str::slug($this->form->title[$locale] ?? '');
        }
    }

    private function validateCurrentStep(): void
    {
        match ($this->step) {
            1 => $this->validate([
                'form.label' => 'required|string|max:50',
                'form.post_type_id' => 'required|exists:post_types,id',
                'form.status' => ['required', Rule::enum(EventStatus::class)],
            ]),
            2 => $this->validateStep2(),
            default => null,
        };
    }

    private function validateStep2(): void
    {
        $rules = [];
        foreach ($this->locales as $locale) {
            $rules["form.title.{$locale}"] = 'required|string|max:60';
            $rules["form.slug.{$locale}"] = ['required', 'string', 'max:255', Rule::unique('posts', "slug->{$locale}")];
            $rules["form.body.{$locale}"] = 'nullable|string';
        }
        $this->validate($rules, [], $this->localeAttributeNames());
    }

    /**
     * @return array<string, string>
     */
    private function localeAttributeNames(): array
    {
        $names = [];
        foreach ($this->locales as $locale) {
            $names["form.title.{$locale}"] = "Titel ({$locale})";
            $names["form.slug.{$locale}"] = "Slug ({$locale})";
            $names["form.body.{$locale}"] = "Inhalt ({$locale})";
        }

        return $names;
    }

    public function save(): void
    {
        try {
            $this->checkPrivilege(Post::class);

            $imageRules = ['images.*' => 'nullable|image|max:10240'];
            foreach ($this->locales as $locale) {
                $imageRules["captions.{$locale}.*"] = 'nullable|string|max:255';
            }
            $imageRules['authors.*'] = 'nullable|string|max:100';

            $this->validate($imageRules);

            $post = $this->form->create();
            $this->handleImages($post);

            Flux::toast(
                text: __('post.form.toasts.create_success', ['num' => count($post->images)]),
                heading: __('post.form.toasts.heading.success'),
                duration: 8000,
                variant: 'success'
            );

            $this->redirect(route('backend.posts.show', $post), true);
        } catch (\Throwable $e) {
            $this->handleError('Beitrag erstellen fehlgeschlagen', $e);
        }
    }

    protected function handleImages(Post $post): void
    {
        foreach ($this->images as $index => $image) {
            $filename = $image->store('post-images', 'public');

            $caption = [];
            foreach ($this->locales as $locale) {
                $caption[$locale] = $this->captions[$locale][$index] ?? '';
            }

            $post->images()->create([
                'filename' => $filename,
                'original_filename' => $image->getClientOriginalName(),
                'caption' => $caption,
                'author' => $this->authors[$index] ?? null,
            ]);
        }
    }

    public function render(): View
    {
        return view('livewire.blog.post.create');
    }
}
