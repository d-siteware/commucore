<?php

declare(strict_types=1);

namespace App\Actions\Event;

use App\Livewire\Forms\Event\AssignmentForm;
use App\Models\Accounting\Account;
use App\Models\Event\EventAssignment;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UpdateAssignment
{
    /**
     * @throws Throwable
     */
    public static function handle(AssignmentForm $form): EventAssignment
    {
        return DB::transaction(function () use ($form) {
            $eventAssignment = EventAssignment::findOrFail($form->id);
            if ($eventAssignment->update([
                'task' => $form->task,
                'status' => $form->status,
                'description' => $form->description,
                'due_at' => $form->due_at,
                'amount' => Account::makeCentInteger($form->amount),
                'event_id' => $form->event_id,
                'member_id' => $form->member_id,
                'user_id' => $form->user_id,
            ])) {
                return $eventAssignment;
            } else {
                return null;
            }
        });
    }
}
