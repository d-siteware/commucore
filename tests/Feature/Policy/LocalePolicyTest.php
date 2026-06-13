<?php

declare(strict_types=1);

use App\Models\User;
use App\Policies\LocalePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('LocalePolicy – viewAny()', function (): void {
    it('allows any authenticated user', function (): void {
        $user = User::factory()->create();
        $policy = new LocalePolicy;

        expect($policy->viewAny($user))->toBeTrue();
    });
});

describe('LocalePolicy – view()', function (): void {
    it('allows any authenticated user', function (): void {
        $user = User::factory()->create();
        $policy = new LocalePolicy;

        expect($policy->view($user))->toBeTrue();
    });
});

describe('LocalePolicy – create()', function (): void {
    it('allows admin users', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new LocalePolicy;

        expect($policy->create($user))->toBeTrue();
    });

    it('denies non-admin users', function (): void {
        $user = User::factory()->create(['is_admin' => false]);
        $policy = new LocalePolicy;

        expect($policy->create($user))->toBeFalse();
    });
});

describe('LocalePolicy – update()', function (): void {
    it('allows admin users', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new LocalePolicy;

        expect($policy->update($user))->toBeTrue();
    });

    it('denies non-admin users', function (): void {
        $user = User::factory()->create(['is_admin' => false]);
        $policy = new LocalePolicy;

        expect($policy->update($user))->toBeFalse();
    });
});

describe('LocalePolicy – delete()', function (): void {
    it('allows admin users', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new LocalePolicy;

        expect($policy->delete($user))->toBeTrue();
    });

    it('denies non-admin users', function (): void {
        $user = User::factory()->create(['is_admin' => false]);
        $policy = new LocalePolicy;

        expect($policy->delete($user))->toBeFalse();
    });
});

describe('LocalePolicy – restore()', function (): void {
    it('allows admin users', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new LocalePolicy;

        expect($policy->restore($user))->toBeTrue();
    });
});

describe('LocalePolicy – forceDelete()', function (): void {
    it('always denies', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new LocalePolicy;

        expect($policy->forceDelete())->toBeFalse();
    });
});
