<?php

declare(strict_types=1);

namespace App\Actions\Minutes;

use App\Livewire\Forms\Minutes\MeetingMinuteForm;
use App\Models\Protocols\Minutes\MeetingMinute;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

final class CreateMeetingMinute extends Action
{
    public static function handle(MeetingMinuteForm $form): MeetingMinute
    {
        return DB::transaction(function () use ($form) {

            return MeetingMinute::create([
                'title' => $form->title,
                'meeting_date' => $form->meeting_date,
                'location' => $form->location,
            ]);

        });

    }
}
