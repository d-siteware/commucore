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
final class EventDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->string('locale', 'de')->toString();

        return [
            'id' => $this->id,
            'title' => $this->title[$locale] ?? reset($this->title),
            'slug' => $this->slug[$locale] ?? reset($this->slug),
            'event_date' => $this->event_date?->toDateString(),
            'start_time' => $this->start_time?->format('H:i'),
            'end_time' => $this->end_time?->format('H:i'),
            'description' => $this->description[$locale] ?? reset($this->description),
            'image' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,
            'entry_fee' => $this->entry_fee,
            'entry_fee_discounted' => $this->entry_fee_discounted,
            'venue' => $this->whenLoaded('venue', fn () => [
                'name' => $this->venue->name,
                'address' => $this->venue->address,
                'city' => $this->venue->city,
            ]),
            'program' => $this->whenLoaded('timelines', fn () => $this->timelines->map(fn ($t) => [
                'start' => $t->start->format('H:i'),
                'end' => $t->end->format('H:i'),
                'duration' => $t->duration,
                'title' => ($t->title_extern[$locale] ?? null)
                    ?? ($t->title_extern[array_key_first($t->title_extern ?? [])] ?? $t->title),
                'description' => $t->description,
                'place' => $t->place,
                'performer' => $t->performer,
            ])->values()
            ),
            'registration_url' => null,
        ];
    }
}
