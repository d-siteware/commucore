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

    /**
     * Essenzielle Punkte (rot) — blockieren die Aktivitäten-Sektion,
     * solange sie nicht erfüllt sind.
     */
    protected function criticalSteps(): array
    {
        return [
            [
                'section' => 'Rechtliche Grundlagen',
                'items' => [
                    ['status_key' => 'has_organization_data', 'label' => 'Vereinsdaten vervollständigen', 'route' => 'settings.organization', 'tutorial' => null],
                    ['status_key' => 'has_statute',           'label' => 'Satzung eintragen',             'route' => 'settings.organization', 'tutorial' => 'https://docs.commu-core.com/tutorials/satzung-hinterlegen'],
                    ['status_key' => 'has_board_member',      'label' => 'Vorstand bestimmen',             'route' => 'members.index',          'tutorial' => null],
                ],
            ],
            [
                'section' => 'Finanzen',
                'items' => [
                    ['status_key' => 'has_account', 'label' => 'Zahlungskonto einrichten', 'route' => 'accounting.accounts', 'tutorial' => null],
                ],
            ],
            [
                'section' => 'Mitglieder & Rollen',
                'items' => [
                    ['status_key' => 'has_min_members',        'label' => 'Weitere Mitglieder anlegen',     'route' => 'members.create', 'tutorial' => 'https://docs.commu-core.com/tutorials/mitglied-erstellen'],
                    ['status_key' => 'has_all_roles_assigned', 'label' => 'Rollen an Mitglieder zuweisen',  'route' => 'members.roles',  'tutorial' => null],
                ],
            ],
        ];
    }

    /**
     * Wichtige, aber nicht blockierende Punkte (amber).
     */
    protected function softSteps(): array
    {
        return [
            [
                'section' => 'Vervollständigung',
                'items' => [
                    ['status_key' => 'has_fiscal_year', 'label' => 'Geschäftsjahr anlegen', 'route' => 'accounting.fiscal-years', 'tutorial' => null],
                    ['status_key' => 'has_logo',        'label' => 'Logo hochladen',         'route' => 'settings.branding',       'tutorial' => null],
                    ['status_key' => 'has_about_us',    'label' => 'Über-uns-Text schreiben','route' => 'settings.organization',   'tutorial' => null],
                ],
            ],
        ];
    }

    /**
     * Erscheint erst, sobald alle kritischen Punkte erledigt sind.
     */
    protected function activitySteps(): array
    {
        return [
            [
                'section' => 'Aktivitäten',
                'items' => [
                    ['status_key' => 'has_venue', 'label' => 'Ersten Veranstaltungsort anlegen', 'route' => 'venues.create', 'tutorial' => null],
                    ['status_key' => 'has_event', 'label' => 'Erste Veranstaltung erstellen',     'route' => 'events.create', 'tutorial' => null],
                ],
            ],
        ];
    }

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

    #[Computed]
    public function visibleSections(): array
    {
        $sections = array_merge($this->criticalSteps(), $this->softSteps());

        if ($this->isFullySetUp) {
            $sections = array_merge($sections, $this->activitySteps());
        }

        return $sections;
    }

    #[Computed]
    public function totalCount(): int
    {
        return collect($this->visibleSections)
            ->flatMap(fn (array $s) => $s['items'])
            ->count();
    }

    #[Computed]
    public function completedCount(): int
    {
        return collect($this->visibleSections)
            ->flatMap(fn (array $s) => $s['items'])
            ->filter(fn (array $item) => $this->status[$item['status_key']] ?? false)
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