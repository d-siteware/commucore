<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Livewire\Forms\Event\EventTimelineForm;
use App\Models\Event\EventTimeline;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UpdateTimeline
{
    /**
     * @throws Throwable
     */
    public static function handle(EventTimelineForm $form): EventTimeline
    {
        return DB::transaction(function () use ($form) {
            $eventTimeline = EventTimeline::findOrFail($form->id);
            if ($eventTimeline->update([
                'start' => $form->start,
                'duration' => $form->duration,
                'end' => $form->end,
                'event_id' => $form->event_id,
                'title' => $form->title,
                'description' => $form->description,
                'notes' => $form->notes,
                'member_id' => $form->member_id,
                'user_id' => $form->user_id,
                'place' => $form->place,
                'performer' => $form->performer,
                'title_extern' => $form->title_extern,
            ])) {
                return $eventTimeline;
            } else {
                return null;
            }
        });
    }
}
