<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Livewire\Component;

/**
 * Ersetzt die Jetstream-DeleteUserForm.
 * Einzige Abweichung: nach dem Löschen landet der Nutzer auf der
 * Erklärungs-Seite statt still auf der öffentlichen Startseite.
 */
final class DeleteUserForm extends Component
{
    /**
     * Indicates if user deletion is being confirmed.
     */
    public bool $confirmingUserDeletion = false;

    /**
     * The user's current password.
     */
    public string $password = '';

    /**
     * Confirm that the user would like to delete their account.
     */
    public function confirmUserDeletion(): void
    {
        $this->resetErrorBag();

        $this->password = '';

        $this->dispatch('confirming-delete-user');

        $this->confirmingUserDeletion = true;
    }

    /**
     * Delete the current user.
     */
    public function deleteUser(Request $request, DeletesUsers $deleter, StatefulGuard $auth): void
    {
        $this->resetErrorBag();

        if (! Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        $deleter->delete(Auth::user()->fresh());

        $auth->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $this->redirect(route('account-deleted'));
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('profile.delete-user-form');
    }
}
