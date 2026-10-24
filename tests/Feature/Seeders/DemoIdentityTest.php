<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use App\Models\User;
use Database\Seeders\Demo\DemoIdentity;
use Database\Seeders\Demo\DemoUserSeeder;
use Database\Seeders\Demo\MemberSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('derives demo email addresses from the generated person name', function (): void {
    $identity = DemoIdentity::make();
    $emailLocalPart = Str::before($identity['email'], '@');
    $nameLocalPart = Str::slug($identity['first_name'].' '.$identity['last_name']);

    expect($emailLocalPart)->toStartWith($nameLocalPart)
        ->and($identity['email'])->toEndWith('@example.test');
});

it('keeps demo login emails stable when generating a coherent name', function (): void {
    $identity = DemoIdentity::named('Admin', 'User', 'admin.user@commu-core.app');

    expect($identity['email'])->toBe('admin.user@commu-core.app')
        ->and($identity['first_name'])->toBe('Admin')
        ->and($identity['last_name'])->toBe('User');
});

it('seeds member emails from each generated name', function (): void {
    (new MemberSeeder)->run();

    $members = Member::query()->where('email', 'like', '%@example.test')->get();

    expect($members)->toHaveCount(45);

    foreach ($members as $member) {
        expect($member->email)->toStartWith(Str::slug($member->first_name.' '.$member->name));
    }
});

it('keeps each demo login and its linked member on the same identity', function (): void {
    (new DemoUserSeeder)->run();

    foreach (['standard.user@commu-core.app', 'admin.user@commu-core.app'] as $email) {
        $user = User::query()->where('email', $email)->firstOrFail();
        $member = Member::query()->where('user_id', $user->id)->firstOrFail();

        expect($member->email)->toBe($user->email)
            ->and($member->first_name)->toBe($user->first_name)
            ->and($user->name)->toBe($user->first_name.' '.$member->name);
    }
});
