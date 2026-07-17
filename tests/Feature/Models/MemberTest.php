<?php

declare(strict_types=1);

use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Membership\Role;
use App\Models\User;
use Carbon\Carbon;

test('a member can be created', function (): void {
    $member = Member::factory()->create();

    expect($member)->toBeInstanceOf(Member::class);
});

test('a member has a full name', function (): void {
    $member = Member::factory()->create([
        'name' => 'Doe',
        'first_name' => 'John',
    ]);

    expect($member->fullName())->toBe('Doe, John');
});

test('a member can have a user', function (): void {
    $user = User::factory()->create();
    $member = Member::factory()->create(['user_id' => $user->id]);

    expect($member->user)->toBeInstanceOf(User::class);
});

test('a member can have multiple transactions', function (): void {
    $member = Member::factory()->create();

    // Create transactions via MemberTransaction
    MemberTransaction::factory()->count(3)->create([
        'member_id' => $member->id,
        'transaction_id' => Transaction::factory()->create()->id,
    ]);

    expect($member->transactions)->toHaveCount(3);
});

test('a member can detect a birthday', function (): void {
    $member = Member::factory()->create(['birth_date' => Carbon::today('Europe/Berlin')]);

    expect($member->hasBirthdayToday())->toBeTrue();
});

test('a member fee is calculated correctly', function (): void {
    $feeString = Member::feeForHumans(1500);

    expect($feeString)->toBe('15,00');
});

test('a member invitation status is checked correctly', function (): void {
    $member = Member::factory()->create(['email' => 'test@example.com']);

    expect($member->checkInvitationStatus())->toBe('none');
});

test('leaderboard strings order roles by sort', function (): void {
    $memberFirst = Member::factory()->create(['first_name' => 'Anna', 'name' => 'Aarau']);
    $memberSecond = Member::factory()->create(['first_name' => 'Bert', 'name' => 'Bern']);

    $roleSecond = Role::factory()->create([
        'name' => ['de' => 'Zweite Rolle', 'en' => 'Second role', 'hu' => 'Második'],
        'sort' => 20,
        'can_represent_organization' => true,
    ]);
    $roleFirst = Role::factory()->create([
        'name' => ['de' => 'Erste Rolle', 'en' => 'First role', 'hu' => 'Első'],
        'sort' => 1,
        'can_represent_organization' => true,
    ]);

    $roleSecond->members()->attach($memberSecond->id, ['designated_at' => now()->toDateString()]);
    $roleFirst->members()->attach($memberFirst->id, ['designated_at' => now()->toDateString()]);

    foreach ([Member::organizationRepresentativeString('de'), Member::leaderBoardString('de')] as $string) {
        $posFirst = strpos($string, 'Erste Rolle');
        $posSecond = strpos($string, 'Zweite Rolle');

        expect($posFirst)->not->toBeFalse()
            ->and($posSecond)->not->toBeFalse()
            ->and($posFirst)->toBeLessThan($posSecond);
    }

    $html = Member::leaderBoardHtml('de');

    expect(strpos($html, 'Erste Rolle'))->toBeLessThan(strpos($html, 'Zweite Rolle'));
});
