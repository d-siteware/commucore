<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use App\Models\Membership\MemberRole;
use App\Models\Membership\Role;
use Carbon\Carbon;

describe('Role model', function (): void {
    it('can be created with factory', function (): void {
        $role = Role::factory()->create();

        expect($role)->toBeInstanceOf(Role::class)
            ->and($role->name)->toBeArray()
            ->and($role->can_manage_accounting)->toBeFalse()
            ->and($role->sort)->toBeInt();
    });

    it('casts name as array (JSON)', function (): void {
        $role = Role::factory()->create([
            'name' => ['de' => 'Vorstand', 'hu' => 'Elnökség', 'en' => 'Board'],
        ]);

        expect($role->name)->toBeArray()
            ->and($role->name['de'])->toBe('Vorstand')
            ->and($role->name['hu'])->toBe('Elnökség')
            ->and($role->name['en'])->toBe('Board');
    });

    it('casts boolean flags', function (): void {
        $role = Role::factory()->create([
            'can_manage_accounting' => true,
            'can_represent_organization' => true,
            'can_audit_accounting' => true,
        ]);

        expect($role->can_manage_accounting)->toBeTrue()
            ->and($role->can_represent_organization)->toBeTrue()
            ->and($role->can_audit_accounting)->toBeTrue();
    });

    it('has accountingRoles scope', function (): void {
        Role::factory()->create(['can_manage_accounting' => false]);
        Role::factory()->create(['can_manage_accounting' => false]);
        $accountingRole = Role::factory()->create(['can_manage_accounting' => true]);

        $accountingRoles = Role::accountingRoles()->get();

        expect($accountingRoles)->toHaveCount(1)
            ->and($accountingRoles->first()->id)->toBe($accountingRole->id);
    });

    it('has auditingRoles scope', function (): void {
        Role::factory()->create(['can_audit_accounting' => false]);
        $auditingRole = Role::factory()->create(['can_audit_accounting' => true]);

        $auditingRoles = Role::auditingRoles()->get();

        expect($auditingRoles)->toHaveCount(1)
            ->and($auditingRoles->first()->id)->toBe($auditingRole->id);
    });

    it('has representingRoles scope', function (): void {
        Role::factory()->create(['can_represent_organization' => false]);
        $representingRole = Role::factory()->create(['can_represent_organization' => true]);

        $representingRoles = Role::representingRoles()->get();

        expect($representingRoles)->toHaveCount(1)
            ->and($representingRoles->first()->id)->toBe($representingRole->id);
    });

    it('has many members through pivot', function (): void {
        $role = Role::factory()->create();
        $member = Member::factory()->create();

        MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $role->id,
            'designated_at' => now()->toDateString(),
        ]);

        expect($role->members)->toHaveCount(1)
            ->and($role->members->first()->id)->toBe($member->id);
    });

    it('currentMembers only includes non-resigned members', function (): void {
        $role = Role::factory()->create();
        $currentMember = Member::factory()->create();
        $resignedMember = Member::factory()->create();

        MemberRole::create([
            'member_id' => $currentMember->id,
            'role_id' => $role->id,
            'designated_at' => now()->toDateString(),
        ]);

        MemberRole::create([
            'member_id' => $resignedMember->id,
            'role_id' => $role->id,
            'designated_at' => '2024-01-01',
            'resigned_at' => '2024-06-01',
        ]);

        expect($role->currentMembers)->toHaveCount(1)
            ->and($role->currentMembers->first()->id)->toBe($currentMember->id);

        expect($role->members)->toHaveCount(2);
    });

    it('has withAccounting factory state', function (): void {
        $role = Role::factory()->withAccounting()->create();

        expect($role->can_manage_accounting)->toBeTrue();
    });
});
