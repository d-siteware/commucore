<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\EmailChangeNotification;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;

test('current profile information is available', function (): void {
    $this->actingAs($user = User::factory()->create());

    $component = Livewire::test(UpdateProfileInformationForm::class);

    expect($component->state['name'])->toEqual($user->name);
    expect($component->state['email'])->toEqual($user->email);
});

test('profile information can be updated', function (): void {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state', ['name' => 'Test Name', 'email' => 'test@example.com'])
        ->call('updateProfileInformation');

    expect($user->fresh())
        ->name->toEqual('Test Name')
        ->email->toEqual('test@example.com');
});

test('profile information can be updated with email change and rollback option', function (): void {
    Notification::fake();
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'email_verified_at' => now(),
    ]);
    $this->actingAs($user);

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state', [
            'name' => 'Updated Name',
            'email' => 'new@example.com',
        ])
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $freshUser = $user->fresh();
    $this->assertEquals('Updated Name', $freshUser->name);
    $this->assertEquals('new@example.com', $freshUser->email);
    $this->assertNotNull($freshUser->email_verified_at);

    Notification::assertSentTo($user, EmailChangeNotification::class, function ($notification): true {
        $this->assertEquals('original@example.com', $notification->oldEmail);
        $this->assertEquals('new@example.com', $notification->newEmail);

        return true;
    });
});

test('email can be rolled back after typo', function (): void {
    $user = User::factory()->create(['email' => 'original@example.com']);
    $this->actingAs($user);

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state', ['name' => 'Test', 'email' => 'typo@exmple.com'])
        ->call('updateProfileInformation');

    $token = encrypt($user->id.'|original@example.com');
    $response = $this->get('/rollback-email?token='.$token);

    $response->assertRedirect('/dashboard');
    $this->assertEquals('original@example.com', $user->fresh()->email);
    $this->assertAuthenticatedAs($user);
});
