<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\Venue;

use App\Models\Venue;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    /**
     * ID des Venues, dessen Löschung gerade bestätigt werden soll.
     * null, solange kein Lösch-Dialog offen ist.
     */
    public ?int $pendingDeleteId = null;

    public function mount(): void
    {
        $this->authorize('create', Venue::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

    #[Computed]
    public function venues(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Venue::query()
            ->withCount('events')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($q): void {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('city', 'like', "%{$this->search}%")
                        ->orWhere('address', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
    }

    public function create(): void
    {
        $this->dispatch('open-venue-create');
    }

    public function edit(int $venueId): void
    {
        $this->dispatch('open-venue-edit', venueId: $venueId);
    }

    /**
     * Öffnet den Bestätigungsdialog statt direkt zu löschen.
     */
    public function confirmDelete(int $venueId): void
    {
        $this->pendingDeleteId = $venueId;
        Flux::modal('venue-delete-confirm')->show();
    }

    public function cancelDelete(): void
    {
        $this->pendingDeleteId = null;
        Flux::modal('venue-delete-confirm')->close();
    }

    public function delete(): void
    {
        if ($this->pendingDeleteId === null) {
            return;
        }

        $venue = Venue::findOrFail($this->pendingDeleteId);
        $venue->delete();

        Flux::toast(
            text: __('venue.toast.deleted.text'),
            heading: __('venue.toast.deleted.heading'),
            variant: 'success',
        );

        $this->pendingDeleteId = null;
        Flux::modal('venue-delete-confirm')->close();
    }

    /**
     * Anzahl verknüpfter Events für den aktuell zur Löschung vorgesehenen Venue.
     * Wird im Bestätigungsdialog angezeigt.
     */
    #[Computed]
    public function pendingDeleteEventsCount(): int
    {
        if ($this->pendingDeleteId === null) {
            return 0;
        }

        $venue = Venue::query()
            ->whereKey($this->pendingDeleteId)
            ->withCount('events')
            ->first();

        if ($venue === null) {
            return 0;
        }

        return $venue->events_count;
    }

    #[Computed]
    public function pendingDeleteVenueName(): string
    {
        if ($this->pendingDeleteId === null) {
            return '';
        }

        $venue = Venue::query()->find($this->pendingDeleteId);

        if ($venue === null) {
            return '';
        }

        return $venue->name;
    }

    #[On('venue-created')]
    #[On('venue-updated')]
    public function refreshList(): void
    {
        unset($this->venues);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.tool.venue.index');
    }
}
