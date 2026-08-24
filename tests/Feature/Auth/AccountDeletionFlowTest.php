<?php

declare(strict_types=1);

use App\Livewire\Profile\DeleteUserForm;
use App\Models\History;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Features;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Post-Deletion-Sackgasse + sichtbare Delete-Pfade
|--------------------------------------------------------------------------
| Verhindert den stummen Login-Loop nach einer Selbstlöschung:
| - DeleteUserForm leitet auf /account-deleted statt auf /
| - der Delete-Pfad loggt inkl. is_last_admin
| - SSO mit Token eines gelöschten Users landet auf /account-deleted
*/

beforeEach(function (): void {
    config(['sso.secret' => 'test-secret']);
});

function makeSsoToken(string $email, string $subdomain = 'commucore', ?int $expires = null, string $redirect = '/dashboard'): string
{
    $expires ??= now()->addMinute()->timestamp;

    $payload = strtr(base64_encode(implode('|', [$email, $subdomain, $expires, $redirect])), '+/', '-_');
    $hmac = hash_hmac('sha256', $payload, config('sso.secret'));

    return "{$payload}.{$hmac}";
}

test('account deletion redirects to the explanation page and writes history synchronously', function (): void {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);
    $this->actingAs($user);

    Livewire::test(DeleteUserForm::class)
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertRedirect(route('account-deleted'));

    expect($user->fresh())->toBeNull();

    // Synchroner Audit-Trail: deleted-Eintrag existiert, Logout-nach-Löschung
    // (remember_token-Rotation) erzeugt weder Exception noch History-Eintrag.
    expect(History::where('historable_type', User::class)
        ->where('historable_id', $user->id)
        ->where('action', 'deleted')
        ->exists())->toBeTrue();
    expect(History::where('action', 'updated')->count())->toBe(0);
})->skip(fn (): bool => ! Features::hasAccountDeletionFeatures(), 'Account deletion is not enabled.');

test('account deletion logs a warning including is_last_admin', function (): void {
    Log::spy();

    $user = User::factory()->create([
        'is_admin' => true,
        'password' => bcrypt('password'),
    ]);
    $this->actingAs($user);

    Livewire::test(DeleteUserForm::class)
        ->set('password', 'password')
        ->call('deleteUser');

    Log::shouldHaveReceived('warning')
        ->with('Self-deletion durchgeführt', Mockery::on(fn ($context): bool => $context['scope'] === 'instance'
            && $context['user_id'] === $user->id
            && $context['email'] === $user->email
            && $context['is_last_admin'] === true
            && $context['subdomain'] === 'commucore'));
})->skip(fn (): bool => ! Features::hasAccountDeletionFeatures(), 'Account deletion is not enabled.');

test('account deletion logs is_last_admin false when another admin exists', function (): void {
    Log::spy();

    User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create([
        'is_admin' => true,
        'password' => bcrypt('password'),
    ]);
    $this->actingAs($user);

    Livewire::test(DeleteUserForm::class)
        ->set('password', 'password')
        ->call('deleteUser');

    Log::shouldHaveReceived('warning')
        ->with('Self-deletion durchgeführt', Mockery::on(fn ($context): bool => $context['is_last_admin'] === false));
})->skip(fn (): bool => ! Features::hasAccountDeletionFeatures(), 'Account deletion is not enabled.');

test('account-deleted page renders explanation and contact cta', function (): void {
    $response = $this->get('/account-deleted');

    $response->assertStatus(200);
    $response->assertSee(__('auth.account_deleted.title'));
    $response->assertSee(__('auth.account_deleted.text'));
    $response->assertSee('helpdesk@commu-core.app');
});

test('sso login with token of a deleted user lands on the explanation page', function (): void {
    $user = User::factory()->create(['email' => 'patrick.flatten@t-online.de']);
    $user->delete();

    $token = makeSsoToken('patrick.flatten@t-online.de');

    $this->get('/auth/sso?token='.$token)
        ->assertRedirect(route('account-deleted'));

    $this->assertGuest();
});

test('sso login still works for an existing user', function (): void {
    $user = User::factory()->create(['email' => 'patrick.flatten@t-online.de']);

    $token = makeSsoToken($user->email);

    $this->get('/auth/sso?token='.$token)
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
});

test('sso login with invalid signature still lands on login with error', function (): void {
    $payload = strtr(base64_encode(implode('|', ['x@y.de', 'commucore', now()->addMinute()->timestamp, '/dashboard'])), '+/', '-_');

    $this->get('/auth/sso?token='.$payload.'.invalid-hmac')
        ->assertRedirect('/login');
});
