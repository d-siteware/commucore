<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Project;

use App\Models\Project\Project;
use Livewire\Form;

final class ProjectForm extends Form
{
    public ?int $id = null;

    public string $title = '';

    public string $description = '';

    public string $status = '';

    public string $start_date = '';

    public string $end_date = '';

    public function setProject(Project $project): void
    {
        $this->id = $project->id;
        $this->title = $project->title;
        $this->description = $project->description ?? '';
        $this->status = $project->status->value;
        $this->start_date = $project->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $project->end_date?->format('Y-m-d') ?? '';
    }

    public function update(Project $project): void
    {
        $validated = $this->validate();

        $project->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?: null,
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?: null,
            'end_date' => $validated['end_date'] ?: null,
        ]);
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
