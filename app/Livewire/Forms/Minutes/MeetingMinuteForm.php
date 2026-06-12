<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Minutes;

use App\Models\Protocols\Minutes\MeetingMinute;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class MeetingMinuteForm extends Form
{
    public MeetingMinute $meetingMinute;

    public Collection $attendes;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|date')]
    public string $meeting_date = '';

    #[Validate('nullable|string|max:255')]
    public ?string $location = null;

    #[Validate('nullable')]
    public ?string $content = null;

    #[Validate('nullable|date')]
    public ?string $stored_at = null;

    public function init(): void
    {
        $this->title = __('minutes.create.default_title');
        $this->meeting_date = now()->format('Y-m-d');
        $this->location = null;
    }

    public function loadMeeting(MeetingMinute $meetingMinute): void
    {
        $this->title = $meetingMinute->title;
        $this->meeting_date = $meetingMinute->meeting_date->format('Y-m-d');
        $this->location = $meetingMinute->location;
        $this->content = $meetingMinute->content;

        $this->attendes = $meetingMinute->attendees;

    }

    protected function messages(): array
    {
        return [
            'title.required' => __('minutes.validation_error.title.required'),
            'meeting_date.required' => __('minutes.validation_error.meeting_date.required'),
        ];
    }
}
