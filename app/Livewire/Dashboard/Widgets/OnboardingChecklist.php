<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Widgets;

use App\Services\OnboardingStatusService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OnboardingChecklist extends Component
{
    public bool $collapsed = false;

    #[Computed]
    public function status(): array
    {
        return app(OnboardingStatusService::class)->getStatus();
    }

    #[Computed]
    public function isFullySetUp(): bool
    {
        return app(OnboardingStatusService::class)->isFullySetUp();
    }

    /**
     * Alle Sektionen aus der Config, gefiltert um die Aktivitäten-Sektion
     * auszuschließen solange noch kritische Punkte offen sind.
     *
     * @return array<string, array{label: string, activity?: bool, items: array}>
     */
    #[Computed]
    public function visibleSections(): array
    {
        return collect(config('onboarding.sections', []))
            ->reject(fn (array $section) => ($section['activity'] ?? false) && ! $this->isFullySetUp())
            ->all();
    }

    #[Computed]
    public function totalCount(): int
    {
        return collect($this->visibleSections())
            ->flatMap(fn (array $s) => $s['items'])
            ->count();
    }

    #[Computed]
    public function completedCount(): int
    {
        return collect($this->visibleSections())
            ->flatMap(fn (array $s) => $s['items'])
            ->filter(fn (array $item) => $this->status()[$item['status_key']] ?? false)
            ->count();
    }

    public function dismiss(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->onboarding_checklist_dismissed_at = now();
        $user->save();
    }

    public function reopen(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->onboarding_checklist_dismissed_at = null;
        $user->save();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.dashboard.widgets.onboarding-checklist');
    }
}