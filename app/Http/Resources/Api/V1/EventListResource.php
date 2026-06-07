<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Event\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Event
 */
final class EventListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->string('locale', 'de')->toString();
        $title = $this->title;
        $slug = $this->slug;
        $excerpt = $this->excerpt;

        return [
            'id' => $this->id,
            'title' => $title[$locale] ?? reset($title),
            'slug' => $slug[$locale] ?? reset($slug),
            'event_date' => $this->event_date?->toDateString(),
            'start_time' => $this->start_time?->format('H:i'),
            'end_time' => $this->end_time?->format('H:i'),
            'excerpt' => $excerpt[$locale] ?? reset($excerpt),
            'image' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,
            'entry_fee' => $this->entry_fee,
        ];
    }
}
