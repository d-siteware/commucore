<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Blog\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
final class PostListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->string('locale', 'de')->toString();

        return [
            'id' => $this->id,
            'title' => $this->title[$locale] ?? reset($this->title),
            'slug' => $this->slug[$locale] ?? reset($this->slug),
            'published_at' => $this->published_at?->toDateString(),
            'excerpt' => $this->excerpt(160),
            'label' => $this->label,
            'event' => $this->whenLoaded('event', fn () => $this->event ? [
                'id' => $this->event->id,
                'title' => $this->event->title[$locale] ?? reset($this->event->title),
            ] : null),
        ];
    }
}
