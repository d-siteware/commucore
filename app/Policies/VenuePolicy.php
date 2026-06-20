<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;
use App\Policies\Traits\HasAdminPrivileges;

class VenuePolicy
{
    use HasAdminPrivileges;

    /**
     * Jeder eingeloggte Nutzer darf Venues sehen (z. B. um sie bei
     * Events auszuwählen) — die Einschränkung greift erst bei
     * Anlegen/Bearbeiten/Löschen.
     */
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, Venue $venue): bool
    {
        return $user->exists;
    }

    public function create(User $user): bool
    {
        return $this->getAdminPrivileges($user);
    }

    public function update(User $user, Venue $venue): bool
    {
        return $this->getAdminPrivileges($user);
    }

    public function delete(User $user, Venue $venue): bool
    {
        return $this->getAdminPrivileges($user);
    }

    public function restore(): bool
    {
        return false;
    }

    public function forceDelete(): bool
    {
        return false;
    }
}
