<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Blog\Post;
use App\Models\Locale;
use Flux\Flux;
use Illuminate\View\View;

final class PostController extends Controller
{
    public function index(): View
    {
        return view('posts.index', [
            'posts' => Post::query()
                ->where('posts.status', EventStatus::PUBLISHED->value)
                ->whereNotNull('published_at')
                ->get(),
            'locale' => app()->getLocale(),
        ]);
    }

    public function show(string $slug): View
    {

        foreach (Locale::getNames() as $locale) {
            $post = Post::query()
                ->with('images')
                ->whereJsonContains("slug->{$locale}", $slug)
                ->first();

            if ($post) {
                return view('posts.show', [
                    'post'   => $post,
                    'images' => $post->images,
                    'locale' => $locale,
                ]);
            }
        }

        Flux::toast('Post not found!', 'Fehler');

        return view('posts.index');

    }
}
