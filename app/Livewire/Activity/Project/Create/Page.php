<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Project\Create;

use App\Enums\ProjectStatus;
use App\Livewire\Forms\Activity\ProjectForm;
use App\Models\Project\Project;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Component;

final class Page extends Component
{
    public ProjectForm $form;

    public function mount(): void
    {
        $this->form->status = ProjectStatus::Planned->value;
    }

    public function createProject(): void
    {
        $this->authorize('create', Project::class);

        $project = $this->form->store();

        Flux::toast(
            text: __('projects.create.success.content'),
            heading: __('projects.create.success.title'),
            variant: 'success',
        );

        $this->redirect(route('project.show', $project), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.activity.project.create.page')
            ->title(__('projects.create.page.title'));
    }
}
