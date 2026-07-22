<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Livewire\Forms\Event\EventForm;
use App\Models\Accounting\Account;
use App\Models\Event\Event;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreateEvent
{
    /**
     * @throws Throwable
     */
    public static function handle(EventForm $eventForm): Event
    {
        return DB::transaction(function () use ($eventForm) {
            return Event::create([
                'name' => $eventForm->name,
                'type' => $eventForm->type,
                'venue_id' => $eventForm->venue_id,
                'event_date' => $eventForm->event_date,
                'start_time' => $eventForm->start_time,
                'end_time' => $eventForm->end_time,
                'title' => $eventForm->title,
                'slug' => $eventForm->slug,
                'excerpt' => $eventForm->excerpt,
                'description' => $eventForm->description,
                'image' => $eventForm->image,
                'status' => $eventForm->status,
                'payment_link' => $eventForm->payment_link,
                'entry_fee' => Account::makeCentInteger($eventForm->entry_fee),
                'entry_fee_discounted' => Account::makeCentInteger($eventForm->entry_fee_discounted),
            ]);
        });
    }
}
