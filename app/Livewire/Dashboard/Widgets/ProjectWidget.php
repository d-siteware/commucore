<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Widgets;

use App\Enums\ProjectStatus;
use App\Models\Project\Project;
use Livewire\Component;

final class ProjectWidget extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        $projects = Project::query()
            ->whereIn('status', [ProjectStatus::Active, ProjectStatus::Planned])
            ->with(['fundings' => fn ($q) => $q->withPivot('allocated_amount')])
            ->orderByRaw("CASE status WHEN 'active' THEN 0 ELSE 1 END")
            ->orderBy('end_date')
            ->get()
            ->map(function (Project $project) {
                $expense          = $project->totalExpense();
                $fundingAllocated = $project->totalFundingAllocated();
                $coverageRate     = $expense > 0
                    ? min(100, round($fundingAllocated / $expense * 100))
                    : 0;

                return [
                    'id'           => $project->id,
                    'title'        => $project->title,
                    'status'       => $project->status,
                    'end_date'     => \App\Helpers\DateHelper::formatDate($project->end_date),
                    'expense'      => $expense,
                    'funding'      => $fundingAllocated,
                    'coverage'     => (int) $coverageRate,
                    'overdue'      => $project->end_date?->isPast() && $project->status === ProjectStatus::Active,
                ];
            });

        $totalActive  = $projects->where('status', ProjectStatus::Active)->count();
        $totalPlanned = $projects->where('status', ProjectStatus::Planned)->count();

        return view('livewire.dashboard.widgets.project-widget', [
            'projects'     => $projects,
            'totalActive'  => $totalActive,
            'totalPlanned' => $totalPlanned,
        ]);
    }
}