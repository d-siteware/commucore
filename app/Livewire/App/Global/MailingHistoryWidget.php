<?php

declare(strict_types=1);

namespace App\Livewire\App\Global;

use App\Models\MailingHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Dashboard widget showing all previously sent mass-mailings.
 *
 * Usage (e.g. in a dashboard blade):
 *   <livewire:app.global.mailing-history-widget />
 */
final class MailingHistoryWidget extends Component
{
    use WithPagination;

    /** Currently expanded detail entry (id) */
    public ?int $selected = null;

    /** Active locale tab in the detail view */
    public string $detailLocale = 'de';

    #[Computed]
    public function mailings(): LengthAwarePaginator
    {
        return MailingHistory::query()
            ->with('sender')
            ->latest()
            ->paginate(8);
    }

    public function selectMailing(int $id): void
    {
        // Toggle: clicking the same row again collapses it
        $this->selected = $this->selected === $id ? null : $id;
        $this->detailLocale = 'de';
    }

    public function setDetailLocale(string $locale): void
    {
        $this->detailLocale = $locale;
    }

    #[Computed]
    public function selectedMailing(): ?MailingHistory
    {
        if ($this->selected === null) {
            return null;
        }

        return MailingHistory::with('sender')->find($this->selected);
    }

    public function render(): View
    {
        return view('livewire.app.global.mailing-history-widget');
    }
}
