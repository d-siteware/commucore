<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Enums\EventStatus;
use App\Models\Accounting\Account;
use App\Models\Event\Event;

/**
 * Generate an invitation letter for members which have no email address stored
 */
final class EventProgramLetter extends BasePdfTemplate
{
    public function __construct(
        public string $filter,
        public string $filename,
        public string $locale,
    ) {
        parent::__construct($locale, __('event.program_letter.title')); // Pass locale & title
        $this->setTitle(__('event.program_letter.title'));
    }

    public function generateContent(): string
    {
        $hH2 = 14;
        $h = 9;
        $width = 17;
        $zh = 5;

        $this->AddPage();
        $this->SetFont('helvetica', '', $h);

        $events = Event::where('status', '=', EventStatus::PUBLISHED->value);

        if ($this->filter === 'upcoming') {
            $events->whereAfterToday('event_date');
            $this->setTitle(__('event.program_letter.title').'upcoming');
        }
        if ($this->filter === 'year') {
            $events->whereYear('event_date', date('Y'));
            $this->setTitle(__('event.program_letter.title').'year');
        }

        $events->orderBy('event_date', 'ASC');

        if ($events->count() > 0) {
            foreach ($events->get() as $event) {
                $this->SetFont('helvetica', '', $hH2);
                $this->MultiCell(0, $zh, $event->title[$this->locale], 0, 'L', false, 1);
                $this->SetFont('helvetica', '', $h);

                $this->cell($width, $zh, trans('event.event_date', [], $this->locale), 0, 0);
                $this->cell(0, $zh, $event->event_date->isoFormat('LL'), 0, 1);

                $this->cell($width, $zh, trans('event.start_time', [], $this->locale), 0, 0);
                $this->cell(0, $zh, $event->start_time->format('H:i'), 0, 1);

                $this->cell($width, $zh, trans('event.end_time', [], $this->locale), 0, 0);
                $this->cell(0, $zh, $event->end_time->format('H:i'), 0, 1);

                $venue = $event->venue;
                if ($venue) {
                    $this->cell($width, $zh, trans('event.venue', [], $this->locale), 0, 0);
                    $this->cell(0, $zh, $event->location(), 0, 1);
                }

                $this->ln(3);
            }
        }

        /*    $locale = $member->locale;


            $this->cell(0, $zh, $member->name.', '.$member->first_name, 0, 1);
            $this->cell(0, $zh, $member->address, 0, 1);
            $this->cell(0, $zh, $member->zip.' - '.$member->city, 0, 1);
            if ($member->country !== '' && $member->country !== 'DE' && $member->country !== 'Deutschland') {
                $this->cell(0, $zh, $member->country, 0, 1);
            }
            //        $this->writeHTML($this->content, true, false, true, false, '');
            $this->ln(10);

            $this->SetFont('helvetica', 'B', $hH1);
            $this->Cell(0, 10, trans('event.notification_letter.title', [], $locale), 0, 1);

            $this->ln(10);

            $this->SetFont('helvetica', '', $h);
            $this->cell(0, 10, trans('event.notification_letter.greeting', ['name' => $member->first_name], $locale), 0, 1);

            $this->MultiCell(0, 10, trans('event.notification_letter.text', ['datum' => $this->event->event_date->isoFormat('LL')], $locale), '', 'L', false, 1);

            $this->ln(2);
            $this->MultiCell(0, 10, $this->event->description[$locale], '', 'L', false, 1);

            $this->ln(2);
            $this->SetFont('helvetica', '', $hH2);
            $this->cell(0, $zh, trans('event.notification_letter.overview', [], $locale), 0, 1);
            $this->ln(1);

            $this->SetFont('helvetica', '', $h);
            $this->cell($width, $zh, trans('event.title.de', [], $locale), 0, 0);
            $this->cell(0, $zh, $this->event->title[$locale], 0, 1);

            $this->cell($width, $zh, trans('event.event_date', [], $locale), 0, 0);
            $this->cell(0, $zh, $this->event->event_date->isoFormat('LL'), 0, 1);

            $venue = $this->event->venue;
            if ($venue) {
                $this->cell($width, $zh, trans('event.venue', [], $locale), 0, 0);
                $this->cell(0, $zh, $this->event->location(), 0, 1);
            }

            $this->cell($width, $zh, trans('event.start_time', [], $locale), 0, 0);
            $this->cell(0, $zh, $this->event->start_time->format('H:i'), 0, 1);

            $this->cell($width, $zh, trans('event.end_time', [], $locale), 0, 0);
            $this->cell(0, $zh, $this->event->end_time->format('H:i'), 0, 1);

            $this->cell($width, $zh, trans('event.form.entry_fee', [], $locale), 0, 0);
            $this->cell(0, $zh, Account::formatedAmount($this->event->entry_fee).' Euro', 0, 1);

            $this->cell($width, $zh, trans('event.entry_fee_discounted', [], $locale), 0, 0);
            $this->cell(0, $zh, Account::formatedAmount($this->event->entry_fee_discounted).' Euro', 0, 1);

            if ($this->event->timelines()->count() > 0) {
                $this->ln(5);
                $this->cell(0, $zh, trans('event.notification_letter.timelines.header', [], $locale), 0, 1);
                $this->ln(1);

                foreach ($this->event->timelines as $timeline) {
                    $this->Cell(120, $zh, $timeline->title_extern[$locale], 0, 0);
                    $this->Cell(10, $zh, trans('event.timeline.start', [], $locale), 0, 0);
                    $this->Cell(10, $zh, $timeline->start->format('H:i'), 0);
                    $this->Cell(10, $zh, trans('event.timeline.end', [], $locale), 0, 0);
                    $this->Cell(10, $zh, $timeline->end->format('H:i'), 0, 1);
                }

            } else {
                $this->Cell(0, 10, trans('event.notification_letter.timelines.empty', [], $locale), 0, 1);
            }

            $this->Cell(0, 10, trans('event.notification_letter.closing_text', [], $locale), 0, 1);
            $this->Cell(0, 10, trans('event.notification_letter.signature', [], $locale), 0, 1);

            $this->ln(20);
            $this->Cell(0, 10, trans('event.notification_letter.board', [], $locale), 0, 1);

      */

        return $this->Output($this->filename, 'S'); // 'D' = Download, 'I' = Inline
    }
}
