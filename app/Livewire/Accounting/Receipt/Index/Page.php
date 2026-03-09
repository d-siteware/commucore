<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Receipt\Index;

use App\Models\Document;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use WithPagination;

    #[Computed]
    public function receipts(): LengthAwarePaginator
    {
        return Document::query()
            ->where('documentable_type', \App\Models\Accounting\Transaction::class)
            ->with(['uploadedBy', 'documentable'])
            ->latest()
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.accounting.receipt.index.page');
    }
}
