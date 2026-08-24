<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use App\Models\User;

test('create-member legt keinen fabrizierten gdpr_consent_at an', function (): void {
    $this->artisan('commucore:create-member', [
        '--email' => 'neu@example.com',
        '--first-name' => 'Patrick',
        '--last-name' => 'Flatten',
        '--type' => 'board',
        '--fee' => 'full',
    ])->assertSuccessful();

    $member = Member::where('email', 'neu@example.com')->firstOrFail();

    expect($member->gdpr_consent_at)->toBeNull();
    expect($member->name)->toBe('Flatten');
    expect($member->first_name)->toBe('Patrick');
    expect($member->type->value)->toBe('board');
    expect($member->fee_type->value)->toBe('full');
});

test('create-member schlägt ohne --last-name mit sauberem Fehler fehl', function (): void {
    $this->artisan('commucore:create-member', [
        '--email' => 'x@example.com',
    ])->assertFailed();

    expect(Member::count())->toBe(0);
});

test('create-member ist idempotent auch ohne existierenden User', function (): void {
    $params = [
        '--email' => 'neu@example.com',
        '--first-name' => 'Patrick',
        '--last-name' => 'Flatten',
    ];

    $this->artisan('commucore:create-member', $params)->assertSuccessful();
    $this->artisan('commucore:create-member', $params)->assertSuccessful();

    expect(Member::where('email', 'neu@example.com')->count())->toBe(1);
});

test('create-member verknüpft einen existierenden User', function (): void {
    $user = User::factory()->create(['email' => 'vorhanden@example.com']);

    $this->artisan('commucore:create-member', [
        '--email' => 'vorhanden@example.com',
        '--last-name' => 'Muster',
    ])->assertSuccessful();

    expect(Member::where('email', 'vorhanden@example.com')->firstOrFail()->user_id)->toBe($user->id);
});