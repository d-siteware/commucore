<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Event\Show;

use App\Enums\AssignmentStatus;
use App\Enums\EventStatus;
use App\Livewire\Forms\Event\AssignmentForm;
use App\Livewire\Forms\Event\EventForm;
use App\Livewire\Forms\Event\EventTimelineForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\PersistsTabs;
use App\Livewire\Traits\Sortable;
use App\Models\Event\Event;
use App\Models\Event\EventAssignment;
use App\Models\Event\EventSubscription;
use App\Models\Event\EventTimeline;
use App\Models\Event\EventTransaction;
use App\Models\Event\EventVisitor;
use App\Models\History;
use App\Models\Membership\Member;
use App\Models\User;
use App\Models\Venue;
use App\Services\MailingService;
use App\Services\PdfGeneratorService;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    use PersistsTabs;
    use Sortable;
    use WithPagination;

    public EventForm $form;

    public AssignmentForm $assignmentForm;

    public EventTimelineForm $timelineForm;

    public ?int $event_id;

    public Event $event;

    public ?Collection $venues;

    public string $venuesKey = '';

    public ?string $defaultTab = 'event-show-dates';

    public ?string $selectedTab;

    public ?int $selectedRow;

    public string $searchVisitor = '';

    #[Computed]
    public function subscriptions(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return EventSubscription::where('event_id', $this->event_id)
            ->paginate(10);
    }

    #[Computed]
    public function histories(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return History::query()->with('user')
            ->where('historable_id', $this->event_id)
            ->where('historable_type', Event::class)
            ->orderByDesc('changed_at')->paginate(10);
    }

    #[Computed]
    public function assignments(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return EventAssignment::where('event_id', $this->event_id)
            ->paginate(10);
    }

    #[Computed]
    public function payments(): LengthAwarePaginator
    {
        return EventTransaction::query()
            ->with('transaction')
            ->where('event_id', '=', $this->event_id)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }

    #[Computed]
    public function visitors(): LengthAwarePaginator
    {
        return EventVisitor::query()
            ->with('transaction:id,amount_gross,status')
            ->with('member:id,name,first_name')
            ->where('event_id', '=', $this->event_id)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->tap(fn ($query) => $this->searchVisitor !== '' && $this->searchVisitor !== '0' ? $query->whereLike('name', '%'.$this->searchVisitor.'%')->orWhereLike('email', '%'.$this->searchVisitor.'%') : $query)
            ->paginate(10);
    }

    #[On('updated-payments')]
    #[On('event-visitor-added')]
    public function refreshLists(): void {}

    #[Computed]
    public function timelineItems(): LengthAwarePaginator
    {
        return EventTimeline::query()
            ->with('member:id,name,first_name')
            ->where('event_id', '=', $this->event_id)
            ->orderBy('start', 'asc')
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }

    #[On('venue-created')]
    #[On('venue-updated')]
    public function updatedVenue(int $venueId): void
    {
        $this->loadVenues();
        $this->form->venue_id = $venueId;
        $this->dispatch('venues-refreshed');
        //        dd($this->venues->pluck('name'));
    }

    private function loadVenues(): void
    {
        $this->venues = Venue::select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    public function mount(Event $event, ?User $user): void
    {
        $this->event = $event;
        $this->event_id = $event->id;
        $this->form->setEvent($event);
        $this->selectedTab = $this->getSelectedTab();
        $this->assignmentForm->due_at = Carbon::today('Europe/Berlin')
            ->format('Y-m-d');
        $this->assignmentForm->status = AssignmentStatus::draft->value;
        $this->assignmentForm->member_id = auth()->user()->member->id;
        $this->timelineForm->member_id = auth()->user()->member->id;
        $this->venuesKey = now()->toDateTimeString();
        $this->loadVenues();
    }

    public function addVisitor(): void
    {
        $this->checkPrivilege(Event::class);
        Flux::modal('add-new-visitor')->show();
    }

    public function updateEventData(): void
    {
        try {
            $this->checkPrivilege(Event::class);
            $this->form->update();
        } catch (\Throwable $e) {
            $this->handleError('Event aktualisieren fehlgeschlagen', $e);
        }
    }

    #[On('image-uploaded')]
    public function storeImage(string $file): void
    {
        try {
            if ($this->form->storeImage($file)) {
                $this->dispatch('flux-toast', ['variant' => 'success']);
            } else {
                Log::error('fehler beim hochladen der Datei', ['file' => $file]);
            }
        } catch (\Throwable $e) {
            $this->handleError('Bild speichern fehlgeschlagen', $e);
        }
    }

    public function deleteImage(): void
    {
        try {
            if ($this->form->deleteImage()) {
                Flux::toast(
                    text: __('event.delete_image.success.content'),
                    heading: __('event.delete_image.success.title'),
                    variant: 'success',
                );
            }
        } catch (\Throwable $e) {
            $this->handleError('Bild löschen fehlgeschlagen', $e);
        }
    }

    public function generateEventReport(): void {}

    public function startNewAssigment(): void
    {
        $this->checkPrivilege(Event::class);
        $this->reset('assignmentForm');
        Flux::modal('assignment-modal')->show();
    }

    public function storeAssignment(): void
    {
        try {
            $this->checkPrivilege(Event::class);

            if ($this->assignmentForm->id) {
                $this->assignmentForm->update();
            } else {
                $this->assignmentForm->event_id = $this->event_id;
                $this->assignmentForm->user_id = auth()->user()->id;
                $this->assignmentForm->create();
                Flux::toast(
                    text: __('assignment.storing_success.msg'),
                    heading: __('assignment.storing_success.header'),
                    variant: 'success',
                );
            }
        } catch (\Throwable $e) {
            $this->handleError('Aufgabe speichern fehlgeschlagen', $e);
        }
    }

    public function editAssignment(int $assignmentId): void
    {
        $this->selectedRow = $assignmentId;
        $this->checkPrivilege(Event::class);
        $this->assignmentForm->set(EventAssignment::findOrFail($assignmentId));
        Flux::modal('assignment-modal')->show();
    }

    public function deleteAssignment(int $assignmentId): void
    {
        try {
            $this->checkPrivilege(Event::class);
            if (EventAssignment::find($assignmentId)->delete()) {
                Flux::toast(
                    text: __('assignment.deletion_success.msg'),
                    heading: __('assignment.deletion_success.header'),
                    variant: 'success',
                );
            }
        } catch (\Throwable $e) {
            $this->handleError('Aufgabe löschen fehlgeschlagen', $e);
        }
    }

    public function startNewTimelineItem(): void
    {
        $this->checkPrivilege(Event::class);
        Flux::modal('timeline-modal')->show();
    }

    public function storeTimeline(): void
    {
        try {
            $this->checkPrivilege(Event::class);

            if ($this->timelineForm->id) {
                $this->timelineForm->update();
            } else {
                $this->timelineForm->event_id = $this->event_id;
                $this->timelineForm->user_id = auth()->user()->id;
                $this->timelineForm->create();
                $this->timelineForm->start = $this->timelineForm->end;
                $this->timelineForm->end = '';
            }

            Flux::toast(
                text: __('timeline.storing_success.msg'),
                heading: __('timeline.deletion_success.header'),
                variant: 'success',
            );
        } catch (\Throwable $e) {
            $this->handleError('Timeline speichern fehlgeschlagen', $e);
        }
    }

    public function editTimeline(int $timelineId): void
    {
        $this->checkPrivilege(Event::class);
        $this->timelineForm->set(EventTimeline::findOrFail($timelineId));
        Flux::modal('timeline-modal')->show();
    }

    public function deleteTimeline(int $timelineId): void
    {
        try {
            $this->checkPrivilege(Event::class);
            if (EventTimeline::find($timelineId)->delete()) {
                Flux::toast(
                    text: __('timeline.deletion_success.msg'),
                    heading: __('timeline.deletion_success.header'),
                    variant: 'success',
                );
            }
        } catch (\Throwable $e) {
            $this->handleError('Timeline löschen fehlgeschlagen', $e);
        }
    }

    public function sendAssignmentNotification(int $assignmentId): void {}

    public function publishEvent(): void
    {
        try {
            $this->checkPrivilege(Event::class);
            $this->form->status = EventStatus::PUBLISHED->value;
            $this->form->update();

            Flux::toast(
                text: __('event.section.published.toast_success.msg'),
                heading: __('timeline.deletion_success.header'),
                variant: 'success',
            );
        } catch (\Throwable $e) {
            $this->handleError('Event veröffentlichen fehlgeschlagen', $e);
        }
    }

    public function resetPublication(): void
    {
        try {
            $this->checkPrivilege(Event::class);
            $this->form->status = EventStatus::RETRACTED->value;
            $this->form->update();

            Flux::toast(
                text: __('post.form.toasts.msg.post_retracted'),
                heading: __('post.form.toasts.heading.success'),
                duration: 3000,
                variant: 'warning',
            );
        } catch (\Throwable $e) {
            $this->handleError('Veröffentlichung zurücknehmen fehlgeschlagen', $e);
        }
    }

    public function sendPublicationNotification(): void
    {
        $this->checkPrivilege(Event::class);

        if (is_null($this->event->notification_sent_at)) {
            $this->sendNotificationEmail();
        } else {
            Flux::modal('confirm-resending-publication-notification')->show();
        }
    }

    /**
     * @throws \Exception
     */
    public function sendPublicationLetter(): mixed
    {
        $emailLessMembers = Member::whereNull('email');

        if ($emailLessMembers->count() > 0) {
            $this->checkPrivilege(Event::class);

            $pdfString = PdfGeneratorService::generatePdf('event-invitation-letter', $this->event);
            $filename = 'einladungsschreiben-'.$this->event->name.'-'.now()->format('Ymd').'.pdf';

            return response()->streamDownload(function () use ($pdfString): void {
                echo $pdfString;
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        Flux::toast(
            text: __('event.send_publication.no_email_members'),
            heading: __('event.send_publication.abort_heading'),
            variant: 'warning',
        );

        return null;
    }

    public function reSendPublicationNotification(): void
    {
        $this->sendNotificationEmail();
        Flux::modal('confirm-resending-publication-notification')->close();
    }

    public function makeWebText(): void
    {
        $this->checkPrivilege(Event::class);
        $this->form->makeWebText();
    }

    public function openVenueCreate(): void
    {
        $this->authorize('update', Event::class);
        $this->dispatch('open-venue-create');
    }

    public function openVenueEdit(): void
    {
        $this->authorize('update', Event::class);
        $venueId = (int) $this->form->venue_id;

        if ($venueId <= 0) {
            return;
        }

        $this->dispatch('open-venue-edit', venueId: $venueId);
    }

    public function render(): View
    {
        return view('livewire.event.show.page')
            ->title(__('event.show.title').' '.$this->event->name);
    }

    protected function sendNotificationEmail(): void
    {
        $this->checkPrivilege(Event::class);
        $mailingService = app(MailingService::class);

        $mailingService->sendNotificationsToSubscribers(
            'events',
            $this->event,
            __('event.notification_mail.subject'),
            'emails.new_event_notification',
            []
        );
        $this->form->notification_sent_at = Carbon::now();
        $this->form->update();

        Flux::toast(
            text: __('post.form.toasts.notification_sent_success'),
            heading: __('post.form.toasts.heading.success'),
            duration: 8000,
            variant: 'success',
        );
    }
}
