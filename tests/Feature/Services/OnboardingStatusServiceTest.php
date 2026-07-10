<?php

declare(strict_types=1);

use App\Enums\MemberType;
use App\Enums\OnboardingPriority;
use App\Models\Accounting\Account;
use App\Models\Accounting\FiscalYear;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use App\Models\Membership\MemberRole;
use App\Models\Membership\Role;
use App\Models\Venue;
use App\Services\OnboardingStatusService;
use Illuminate\Support\Facades\Cache;

describe('OnboardingStatusService', function (): void {
    beforeEach(function (): void {
        Cache::flush();
    });

    it('returns cached status on subsequent calls', function (): void {
        $service = app(OnboardingStatusService::class);

        $first = $service->getStatus();
        $second = $service->getStatus();

        expect($first)->toBe($second)
            ->and(Cache::has('onboarding.status'))->toBeTrue();
    });

    it('invalidates cache when a new account is created', function (): void {
        $service = app(OnboardingStatusService::class);
        $service->getStatus();
        expect(Cache::has('onboarding.status'))->toBeTrue();

        Account::factory()->create();

        expect(Cache::has('onboarding.status'))->toBeFalse();
    });

    it('invalidates cache when a member is created', function (): void {
        $service = app(OnboardingStatusService::class);
        $service->getStatus();
        expect(Cache::has('onboarding.status'))->toBeTrue();

        Member::factory()->create();

        expect(Cache::has('onboarding.status'))->toBeFalse();
    });

    it('invalidates cache when a fiscal year is created', function (): void {
        $service = app(OnboardingStatusService::class);
        $service->getStatus();
        expect(Cache::has('onboarding.status'))->toBeTrue();

        FiscalYear::factory()->create();

        expect(Cache::has('onboarding.status'))->toBeFalse();
    });

    it('invalidates cache when an event is created', function (): void {
        $service = app(OnboardingStatusService::class);
        $service->getStatus();
        expect(Cache::has('onboarding.status'))->toBeTrue();

        Event::factory()->create();

        expect(Cache::has('onboarding.status'))->toBeFalse();
    });

    it('invalidates cache when a venue is created', function (): void {
        $service = app(OnboardingStatusService::class);
        $service->getStatus();
        expect(Cache::has('onboarding.status'))->toBeTrue();

        Venue::factory()->create();

        expect(Cache::has('onboarding.status'))->toBeFalse();
    });

    it('invalidates cache when a member role is assigned via attach', function (): void {
        $service = app(OnboardingStatusService::class);
        $service->getStatus();
        expect(Cache::has('onboarding.status'))->toBeTrue();

        $member = Member::factory()->create();
        $role = Role::factory()->create();
        $member->roles()->attach($role->id, ['designated_at' => now()]);

        expect(Cache::has('onboarding.status'))->toBeFalse();
    });

    it('invalidates cache when a member role is revoked via detach', function (): void {
        $service = app(OnboardingStatusService::class);
        $member = Member::factory()->create();
        $role = Role::factory()->create();
        $member->roles()->attach($role->id, ['designated_at' => now()]);

        $service->getStatus();
        expect(Cache::has('onboarding.status'))->toBeTrue();

        $member->roles()->detach($role->id);

        expect(Cache::has('onboarding.status'))->toBeFalse();
    });

    it('invalidates cache when a member role is updated via save', function (): void {
        $service = app(OnboardingStatusService::class);
        $member = Member::factory()->create();
        $role = Role::factory()->create();
        $memberRole = MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $role->id,
            'designated_at' => now()->toDateString(),
        ]);

        $service->getStatus();
        expect(Cache::has('onboarding.status'))->toBeTrue();

        $memberRole->update(['resigned_at' => now()->toDateString()]);

        expect(Cache::has('onboarding.status'))->toBeFalse();
    });

    it('invalidates cache when a member role is deleted', function (): void {
        $service = app(OnboardingStatusService::class);
        $member = Member::factory()->create();
        $role = Role::factory()->create();
        $memberRole = MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $role->id,
            'designated_at' => now()->toDateString(),
        ]);

        $service->getStatus();
        expect(Cache::has('onboarding.status'))->toBeTrue();

        $memberRole->delete();

        expect(Cache::has('onboarding.status'))->toBeFalse();
    });

    it('detects missing critical steps as red badge', function (): void {
        $service = app(OnboardingStatusService::class);
        $badge = $service->badgeStatus();

        expect($badge['priority'])->toBe(OnboardingPriority::Critical)
            ->and($badge['missing'])->toBeArray();
    });

    it('detects missing soft steps as amber when critical is resolved', function (): void {
        $service = app(OnboardingStatusService::class);

        // Create minimal organization data
        $settingsService = app(\App\Services\SettingsService::class);
        $settingsService->set('organization.name', 'Test');
        $settingsService->set('organization.register_id', 'VR123');
        $settingsService->set('organization.court', 'Berlin');
        $settingsService->set('organization.address', 'Street');
        $settingsService->set('organization.zip', '12345');
        $settingsService->set('organization.city', 'City');
        $settingsService->set('organization.statute', ['de' => 'Statut']);
        $settingsService->set('organization.about_us', ['de' => 'About']);

        // Create board member (MD type)
        Member::factory()->create(['type' => MemberType::MD->value]);

        // Create enough active members
        Member::factory()->count(3)->create(['type' => MemberType::ST->value]);

        // Create accounting roles with current members
        $manageRole = Role::factory()->create([
            'name' => ['de' => 'Manage Role 1', 'hu' => 'Manage Role 1', 'en' => 'Manage Role 1'],
            'can_manage_accounting' => true,
        ]);
        $member = Member::factory()->create();
        $member->roles()->attach($manageRole->id, ['designated_at' => now()]);

        $representRole = Role::factory()->create([
            'name' => ['de' => 'Represent Role 1', 'hu' => 'Represent Role 1', 'en' => 'Represent Role 1'],
            'can_represent_organization' => true,
        ]);
        $member2 = Member::factory()->create();
        $member2->roles()->attach($representRole->id, ['designated_at' => now()]);

        $auditRole = Role::factory()->create([
            'name' => ['de' => 'Audit Role 1', 'hu' => 'Audit Role 1', 'en' => 'Audit Role 1'],
            'can_audit_accounting' => true,
        ]);
        $member3 = Member::factory()->create();
        $member3->roles()->attach($auditRole->id, ['designated_at' => now()]);

        // Create account
        Account::factory()->create();
        app(\App\Services\Accounting\DatevSettingsService::class)->setBeraterNr('12345');
        app(\App\Services\Accounting\DatevSettingsService::class)->setMandantNr('123');

        $badge = $service->badgeStatus();

        expect($badge['priority'])->toBe(OnboardingPriority::Important)
            ->and($badge['missing'])->toBeArray();
    });

    it('returns null priority when all steps are complete', function (): void {
        $service = app(OnboardingStatusService::class);

        // Create all required data
        $settingsService = app(\App\Services\SettingsService::class);
        $settingsService->set('organization.name', 'Test');
        $settingsService->set('organization.register_id', 'VR123');
        $settingsService->set('organization.court', 'Berlin');
        $settingsService->set('organization.address', 'Street');
        $settingsService->set('organization.zip', '12345');
        $settingsService->set('organization.city', 'City');
        $settingsService->set('organization.statute', ['de' => 'Statut']);
        $settingsService->set('organization.about_us', ['de' => 'About']);
        $settingsService->set('branding.logo', 'logo.png');

        // Create board member
        Member::factory()->create(['type' => MemberType::MD->value]);

        // Create enough active members
        Member::factory()->count(3)->create(['type' => MemberType::ST->value]);

        // Create roles with current members
        $manageRole = Role::factory()->create([
            'name' => ['de' => 'Manage Role 1', 'hu' => 'Manage Role 1', 'en' => 'Manage Role 1'],
            'can_manage_accounting' => true,
        ]);
        $member = Member::factory()->create();
        $member->roles()->attach($manageRole->id, ['designated_at' => now()]);

        $representRole = Role::factory()->create([
            'name' => ['de' => 'Represent Role 1', 'hu' => 'Represent Role 1', 'en' => 'Represent Role 1'],
            'can_represent_organization' => true,
        ]);
        $member2 = Member::factory()->create();
        $member2->roles()->attach($representRole->id, ['designated_at' => now()]);

        $auditRole = Role::factory()->create([
            'name' => ['de' => 'Audit Role 1', 'hu' => 'Audit Role 1', 'en' => 'Audit Role 1'],
            'can_audit_accounting' => true,
        ]);
        $member3 = Member::factory()->create();
        $member3->roles()->attach($auditRole->id, ['designated_at' => now()]);

        // Create account
        Account::factory()->create();
        app(\App\Services\Accounting\DatevSettingsService::class)->setBeraterNr('12345');
        app(\App\Services\Accounting\DatevSettingsService::class)->setMandantNr('123');

        // Create fiscal year
        FiscalYear::factory()->create();

        // Create event and venue
        Event::factory()->create();
        Venue::factory()->create();

        $badge = $service->badgeStatus();

        expect($badge['priority'])->toBeNull()
            ->and($badge['missing'])->toBeEmpty();
    });

    it('isFullySetUp returns false when critical steps are missing', function (): void {
        $service = app(OnboardingStatusService::class);

        expect($service->isFullySetUp())->toBeFalse();
    });

    it('isFullySetUp returns true when critical steps are resolved', function (): void {
        $service = app(OnboardingStatusService::class);

        // Resolve all critical steps
        $settingsService = app(\App\Services\SettingsService::class);
        $settingsService->set('organization.name', 'Test');
        $settingsService->set('organization.register_id', 'VR123');
        $settingsService->set('organization.court', 'Berlin');
        $settingsService->set('organization.address', 'Street');
        $settingsService->set('organization.zip', '12345');
        $settingsService->set('organization.city', 'City');
        $settingsService->set('organization.statute', ['de' => 'Statut']);

        Member::factory()->create(['type' => MemberType::MD->value]);
        Member::factory()->count(3)->create(['type' => MemberType::ST->value]);

        $manageRole = Role::factory()->create([
            'name' => ['de' => 'Manage Role 1', 'hu' => 'Manage Role 1', 'en' => 'Manage Role 1'],
            'can_manage_accounting' => true,
        ]);
        $member = Member::factory()->create();
        $member->roles()->attach($manageRole->id, ['designated_at' => now()]);

        $representRole = Role::factory()->create([
            'name' => ['de' => 'Represent Role 1', 'hu' => 'Represent Role 1', 'en' => 'Represent Role 1'],
            'can_represent_organization' => true,
        ]);
        $member2 = Member::factory()->create();
        $member2->roles()->attach($representRole->id, ['designated_at' => now()]);

        $auditRole = Role::factory()->create([
            'name' => ['de' => 'Audit Role 1', 'hu' => 'Audit Role 1', 'en' => 'Audit Role 1'],
            'can_audit_accounting' => true,
        ]);
        $member3 = Member::factory()->create();
        $member3->roles()->attach($auditRole->id, ['designated_at' => now()]);

        Account::factory()->create();
        app(\App\Services\Accounting\DatevSettingsService::class)->setBeraterNr('12345');
        app(\App\Services\Accounting\DatevSettingsService::class)->setMandantNr('123');

        expect($service->isFullySetUp())->toBeTrue();
    });

    it('hasAllAccountingRolesAssigned excludes resigned members', function (): void {
        $service = app(OnboardingStatusService::class);

        // Create organization basics
        $settingsService = app(\App\Services\SettingsService::class);
        $settingsService->set('organization.name', 'Test');
        $settingsService->set('organization.register_id', 'VR123');
        $settingsService->set('organization.court', 'Berlin');
        $settingsService->set('organization.address', 'Street');
        $settingsService->set('organization.zip', '12345');
        $settingsService->set('organization.city', 'City');
        $settingsService->set('organization.statute', ['de' => 'Statut']);

        // Create board member and enough members
        Member::factory()->create(['type' => MemberType::MD->value]);
        Member::factory()->count(3)->create(['type' => MemberType::ST->value]);
        Account::factory()->create();

        // Create roles but assign them to resigned members
        $manageRole = Role::factory()->create([
            'name' => ['de' => 'Manage Resigned', 'hu' => 'Manage Resigned', 'en' => 'Manage Resigned'],
            'can_manage_accounting' => true,
        ]);
        $member = Member::factory()->create();
        $memberRole = MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $manageRole->id,
            'designated_at' => now()->toDateString(),
            'resigned_at' => now()->toDateString(),
        ]);

        $representRole = Role::factory()->create([
            'name' => ['de' => 'Represent Resigned', 'hu' => 'Represent Resigned', 'en' => 'Represent Resigned'],
            'can_represent_organization' => true,
        ]);
        $member2 = Member::factory()->create();
        MemberRole::create([
            'member_id' => $member2->id,
            'role_id' => $representRole->id,
            'designated_at' => now()->toDateString(),
            'resigned_at' => now()->toDateString(),
        ]);

        $auditRole = Role::factory()->create([
            'name' => ['de' => 'Audit Resigned', 'hu' => 'Audit Resigned', 'en' => 'Audit Resigned'],
            'can_audit_accounting' => true,
        ]);
        $member3 = Member::factory()->create();
        MemberRole::create([
            'member_id' => $member3->id,
            'role_id' => $auditRole->id,
            'designated_at' => now()->toDateString(),
            'resigned_at' => now()->toDateString(),
        ]);

        // All three roles have members but they are resigned
        // The status should reflect that has_all_roles_assigned is false
        $status = $service->getStatus();
        expect($status['has_all_roles_assigned'])->toBeFalse();
    });

    it('hasAllAccountingRolesAssigned counts only active (non-resigned) members', function (): void {
        $service = app(OnboardingStatusService::class);

        // Create organization basics
        $settingsService = app(\App\Services\SettingsService::class);
        $settingsService->set('organization.name', 'Test');
        $settingsService->set('organization.register_id', 'VR123');
        $settingsService->set('organization.court', 'Berlin');
        $settingsService->set('organization.address', 'Street');
        $settingsService->set('organization.zip', '12345');
        $settingsService->set('organization.city', 'City');
        $settingsService->set('organization.statute', ['de' => 'Statut']);

        Member::factory()->create(['type' => MemberType::MD->value]);
        Member::factory()->count(3)->create(['type' => MemberType::ST->value]);
        Account::factory()->create();

        // Create roles with active members
        $manageRole = Role::factory()->create([
            'name' => ['de' => 'Manage Role 1', 'hu' => 'Manage Role 1', 'en' => 'Manage Role 1'],
            'can_manage_accounting' => true,
        ]);
        $member = Member::factory()->create();
        $member->roles()->attach($manageRole->id, ['designated_at' => now()]);

        $representRole = Role::factory()->create([
            'name' => ['de' => 'Represent Role 1', 'hu' => 'Represent Role 1', 'en' => 'Represent Role 1'],
            'can_represent_organization' => true,
        ]);
        $member2 = Member::factory()->create();
        $member2->roles()->attach($representRole->id, ['designated_at' => now()]);

        $auditRole = Role::factory()->create([
            'name' => ['de' => 'Audit Role 1', 'hu' => 'Audit Role 1', 'en' => 'Audit Role 1'],
            'can_audit_accounting' => true,
        ]);
        $member3 = Member::factory()->create();
        $member3->roles()->attach($auditRole->id, ['designated_at' => now()]);

        $status = $service->getStatus();
        expect($status['has_all_roles_assigned'])->toBeTrue();

        // Now resign one member
        $memberRole = MemberRole::where('member_id', $member->id)
            ->where('role_id', $manageRole->id)
            ->first();
        $memberRole->update(['resigned_at' => now()->toDateString()]);

        // Cache should be invalidated by the update
        $status = $service->getStatus();
        expect($status['has_all_roles_assigned'])->toBeFalse();
    });
});
