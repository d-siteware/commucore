<?php

declare(strict_types=1);

namespace App\Livewire\App\Global\Venue;

use App\Livewire\Forms\Global\VenueForm;
use Flux\Flux;
use Livewire\Component;

final class Form extends Component
{
    public VenueForm $form;

    public bool $isEditing = false;

    #[\Livewire\Attributes\On('open-venue-create')]
    public function openCreate(): void
    {
        $this->form->resetForm();
        $this->isEditing = false;
        Flux::modal('venue-modal')->show();
    }

    #[\Livewire\Attributes\On('open-venue-edit')]
    public function openEdit(int $venueId): void
    {
        $venue = \App\Models\Venue::findOrFail($venueId);
        $this->form->setVenue($venue);
        $this->isEditing = true;
        Flux::modal('venue-modal')->show();
    }

    public function save(): void
    {
        if ($this->isEditing) {
            $success = $this->form->update();

            if ($success) {
                $this->dispatch('venue-updated', venueId: $this->form->venue?->id);
                Flux::toast(
                    text: __('venue.toast.updated.text'),
                    heading: __('venue.toast.updated.heading'),
                    variant: 'success',
                );
                Flux::modal('venue-modal')->close();
            }

            return;
        }

        $id = $this->form->store();

        if ($id > 0) {
            $this->dispatch('venue-created', venueId: $id);
            Flux::toast(
                text: __('venue.toast.created.text'),
                heading: __('venue.toast.created.heading'),
                variant: 'success',
            );
            Flux::modal('venue-modal')->close();
        }
    }

    public function saveOnly(): void
    {
        if ($this->isEditing) {
            $success = $this->form->update();

            if ($success) {
                Flux::toast(
                    text: __('venue.toast.updated.text'),
                    heading: __('venue.toast.updated.heading'),
                    variant: 'success',
                );
                Flux::modal('venue-modal')->close();
            }

            return;
        }

        $id = $this->form->store();

        if ($id > 0) {
            Flux::toast(
                text: __('venue.toast.created.text'),
                heading: __('venue.toast.created.heading'),
                variant: 'success',
            );
            Flux::modal('venue-modal')->close();
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.global.venue.form');
    }
}