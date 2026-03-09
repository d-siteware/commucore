<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Funding\Create;

use App\Livewire\Forms\Accounting\FundingForm;
use App\Models\Funding\Funding;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Component;

final class Page extends Component
{
    public FundingForm $form;

    public function mount(): void
    {
        $this->form->status = \App\Enums\FundingStatus::Applied->value;
    }

    public function createFunding(): void
    {
        $this->authorize('create', Funding::class);

        $funding = $this->form->store();

        Flux::toast(
            text: __('funding.create.success.content'),
            heading: __('funding.create.success.title'),
            variant: 'success',
        );

        $this->redirect(route('funding.show', $funding), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.accounting.funding.create.page')
            ->title(__('funding.create.page.title'));
    }
}
