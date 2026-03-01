<?php

use App\Models\Accounting\FiscalYear;
use App\Models\User;
use App\Policies\FiscalYearPolicy;

beforeEach(function () {
    $this->policy = new FiscalYearPolicy;
    $this->fiscalYear = FiscalYear::factory()->create();
});

describe('FiscalYear Policy', function () {

    it('allows admin to create fiscal year', function () {
        $admin = User::factory()->create(['is_admin' => true]);

        expect($this->policy->create($admin))->toBeTrue();
    });

    it('allows accountant to create fiscal year', function () {
        $accountant = User::factory()->withAccountingRole()->create();

        expect($this->policy->create($accountant))->toBeTrue();
    });

    it('denies regular user to create fiscal year', function () {
        $user = User::factory()->create(['is_admin' => false]);

        expect($this->policy->create($user))->toBeFalse();
    });

    it('allows admin to close fiscal year', function () {
        $admin = User::factory()->create(['is_admin' => true]);

        expect($this->policy->close($admin, $this->fiscalYear))->toBeTrue();
    });

    it('allows accountant to close fiscal year', function () {
        $accountant = User::factory()->withAccountingRole()->create();

        expect($this->policy->close($accountant, $this->fiscalYear))->toBeTrue();
    });

    it('only allows admin to reopen fiscal year', function () {
        $admin = User::factory()->admin()->create();
        $accountant = User::factory()->withAccountingRole()->create();

        dump($accountant->member->roles);

        expect($this->policy->reopen($admin))->toBeTrue()
            ->and($this->policy->reopen($accountant))->toBeFalse();
    });

    it('allows board member to view fiscal years', function () {
        $boardMember = User::factory()->withBoardRole()->create();

        expect($this->policy->viewAny($boardMember))->toBeTrue();
    });

    it('allows only Admin to delete fiscal years', function () {
        $boardMember = User::factory()->admin()->create();

        expect($this->policy->delete($boardMember))->toBeTrue();

        $admin = User::factory()->create();
        expect($this->policy->delete($admin))->toBeFalse();
    });
});
