<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\User;
use App\Policies\Traits\HasAdminPrivileges;

final class TransactionPolicy
{
    use HasAdminPrivileges;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $fiscalYear = FiscalYear::getCurrent();

        if ($fiscalYear && $fiscalYear->isClosed()) {
            return false;
        }

        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        if ($transaction->fiscalYear?->isClosed()) {
            return $user->is_admin;
        }

        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        if ($transaction->fiscalYear?->isClosed()) {
            return false;
        }

        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(): bool
    {
        return false;
    }

    public function addAccount(User $user): bool
    {

        return $this->getAdminPrivileges($user);
    }
}
