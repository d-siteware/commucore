<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Blog\Post;
use App\Models\Blog\PostImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
final class PostDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->string('locale', 'de')->toString();

        return [
            'id' => $this->id,
            'title' => $this->title[$locale] ?? reset($this->title),
            'slug' => $this->slug[$locale] ?? reset($this->slug),
            'published_at' => $this->published_at?->toDateString(),
            'body' => $this->body[$locale] ?? reset($this->body),
            'label' => $this->label,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn (PostImage $img) => [
                'url' => url($img->filename),
                'alt' => $img->alt ?? '',
                'caption' => isset($img->caption)
                    ? ($img->caption[$locale] ?? reset($img->caption) ?? null)
                    : null,
                'author' => $img->author ?? '',
            ])->values()
            ),

            'event' => $this->whenLoaded('event', fn () => $this->event ? [
                'id' => $this->event->id,
                'title' => $this->event->title[$locale] ?? reset($this->event->title),
                'event_date' => $this->event->event_date?->toDateString(),
            ] : null),
        ];
    }
}
