<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Models\Protocols\Minutes\MeetingMinute;
use Illuminate\Support\Facades\View;

final class MeetingMinutesPdf extends BasePdfTemplate
{
    protected MeetingMinute $meetingMinute;

    public function __construct(MeetingMinute $meetingMinute, string $locale = 'en')
    {
        $this->meetingMinute = $meetingMinute;
        parent::__construct($locale, __('minutes.pdf.title'));
    }

    public function generateContent(): void
    {
        $this->AddPage();

        // Set font for content
        $this->SetFont('helvetica', '', 10);

        // Render the Blade component as HTML
        $html = View::make('components.meeting-minute-details', [
            'meetingMinute' => $this->meetingMinute->load(['attendees', 'topics.actionItems.assignee']),
        ])->render();

        // Write HTML to PDF
        $this->writeHTML($html, true, false, true, false, '');
    }
}
