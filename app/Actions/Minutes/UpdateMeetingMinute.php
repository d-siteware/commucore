<?php

declare(strict_types=1);

namespace App\Actions\Minutes;

use App\Livewire\Forms\Minutes\MeetingMinuteForm;
use App\Models\MeetingMinute;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

final class UpdateMeetingMinute extends Action
{
    public static function handle(MeetingMinuteForm $meetingForm, MeetingMinute $meetingMinute, ?string $storedAt = null): MeetingMinute
    {
        return DB::transaction(function () use ($meetingForm, $meetingMinute): \App\Models\MeetingMinute {
            $meetingMinute->update([
                'title' => $meetingForm->title,
                'meeting_date' => $meetingForm->meeting_date,
                'location' => $meetingForm->location,
            ]);

            return $meetingMinute;
        });
    }
}
