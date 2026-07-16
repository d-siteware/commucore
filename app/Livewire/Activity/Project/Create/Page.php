<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Project\Create;

use App\Enums\ProjectStatus;
use App\Livewire\Forms\Activity\ProjectForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Project\Project;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Component;

final class Page extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    public ProjectForm $form;

    public function mount(): void
    {
        $this->form->status = ProjectStatus::Planned->value;
    }

    public function createProject(): void
    {
        try {
            $this->checkPrivilege(Project::class);

            $project = $this->form->store();

            Flux::toast(
                text: __('projects.create.success.content'),
                heading: __('projects.create.success.title'),
                variant: 'success',
            );

            $this->redirect(route('project.show', $project), navigate: true);
        } catch (\Throwable $e) {
            $this->handleError('Projekt erstellen fehlgeschlagen', $e);
        }
    }

    public function render(): View
    {
        return view('livewire.activity.project.create.page')
            ->title(__('projects.create.page.title'));
    }
}
