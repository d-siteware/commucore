<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Accounting\Transaction;
use App\Models\User;
use App\Policies\Traits\HasAdminPrivileges;

final class TransactionPolicy
{
    use HasAdminPrivileges;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Prüfe ob das aktuelle FY geschlossen ist
        $currentYear = (int) session('financialYear');
        $fiscalYear = \App\Models\Accounting\FiscalYear::where('year', $currentYear)->first();

        if ($fiscalYear && $fiscalYear->isClosed()) {
            return false; // Auch Admins können keine Transaktionen in geschlossenen Jahren anlegen
        }

        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        $currentYear = (int) session('financialYear');

        // Prüfe ob Transaction in einem geschlossenen FY ist
        if ($transaction->isLockedInFiscalYear($currentYear)) {
            // Nur Admins dürfen gesperrte Transaktionen bearbeiten (z.B. für Korrekturen)
            return $user->is_admin;
        }

        // Standard-Berechtigungen
        return $this->getAdminPrivileges($user);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        $currentYear = (int) session('financialYear');

        // Gesperrte Transaktionen können NIE gelöscht werden
        if ($transaction->isLockedInFiscalYear($currentYear)) {
            return false;
        }

        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return false;
    }

    public function addAccount(User $user): bool
    {

        return $this->getAdminPrivileges($user);
    }
}
