<?php

declare(strict_types=1);

use App\Models\Membership\Invitation;
use App\Models\Membership\Member;
use App\Models\User;
use Laravel\Fortify\Features;

test('registration screen cannot be rendered without token', function (): void {
    Log::shouldReceive('alert')->once()
        ->withArgs(fn (string $msg): bool => str_contains($msg, 'Failed due to invalid or expired invitation'));

    $response = $this->get('/members/register');
    $response->assertStatus(302)
        ->assertSessionHas('error', 'Invalid or expired invitation link. Requested token: ');
});

test('registration screen can be rendered with a valid token', function (): void {
    // Arrange: Create a member and invitation
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'locale' => 'de', // Assuming Member has a locale field
    ]);
    $invitation = Invitation::factory()->create([
        'email' => $member->email,
        'token' => 'valid-token-123',
        'accepted' => false,
    ]);

    // Act: Visit the registration route with the token
    $response = $this->get('/members/register?token='.$invitation->token);

    // Assert: Check for success
    $response->assertStatus(200)
        ->assertViewIs('auth.register-member')
        ->assertViewHas('token', $invitation->token)
        ->assertViewHas('invitation', $invitation)
        ->assertViewHas('member', $member);
});

test('registration screen cannot be rendered if support is disabled', function (): void {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(function () {
    return Features::enabled(Features::registration());
}, 'Registration support is enabled.');

test('user can register and login with a valid invitation token', function (): void {
    // Arrange: Create a member and invitation
    $member = Member::factory()->create([
        'email' => 'test@example.com',
        'first_name' => 'Test',
        'name' => 'User',
        'locale' => 'de',
        'user_id' => null, // No user linked yet
    ]);
    $invitation = Invitation::factory()->create([
        'email' => $member->email,
        'token' => 'valid-token-123',
        'accepted' => false,
    ]);

    // Act 1: Check the registration form renders with a valid token
    $response = $this->get('/members/register?token='.$invitation->token);
    $response->assertStatus(200)
        ->assertViewIs('auth.register-member')
        ->assertViewHas('token', $invitation->token)
        ->assertViewHas('invitation', $invitation)
        ->assertViewHas('member', $member);

    // Act 2: Submit the registration form
    $response = $this->post('/members/register', [
        'token' => $invitation->token,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    // Assert: Registration succeeds and user is logged in
    $response->assertRedirect(route('dashboard'));
    $this->assertTrue(Auth::guard('web')->check());

    // Verify the user was created and linked
    $user = User::where('email', $member->email)->first();
    $this->assertNotNull($user);
    $this->assertEquals($member->fresh()->user_id, $user->id);
    $this->assertNotNull($user->email_verified_at);
    $this->assertEquals($user->id, Auth::guard('web')->id());

    // Verify invitation is marked as accepted
    $this->assertDatabaseHas('invitations', [
        'id' => $invitation->id,
        'accepted' => true,
    ]);

    // Verify member is linked to the user
    $this->assertDatabaseHas('members', [
        'id' => $member->id,
        'user_id' => $user->id,
    ]);
});

test('fortify registration route is disabled', function (): void {
    $response = $this->get('/register');
    $response->assertStatus(404);
});
