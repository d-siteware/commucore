<?php

declare(strict_types=1);

use App\Models\User;
use App\Policies\CancelTransactionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CancelTransactionPolicy – viewAny()', function (): void {
    it('always denies', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new CancelTransactionPolicy;

        expect($policy->viewAny())->toBeFalse();
    });
});

describe('CancelTransactionPolicy – view()', function (): void {
    it('allows admin users', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new CancelTransactionPolicy;

        expect($policy->view($user))->toBeTrue();
    });

    it('denies non-admin users', function (): void {
        $user = User::factory()->create(['is_admin' => false]);
        $policy = new CancelTransactionPolicy;

        expect($policy->view($user))->toBeFalse();
    });
});

describe('CancelTransactionPolicy – create()', function (): void {
    it('allows admin users', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new CancelTransactionPolicy;

        expect($policy->create($user))->toBeTrue();
    });

    it('denies non-admin users', function (): void {
        $user = User::factory()->create(['is_admin' => false]);
        $policy = new CancelTransactionPolicy;

        expect($policy->create($user))->toBeFalse();
    });
});

describe('CancelTransactionPolicy – update()', function (): void {
    it('allows admin users', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new CancelTransactionPolicy;

        expect($policy->update($user))->toBeTrue();
    });
});

describe('CancelTransactionPolicy – delete()', function (): void {
    it('allows admin users', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new CancelTransactionPolicy;

        expect($policy->delete($user))->toBeTrue();
    });
});

describe('CancelTransactionPolicy – restore()', function (): void {
    it('always denies', function (): void {
        $policy = new CancelTransactionPolicy;

        expect($policy->restore())->toBeFalse();
    });
});

describe('CancelTransactionPolicy – forceDelete()', function (): void {
    it('always denies', function (): void {
        $policy = new CancelTransactionPolicy;

        expect($policy->forceDelete())->toBeFalse();
    });
});
