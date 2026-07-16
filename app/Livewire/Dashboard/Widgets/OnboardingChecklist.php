<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Widgets;

use App\Services\OnboardingStatusService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class OnboardingChecklist extends Component
{
    public bool $collapsed = false;

    #[On('onboarding-update')]
    public function refresh(): void
    {
        $this->recheck();
    }

    public function recheck(): void
    {
        Log::debug('[Onboarding] widget recheck triggered (button or onboarding-update event)');
        app(OnboardingStatusService::class)->invalidate();
    }

    public function isDismissed(): bool
    {
        return Auth::user()->onboarding_checklist_dismissed_at !== null;
    }

    public function toggleCollapsed(): void
    {
        $this->collapsed = ! $this->collapsed;
    }

    public function hideChecklist(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->onboarding_checklist_dismissed_at = now();
        $user->save();

        $this->dispatch('checklist-hidden');
    }

    public function reopen(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->onboarding_checklist_dismissed_at = null;
        $user->save();

        $this->dispatch('checklist-shown');
    }

    public function render(): \Illuminate\View\View
    {
        $statusService = app(OnboardingStatusService::class);
        $status = $statusService->getStatus();

        $isFullySetUp = $statusService->isFullySetUp();

        $visibleSections = collect(config('onboarding.sections', []))
            ->reject(fn (array $section) => ($section['activity'] ?? false) && ! $isFullySetUp)
            ->all();

        $totalCount = collect($visibleSections)
            ->flatMap(fn (array $s) => $s['items'])
            ->count();

        $completedCount = collect($visibleSections)
            ->flatMap(fn (array $s) => $s['items'])
            ->filter(fn (array $item) => $status[$item['status_key']] ?? false)
            ->count();

        return view('livewire.dashboard.widgets.onboarding-checklist', compact(
            'status', 'visibleSections', 'totalCount', 'completedCount', 'isFullySetUp',
        ));
    }
}
