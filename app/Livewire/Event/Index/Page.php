<?php

declare(strict_types=1);

namespace App\Livewire\Event\Index;

use App\Enums\EventStatus;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\Sortable;
use App\Models\Event\Event;
use App\Services\PdfGeneratorService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use HasPrivileges;
    use Sortable;
    use WithPagination;

    public string $locale;

    public $search = '';

    public string $programmFilter = 'year';

    public $filteredBy = [
        EventStatus::DRAFT->value,
        EventStatus::PUBLISHED->value,
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->locale = session('locale') ?? app()->getLocale();
        $this->sortBy = 'event_date';
        $this->sortDirection = 'desc';
    }

    #[Computed]
    public function events(): LengthAwarePaginator
    {
        return Event::query()
            ->with('venue')
            ->with('subscriptions')
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->tap(fn ($query) => $this->search ? $query->where('title', 'LIKE', '%'.$this->search.'%')
                ->orWhere('name', 'LIKE', '%'.$this->search.'%')
                : $query)
            ->tap(fn ($query) => $this->filteredBy ? $query->whereIn('status', $this->filteredBy) : $query)
            ->paginate(10);
    }

    public function generateEventList()
    {

        $this->checkPrivilege(Event::class);

        $filename = 'veranstaltungen-'.now()->format('Y').'_'.now()->format('Ymd').'.pdf';
        $pdfString = PdfGeneratorService::generatePdf('event-programm-letter', $this->programmFilter, $filename);

        return response()->streamDownload(function () use ($pdfString) {
            echo $pdfString;
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);

    }

    public function render(): View
    {
        return view('livewire.event.index.page')
            ->title(__('event.page.title'));
    }
}
