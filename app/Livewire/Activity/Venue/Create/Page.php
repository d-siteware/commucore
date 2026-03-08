<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Venue\Create;

use App\Livewire\Forms\Event\VenueForm;
use Flux\Flux;
use Livewire\Component;

final class Page extends Component
{
    public VenueForm $form;

    public function storeVenue(): void
    {
        $id = $this->form->store();

        if ($id > 0) {
            $this->dispatch('new-venue-created');
            Flux::modal('add-new-venue')->close();
        }

    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.venue.create.page');
    }
}
