<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\MeetingMinutes;

use App\Livewire\Traits\Sortable;
use App\Models\Protocols\Minutes\MeetingMinute;
use App\Services\PdfGeneratorService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use Sortable;
    use WithPagination;

    public string $search = '';

    public ?MeetingMinute $selectedMeeting = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function minutes(): LengthAwarePaginator
    {
        $query = MeetingMinute::query()->with(['attendees', 'topics:id,content', 'actionItems']);

        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        if ($this->search !== '' && $this->search !== '0') {
            $query->where('title', 'LIKE', '%'.$this->search.'%')
                ->orWhereHas('topics', function ($q): void {
                    $q->where('content', 'LIKE', '%'.$this->search.'%');
                });
        }

        return $query->paginate(10);

    }

    public function render(): View
    {
        return view('livewire.app.tool.meeting-minutes.index')
            ->title(__('minutes.index.page_title'));
    }

    public function fetchMeetingMinutes(int $meetingId): void
    {
        $this->authorize('view', MeetingMinute::class);
        $this->selectedMeeting = MeetingMinute::with(['attendees', 'topics.actionItems.assignee'])
            ->findOrFail($meetingId);
    }

    public function editMeetingMinutes(int $meetingId): void
    {
        // TODO: Redirect to edit page
        $this->authorize('update', MeetingMinute::findOrFail($meetingId));
        $this->redirect(route('minutes.edit', $meetingId), navigate: true);
    }

    /**
     * @return ResponseFactory|Application|Response|object|void
     */
    public function printMeetingMinutes(int $meetingId)
    {
        $meeting = MeetingMinute::findOrFail($meetingId);
        $this->authorize('view', $meeting);

        try {
            $pdfContent = app(PdfGeneratorService::class)->generate(
                type: 'meeting-minute',
                data: $meeting,
                restricted: true
            );

            $filename = "meeting-minute-{$meetingId}-".now()->format('Ymd').'.pdf';

            return response()->streamDownload(
                fn () => print ($pdfContent),
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => "inline; filename=\"{$filename}\"",
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to generate meeting minute PDF');
            session()->flash('error', __('minutes.pdf.error'));
            $this->redirect(route('minutes.index'), navigate: true);
        }
    }
}
