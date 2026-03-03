<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\History;
use App\Models\User;

/**
 * History is an append-only audit log (DSGVO Art. 5 Abs. 2 – Rechenschaftspflicht).
 * No user – including admins – may update or delete entries.
 */
final class HistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, History $history): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        // Creation happens only via RecordHistory Job, never directly via UI.
        return false;
    }

    public function update(User $user, History $history): bool
    {
        return false;
    }

    public function delete(User $user, History $history): bool
    {
        return false;
    }

    public function forceDelete(User $user, History $history): bool
    {
        return false;
    }
}
