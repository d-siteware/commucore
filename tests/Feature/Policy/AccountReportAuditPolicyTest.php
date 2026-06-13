<?php

declare(strict_types=1);

use App\Enums\MemberType;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Membership\Member;
use App\Models\Membership\Role;
use App\Models\User;
use App\Policies\AccountReportAuditPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

function createAudit(int $userId): AccountReportAudit
{
    $report = AccountReport::factory()->create();

    return AccountReportAudit::create([
        'user_id' => $userId,
        'account_report_id' => $report->id,
    ]);
}

uses(RefreshDatabase::class);

describe('AccountReportAuditPolicy – viewAny()', function (): void {
    it('always denies', function (): void {
        $policy = new AccountReportAuditPolicy;

        expect($policy->viewAny())->toBeFalse();
    });
});

describe('AccountReportAuditPolicy – view()', function (): void {
    it('always denies', function (): void {
        $policy = new AccountReportAuditPolicy;

        expect($policy->view())->toBeFalse();
    });
});

describe('AccountReportAuditPolicy – create()', function (): void {
    it('allows admin users', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new AccountReportAuditPolicy;

        expect($policy->create($user))->toBeTrue();
    });

    it('denies non-admin users', function (): void {
        $user = User::factory()->create(['is_admin' => false]);
        $policy = new AccountReportAuditPolicy;

        expect($policy->create($user))->toBeFalse();
    });

    it('allows accountant users', function (): void {
        $role = Role::factory()->withAccounting()->create();
        $member = Member::factory()->withUser(['is_admin' => false])->create();
        $member->roles()->attach($role->id, ['designated_at' => now()]);
        /** @var User $user */
        $user = $member->user;
        $policy = new AccountReportAuditPolicy;

        expect($policy->create($user))->toBeTrue();
    });

    it('allows board member users', function (): void {
        $member = Member::factory()->boardMember()->withUser(['is_admin' => false])->create();
        /** @var User $user */
        $user = $member->user;
        $policy = new AccountReportAuditPolicy;

        expect($policy->create($user))->toBeTrue();
    });
});

describe('AccountReportAuditPolicy – update()', function (): void {
    it('allows the owner to update their audit', function (): void {
        $user = User::factory()->create();
        $audit = createAudit($user->id);
        $policy = new AccountReportAuditPolicy;

        expect($policy->update($user, $audit))->toBeTrue();
    });

    it('denies other users from updating', function (): void {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $audit = createAudit($owner->id);
        $policy = new AccountReportAuditPolicy;

        expect($policy->update($other, $audit))->toBeFalse();
    });
});

describe('AccountReportAuditPolicy – delete()', function (): void {
    it('always denies', function (): void {
        $policy = new AccountReportAuditPolicy;

        expect($policy->delete())->toBeFalse();
    });
});

describe('AccountReportAuditPolicy – restore()', function (): void {
    it('always denies', function (): void {
        $policy = new AccountReportAuditPolicy;

        expect($policy->restore())->toBeFalse();
    });
});

describe('AccountReportAuditPolicy – forceDelete()', function (): void {
    it('always denies', function (): void {
        $policy = new AccountReportAuditPolicy;

        expect($policy->forceDelete())->toBeFalse();
    });
});

describe('AccountReportAuditPolicy – audit()', function (): void {
    it('allows the owner to audit their report', function (): void {
        $user = User::factory()->create();
        $audit = createAudit($user->id);
        $policy = new AccountReportAuditPolicy;

        expect($policy->audit($user, $audit))->toBeTrue();
    });

    it('denies other users from auditing', function (): void {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $audit = createAudit($owner->id);
        $policy = new AccountReportAuditPolicy;

        expect($policy->audit($other, $audit))->toBeFalse();
    });
});
