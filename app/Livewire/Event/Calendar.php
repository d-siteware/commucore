<?php

declare(strict_types=1);

namespace App\Livewire\Event;

use App\Enums\EventStatus;
use App\Models\Event\Event;
use Carbon\Carbon;
use Livewire\Component;

final class Calendar extends Component
{
    public $selectedMonth;

    public $selectedYear;

    public $events = [];

    public $currentDate;

    public $locale = 'de'; // Default locale

    public function mount(): void
    {
        $this->locale = app()->getLocale();
        $this->currentDate = Carbon::now();
        $this->selectedMonth = $this->currentDate->month;
        $this->selectedYear = $this->currentDate->year;
        $this->loadEvents();
    }

    public function updatedSelectedMonth(): void
    {
        $this->loadEvents();
    }

    public function updatedSelectedYear(): void
    {
        $this->loadEvents();
    }

    public function loadEvents(): void
    {
        $this->events = Event::select(['id', 'event_date', 'start_time', 'title', 'excerpt', 'slug'])
            ->whereMonth('event_date', $this->selectedMonth)
            ->whereYear('event_date', $this->selectedYear)
            ->where('status', EventStatus::PUBLISHED->value)
            ->get()
            ->map(function ($event): array {
                return [
                    'id' => $event->id,
                    'event_date' => $event->event_date,
                    'start_time' => $event->start_time ? Carbon::parse($event->start_time)->format('H:i') : null,
                    'title' => $event->title[$this->locale] ?? array_values($event->title)[0] ?? 'No title',
                    'slug' => $event->slug[$this->locale] ?? array_values($event->slug)[0] ?? '#',
                    'excerpt' => $event->excerpt[$this->locale] ?? is_array($event->excerpt) ? array_values($event->excerpt)[0] : null,
                ];
            });
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth)->subMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->loadEvents();
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->loadEvents();
    }

    public function goToToday(): void
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->loadEvents();
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1, 0, 0, 0, 'Europe/Berlin');
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        // Adjust for Monday-first week: Mon=0, Tue=1, ..., Sun=6
        $firstDayOfWeek = ($startOfMonth->dayOfWeek + 6) % 7;
        $days = [];

        // Add days from previous month
        $prevMonthDays = $firstDayOfWeek;
        $prevMonth = $startOfMonth->copy()->subMonth();
        for ($i = $prevMonthDays - 1; $i >= 0; $i--) {
            $days[] = ['date' => $prevMonth->copy()->endOfMonth()->subDays($i), 'isCurrentMonth' => false];
        }

        // Add days of current month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $days[] = ['date' => $startOfMonth->copy()->setDay($day), 'isCurrentMonth' => true];
        }

        // Add days from next month to fill 6 rows (42 cells)
        $totalDays = count($days);
        $nextMonth = $startOfMonth->copy()->addMonth();
        for ($i = 1; $totalDays < 42; $i++) {
            $days[] = ['date' => $nextMonth->copy()->setDay($i), 'isCurrentMonth' => false];
            $totalDays++;
        }

        return view('livewire.event.calendar', [
            'days' => $days,
            'startOfMonth' => $startOfMonth,
            'events' => $this->events,
            'currentDate' => $this->currentDate,
        ]);
    }
}
