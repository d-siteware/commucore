<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm;
use Livewire\Livewire;

test('user accounts can be deleted', function (): void {
    Queue::fake();
    $admin = User::factory()->create([
        'is_admin' => true,
        'password' => bcrypt('password'), // Ensure password is hashed
    ]);
    $this->actingAs($admin);

    $admin->email = 'admin@example.com';
    $admin->save();

    Livewire::test(DeleteUserForm::class)
        ->set('password', 'password') // Plaintext password for form input
        ->call('deleteUser');

    expect($admin->fresh())->toBeNull();
})->skip(function (): bool {
    return ! Features::hasAccountDeletionFeatures();
}, 'Account deletion is not enabled.');

test('correct password must be provided before account can be deleted', function (): void {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(DeleteUserForm::class)
        ->set('password', 'wrong-password')
        ->call('deleteUser')
        ->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
})->skip(function (): bool {
    return ! Features::hasAccountDeletionFeatures();
}, 'Account deletion is not enabled.');

test('user deletion triggers history', function (): void {
    config(['queue.default' => 'sync']);
    $this->actingAs($user = User::factory()->create());
    $user->delete();

    expect($user->fresh())->toBeNull();
    expect(\App\Models\History::where('action', 'deleted')->count())->toBe(1);
});
