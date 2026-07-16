<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Event\Visitor\Create;

use App\Livewire\Forms\Event\EventVisitorForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
use App\Models\Membership\Member;
use Flux\Flux;
use Livewire\Component;

final class Form extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    public EventVisitorForm $form;

    public $members = [];

    public $subscribers = [];

    public function mount(Event $event): void
    {
        $this->form->event_id = $event->id;
        $this->members = Member::select('id', 'name', 'first_name')->get();
        $this->subscribers = EventSubscription::select()->get();

    }

    public function add(): void
    {
        try {
            $this->checkPrivilege(Event::class);

            $this->form->create();
            Flux::toast(__('event.visitor-modal.toast.msg'), __('event.visitor-modal.toast.heading'), variant: 'success');
            $this->dispatch('event-visitor-added');
        } catch (\Throwable $e) {
            $this->handleError('Besucher hinzufügen fehlgeschlagen', $e);
        }
    }

    public function setMember(): void
    {
        $member = Member::findOrFail($this->form->member_id);
        $this->form->member_id = $member->id;
        $this->form->name = $member->fullName();
        $this->form->email = $member->email;
        $this->form->phone = $member->phone;
        $this->form->gender = $member->gender;
        $this->reset('form.event_subscription_id');

    }

    public function setSubscriber(): void
    {
        $subscription = EventSubscription::findOrFail($this->form->event_subscription_id);
        $this->form->event_subscription_id = $subscription->id;
        $this->form->name = $subscription->name;
        $this->form->email = $subscription->email;
        $this->form->phone = $subscription->phone;
        $this->reset('form.member_id');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.event.visitor.create.form');
    }
}
