<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MemberType;
use App\Enums\OnboardingPriority;
use App\Models\Accounting\Account;
use App\Models\Accounting\FiscalYear;
use App\Models\Membership\Member;
use App\Models\Membership\Role;
use App\Models\Event\Event;
use App\Models\Venue;
use App\Services\Accounting\DatevSettingsService;
use Illuminate\Support\Facades\Cache;

class OnboardingStatusService
{
    protected const CACHE_KEY = 'onboarding.status';

    protected const TTL_MONTHS = 2;

    protected const MIN_ACTIVE_MEMBERS = 4; // Founder + 3 weitere

    /**
     * Liefert den vollständigen Status, gecached pro Tenant-Instanz.
     * Der Redis-Prefix sorgt für Mandantentrennung (siehe config/database.php).
     */
    public function getStatus(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            now()->addMonths(self::TTL_MONTHS),
            fn (): array => $this->resolve()
        );
    }

    protected function resolve(): array
    {
        return [
            // --- Essenziell (rot) ---
            'has_account'         => Account::query()->exists(),
            'has_organization_data' => $this->hasOrganizationData(),
            'has_statute'          => $this->hasStatute(),
            'has_board_member'     => $this->hasBoardMember(),
            'has_min_members'      => $this->hasMinActiveMembers(),
            'has_all_roles_assigned' => $this->hasAllAccountingRolesAssigned(),
            'has_datev_berater_nr' => $this->hasDatevBeraterNr(),
            'has_datev_mandant_nr' => $this->hasDatevMandantNr(),

            // --- Wichtig (amber) ---
            'has_fiscal_year' => FiscalYear::query()->exists(),
            'has_logo'        => filled(setting('branding.logo')),
            'has_about_us'    => $this->hasAboutUs(),

            // --- Aktivitäten (nur sichtbar wenn alle roten Punkte grün) ---
            'has_event' => Event::query()->exists(),
            'has_venue' => Venue::query()->exists(),
            // Blog/Projects ergänzen, sobald die jeweiligen Models feststehen
        ];
    }

    public function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array{priority: OnboardingPriority|null, missing: array<string>}
     */
    public function badgeStatus(): array
    {
        $status = $this->getStatus();
        $items = $this->configItems();

        $missingCritical = $this->missingForPriority($status, $items, OnboardingPriority::Critical);
        if ($missingCritical !== []) {
            return ['priority' => OnboardingPriority::Critical, 'missing' => $missingCritical];
        }

        $missingImportant = $this->missingForPriority($status, $items, OnboardingPriority::Important);
        if ($missingImportant !== []) {
            return ['priority' => OnboardingPriority::Important, 'missing' => $missingImportant];
        }

        return ['priority' => null, 'missing' => []];
    }

    /**
     * Alle Checklist-Items aus der Config, flach (ohne Sektionsstruktur).
     * Aktivitäten-Items werden hier bewusst mitgeliefert — für das Badge
     * spielt die Gating-Logik der UI keine Rolle, nur der reine Status zählt.
     *
     * @return array<int, array{status_key: string, label: string, priority: OnboardingPriority, route: ?string, tutorial: ?string}>
     */
    protected function configItems(): array
    {
        return collect(config('onboarding.sections', []))
            ->flatMap(fn (array $section) => $section['items'])
            ->all();
    }

    /**
     * @param  array<int, array{status_key: string, label: string, priority: OnboardingPriority}>  $items
     * @return array<string>
     */
    protected function missingForPriority(array $status, array $items, OnboardingPriority $priority): array
    {
        return collect($items)
            ->where('priority', $priority)
            ->reject(fn (array $item) => $status[$item['status_key']] ?? false)
            ->pluck('label')
            ->values()
            ->all();
    }

    /**
     * True, sobald keine kritischen Punkte mehr offen sind.
     * Steuert, ob die Aktivitäten-Sektion in der Checkliste erscheint.
     */
    public function isFullySetUp(): bool
    {
        return $this->badgeStatus()['priority'] !== OnboardingPriority::Critical;
    }

    /**
     * Pflichtfelder laut Vereinsrecht: Name, Registernummer, Anschrift
     * (Straße, PLZ, Ort als separate Settings-Keys).
     */
    protected function hasOrganizationData(): bool
    {
        return filled(setting('organization.name'))
            && filled(setting('organization.register_id'))
            && filled(setting('organization.court'))
            && filled(setting('organization.address'))
            && filled(setting('organization.zip'))
            && filled(setting('organization.city'));
    }

    /**
     * Satzung liegt als mehrsprachiges Array unter organization.statute.
     * Mindestens die deutsche (Default-)Locale muss befüllt sein.
     */
    protected function hasStatute(): bool
    {
        $statute = setting('organization.statute', []);

        if (! is_array($statute)) {
            return filled($statute);
        }

        return filled($statute['de'] ?? null);
    }

    protected function hasBoardMember(): bool
    {
        return Member::query()
            ->where('type', MemberType::MD->value)
            ->whereNull('left_at')
            ->exists();
    }

    /**
     * Founder + mind. 3 weitere aktive Mitglieder (alles außer Antragsteller,
     * nicht ausgetreten).
     */
    protected function hasMinActiveMembers(): bool
    {
        return Member::query()
                ->where('type', '!=', MemberType::AP->value)
                ->whereNull('left_at')
                ->count() >= self::MIN_ACTIVE_MEMBERS;
    }

    /**
     * Alle drei Buchhaltungs-/Vertretungsrollen müssen mindestens einmal
     * an ein aktives Mitglied vergeben sein.
     */
    protected function hasAllAccountingRolesAssigned(): bool
    {
        $hasManage = Role::query()
            ->where('can_manage_accounting', true)
            ->whereHas('currentMembers')
            ->exists();

        $hasRepresent = Role::query()
            ->where('can_represent_organization', true)
            ->whereHas('currentMembers')
            ->exists();

        $hasAudit = Role::query()
            ->where('can_audit_accounting', true)
            ->whereHas('currentMembers')
            ->exists();

        return $hasManage && $hasRepresent && $hasAudit;
    }

    /**
     * Über-uns-Text als mehrsprachiges Array unter organization.about_us.
     */
    protected function hasAboutUs(): bool
    {
        $aboutUs = setting('organization.about_us', []);

        if (! is_array($aboutUs)) {
            return filled($aboutUs);
        }

        return filled($aboutUs['de'] ?? null);
    }

    protected function hasDatevBeraterNr(): bool
    {
        return app(DatevSettingsService::class)->beraterNr() !== '0000';
    }

    protected function hasDatevMandantNr(): bool
    {
        return app(DatevSettingsService::class)->mandantNr() !== '00000';
    }
}
