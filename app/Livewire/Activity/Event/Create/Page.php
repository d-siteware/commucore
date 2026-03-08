<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Event\Create;

use App\Livewire\Forms\Event\EventForm;
use App\Models\Event\Event;
use App\Models\Venue;
use App\Rules\UniqueJsonSlug;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Page extends Component
{
    public EventForm $form;

    public $step = 1;

    public bool $step1Completed = false;

    public bool $step2Completed = false;

    public $totalSteps = 3;

    #[Computed]
    public function venues(): Collection
    {
        return Venue::select('id', 'name')
            ->get();
    }

    public function nextStep(): void
    {
        $this->validateStep();
        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function validateStep(): void
    {
        // Validate only the fields for the current step
        if ($this->step == 1) {
            $this->validate([
                'form.name' => 'required|string|max:255',
                'form.event_date' => 'required|date|after:today',
                'form.start_time' => 'required_with:form.event_date',
                'form.end_time' => 'required_with:form.event_date',
            ]);
            $this->step1Completed = true;
        } elseif ($this->step == 2) {
            $this->validate([
                'form.title.*' => ['required', new UniqueJsonSlug('events', 'title')],
            ]);
            $this->step2Completed = true;

        } elseif ($this->step == 3) {
            $this->validate([
                'form.slug.*' => ['required', new UniqueJsonSlug('events', 'title')],
            ]);
        }
    }

    public function setStep(int $step): void
    {
        if ($step < $this->step) {
            $this->step = $step;
        }
    }

    public function makeWebText(): void
    {
        $this->form->makeWebText();
    }

    public function createEventData(): void
    {
        $this->authorize('create', Event::class);
        $newEvent = $this->form->create();
        Flux::toast(
            text: __('event.store.success.content'),
            heading: __('event.store.success.title'),
            variant: 'success',
        );
        //        $this->dispatch('navigate-to', route('backend.events.index'));
        $this->redirect(route('backend.events.show', $newEvent));
    }

    public function render(): View
    {
        return view('livewire.event.create.page');
    }

    public function addDemoData(): void
    {
        if (! app()->isProduction()) {
            $this->authorize('create', Event::class);

            $this->form->demoData();

        }
    }
}
