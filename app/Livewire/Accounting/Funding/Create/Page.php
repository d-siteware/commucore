<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Funding\Create;

use App\Livewire\Forms\Accounting\FundingForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Funding\Funding;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Component;

final class Page extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    public FundingForm $form;

    public function mount(): void
    {
        $this->form->status = \App\Enums\FundingStatus::Applied->value;
    }

    public function createFunding(): void
    {
        try {
            $this->checkPrivilege(Funding::class);

            $funding = $this->form->store();

            Flux::toast(
                text: __('funding.create.success.content'),
                heading: __('funding.create.success.title'),
                variant: 'success',
            );

            $this->redirect(route('funding.show', $funding), navigate: true);
        } catch (\Throwable $e) {
            $this->handleError('Förderung erstellen fehlgeschlagen', $e);
        }
    }

    public function render(): View
    {
        return view('livewire.accounting.funding.create.page')
            ->title(__('funding.create.page.title'));
    }
}
