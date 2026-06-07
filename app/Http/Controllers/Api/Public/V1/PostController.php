<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostDetailResource;
use App\Http\Resources\Api\V1\PostListResource;
use App\Models\Blog\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PostController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $locale = $request->string('locale', 'de')->toString();
        $limit = min((int) $request->integer('limit', 10), 50);

        $posts = Post::query()
            ->published()
            ->with('event')
            ->orderByDesc('published_at')
            ->paginate($limit);

        return PostListResource::collection($posts)
            ->additional(['locale' => $locale]);
    }

    public function show(Request $request, Post $post): PostDetailResource
    {
        $locale = $request->string('locale', 'de')->toString();

        abort_if(! $post->isPublished(), 404);

        $post->load(['images', 'event']);

        return (new PostDetailResource($post))
            ->additional(['locale' => $locale]);
    }
}
