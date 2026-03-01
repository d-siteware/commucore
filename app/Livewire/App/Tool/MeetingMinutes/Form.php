<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\MeetingMinutes;

use App\Livewire\Forms\Minutes\MeetingMinuteForm;
use App\Models\MeetingMinute;
use App\Models\MeetingTopic;
use App\Models\Membership\Member;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class Form extends Component
{
    public MeetingMinuteForm $minuteForm;

    public MeetingMinute $meetingMinute;

    public string $newAttendeeName = '';

    public string $newAttendeeEmail = '';

    public ?int $newAttendeeMemberId = 0;

    public Collection $members;

    public array $attendeesList = [];

    public array $topicsList = [];

    public array $actionItemsList = [];

    public string $newTopicContent = '';

    public string $newActionItemDescription = '';

    public ?int $newActionItemAssigneeId = null;

    public ?string $newActionItemDueDate = null;

    public ?int $currentTopicIndex = null;

    public function mount(?MeetingMinute $meetingMinute): void
    {
        if ($meetingMinute->title) {
            $this->minuteForm->loadMeeting($meetingMinute);
        } else {
            $this->attendeesList = [];
            $this->topicsList = [];
            $this->actionItemsList = [];
            $this->minuteForm->init();
            $this->minuteForm->meeting_date = Carbon::today('Europe/Berlin')
                ->format('Y-m-d');
        }

        $this->members = Member::query()
            ->select(['id', 'name', 'first_name', 'email'])
            ->whereNotNull('entered_at')
            ->get();
    }

    public function updatedNewAttendeeMemberId(int $value): void
    {
        $this->newAttendeeName = '';
        $this->newAttendeeEmail = '';

        if ($value !== 0) {
            $member = $this->members->firstWhere('id', $value);
            if ($member) {
                $this->newAttendeeName = trim("{$member->first_name} {$member->name}") ?: '';
                $this->newAttendeeEmail = $member->email ?? '';
            }
        }
    }

    public function addBoardMembers(): void
    {
        foreach (Member::getBoardMembers() as $member) {

            if (!$this->existsInAttendeeList($member->fullName(), $member->email, $member->id)) {

                $this->attendeesList[] = [
                    'name' => $member->fullName(),
                    'email' => $member->email,
                    'member_id' => $member->id,
                ];
            }
        }

        Flux::modal('add-attendee')
            ->close();
    }

    public function addAttendee(): void
    {
        $this->validate([
            'newAttendeeName' => 'required|string',
            'newAttendeeEmail' => 'nullable|email',
        ]);

        if (!$this->existsInAttendeeList($this->newAttendeeName, $this->newAttendeeEmail, $this->newAttendeeMemberId)) {
            $this->attendeesList[] = [
                'name' => $this->newAttendeeName,
                'email' => $this->newAttendeeEmail,
                'member_id' => $this->newAttendeeMemberId !== 0 ? $this->newAttendeeMemberId : null,
            ];

            $this->newAttendeeName = '';
            $this->newAttendeeEmail = '';
            $this->newAttendeeMemberId = 0;
            Flux::modal('add-attendee')
                ->close();
        } else{
            $this->addError('newAttendeeName', __('minutes.create.validation_error.attendees.duplicate'));
        }


    }

    private function existsInAttendeeList(string $newAttendeeName, string $newAttendeeEmail, int $newAttendeeMemberId = 0): bool
    {
        // Check for duplicates by member_id (if set) or name + email
        return collect($this->attendeesList)->contains(function ($attendee) use ($newAttendeeEmail, $newAttendeeName, $newAttendeeMemberId) {
            $email = $newAttendeeEmail ?: $attendee['email'];
            $newAttendeeMemberId = $newAttendeeMemberId ?: (int) $attendee['member_id'];
            if ($newAttendeeMemberId !== 0) {
                return (int) $attendee['member_id'] === $newAttendeeMemberId;
            }

            return $attendee['name'] === $newAttendeeName && $attendee['email'] === $email;
        });

    }

    public function removeAttendee(int $index): void
    {
        unset($this->attendeesList[$index]);
        $this->attendeesList = array_values($this->attendeesList);
    }

    public function addTopic(): void
    {
        $this->topicsList[] = [
            'content' => $this->newTopicContent ?: null,
            'temporary_id' => uniqid(),
        ];

        $this->newTopicContent = '';
        $this->dispatch('topic-added');
    }

    public function updateTopic(int $index, string $content): void
    {
        if (isset($this->topicsList[$index])) {
            $this->topicsList[$index]['content'] = $content ?: null;
        }
    }

    public function openActionItemModal(int $topicIndex): void
    {
        if (isset($this->topicsList[$topicIndex])) {
            $this->currentTopicIndex = $topicIndex;
            $this->newActionItemDescription = '';
            $this->newActionItemAssigneeId = null;
            $this->newActionItemDueDate = null;
            $this->dispatch('open-modal', 'add-action-item');
        }
    }

    public function addActionItemFromModal(): void
    {
        if ($this->currentTopicIndex === null || ! isset($this->topicsList[$this->currentTopicIndex])) {
            return;
        }

        $this->validate([
            'newActionItemDescription' => 'required|string|min:3',
            'newActionItemAssigneeId' => 'nullable|exists:members,id',
            'newActionItemDueDate' => 'nullable|date',
        ], [
            'newActionItemDescription.required' => __('minutes.create.validation_error.actionitems.description.required'),
            'newActionItemDescription.min' => __('minutes.create.validation_error.actionitems.description.min'),
        ]);

        $this->actionItemsList[] = [
            'topic_temporary_id' => $this->topicsList[$this->currentTopicIndex]['temporary_id'],
            'description' => $this->newActionItemDescription,
            'assignee_id' => $this->newActionItemAssigneeId,
            'due_date' => $this->newActionItemDueDate,
            'completed' => false,
            'temporary_id' => uniqid(),
        ];

        $this->newActionItemDescription = '';
        $this->newActionItemAssigneeId = null;
        $this->newActionItemDueDate = null;
        $this->currentTopicIndex = null;
        $this->dispatch('close-modal', 'add-action-item');
        $this->dispatch('action-item-added');
    }

    public function removeActionItem(int $index): void
    {
        unset($this->actionItemsList[$index]);
        $this->actionItemsList = array_values($this->actionItemsList);
    }

    public function save(): void
    {
        $this->validate([
            'minuteForm.title' => 'required|string|max:255',
            'minuteForm.meeting_date' => 'required|date',
            'minuteForm.location' => 'nullable|string|max:255',
            'minuteForm.content' => 'nullable|string',
            'attendeesList' => 'required|array|min:1',
            'attendeesList.*.name' => 'required|string',
            'attendeesList.*.email' => 'nullable|email',
            'attendeesList.*.member_id' => 'nullable|exists:members,id',
            'topicsList' => 'required|array|min:1',
            'topicsList.*.content' => 'required|string|min:3',
            'actionItemsList.*.description' => 'required|string',
            'actionItemsList.*.assignee_id' => 'nullable|exists:members,id',
            'actionItemsList.*.due_date' => 'nullable|date',
            'actionItemsList.*.completed' => 'boolean',
        ], [
            'topicsList.*.content.required' => __('minutes.create.validation_error.topics.content.required'),
            'attendeesList.required' => __('minutes.create.validation_error.attendees.required'),
            'attendeesList.min' => __('minutes.create.validation_error.attendees.min'),
            'topicsList.required' => __('minutes.create.validation_error.topics.required'),
            'topicsList.min' => __('minutes.create.validation_error.topics.min'),
            'minuteForm.title.required' => __('minutes.create.validation_error.title.required'),
            'minuteForm.meeting_date.required' => __('minutes.create.validation_error.meeting_date.required'),
        ]);

        $meetingMinute = MeetingMinute::create([
            'title' => $this->minuteForm->title,
            'meeting_date' => $this->minuteForm->meeting_date,
            'location' => $this->minuteForm->location,
        ]);

        foreach ($this->attendeesList as $attendee) {
            $meetingMinute->attendees()
                ->create([
                    'name' => $attendee['name'],
                    'email' => $attendee['email'],
                    'member_id' => $attendee['member_id'],
                ]);
        }

        foreach ($this->topicsList as $topicData) {
            /** @var MeetingTopic $topic */
            $topic = $meetingMinute->topics()
                ->create([
                    'content' => $topicData['content'],
                ]);

            foreach ($this->actionItemsList as $actionItemData) {
                if ($actionItemData['topic_temporary_id'] === $topicData['temporary_id']) {
                    // Hier wird statt $topic->actionItems() nun direkt der ActionItem erstellt
                    // mit Verweis auf sowohl das Meeting als auch das Topic
                    $meetingMinute->actionItems()
                        ->create([
                            'meeting_topic_id' => $topic->id,
                            'description' => $actionItemData['description'],
                            'assignee_id' => $actionItemData['assignee_id'],
                            'due_date' => $actionItemData['due_date'],
                            'completed' => $actionItemData['completed'],
                        ]);
                }
            }
        }

        session()->flash('message', __('minutes.create.success'));
        $this->redirect(route('minutes.index'), navigate: true);
    }

    public function removeTopic(int $index): void
    {
        unset($this->topicsList[$index]);
        $this->topicsList = array_values($this->topicsList);
    }

    public function render(): View
    {
        return view('livewire.app.tool.meeting-minutes.form', [
            'topics' => $this->topicsList,
            'actionItems' => $this->actionItemsList,
        ]);
    }
}
