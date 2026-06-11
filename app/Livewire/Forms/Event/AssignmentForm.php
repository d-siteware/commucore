<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Event;

use App\Actions\Event\CreateAssignment;
use App\Actions\Event\UpdateAssignment;
use App\Enums\AssignmentStatus;
use App\Models\Event\EventAssignment;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Form;
use Throwable;

final class AssignmentForm extends Form
{
    public EventAssignment $assignment;

    public $task;

    public $status;

    public $description;

    public $due_at;

    public $amount;

    public $event_id;

    public $member_id;

    public $user_id;

    public $id;

    public function set(EventAssignment $assignment): void
    {
        $this->task = $assignment->task;
        $this->status = $assignment->status;
        $this->description = $assignment->description;
        $this->due_at = $assignment->due_at;
        $this->amount = $assignment->amount / 100;
        $this->event_id = $assignment->event_id;
        $this->member_id = $assignment->member_id;
        $this->user_id = $assignment->user_id;
        $this->id = $assignment->id;
    }

    public function create(): void
    {
        $this->validate();
        try {
            $this->assignment = CreateAssignment::handle($this);
        } catch (Throwable $exception) {
            Flux::toast(
                text: __('Aufgabe konnte nicht gespeichert werden. \n\r :msg', ['msg' => $exception->getMessage()]),
                heading: __('event.error.heading'),
                variant: 'danger',
            );
        }

    }

    public function update(): void
    {

        $this->validate();
        try {
            $this->assignment = UpdateAssignment::handle($this);
        } catch (Throwable $exception) {
            Flux::toast(
                text: __('Aufgabe konnte nicht aktualisiert werden. \n\r :msg', ['msg' => $exception->getMessage()]),
                heading: __('event.error.heading'),
                variant: 'danger',
            );
        }

    }

    protected function rules(): array
    {
        return [
            'task' => ['required', 'string'],
            'status' => ['required', Rule::enum(AssignmentStatus::class)],
            'due_at' => 'date|nullable',
            'amount' => 'nullable',
            'event_id' => 'required|nullable',
            'member_id' => 'nullable|exists:members,id',
            'description' => 'string|nullable',
            'user_id' => 'required|nullable',
        ];
    }
}
