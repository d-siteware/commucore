<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Membership\Member;
use App\Models\Membership\MemberDocument;
use App\Models\User;

final class MemberDocumentPolicy
{
    /**
     * Dokumente eines Mitglieds auflisten.
     * Admin, Vorstand oder das Mitglied selbst.
     */
    public function viewAny(User $user, Member $member): bool
    {
        return $this->isAdminOrBoard($user)
            || $this->isOwnMember($user, $member);
    }

    /**
     * Ein einzelnes Dokument einsehen.
     */
    public function view(User $user, MemberDocument $document): bool
    {
        return $this->isAdminOrBoard($user)
            || $this->isOwnDocument($user, $document);
    }

    /**
     * Dokument hochladen – nur Admin oder Vorstand.
     * Mitglieder laden keine eigenen Dokumente hoch (Upload = Verwaltungsakt).
     */
    public function create(User $user): bool
    {
        return $this->isAdminOrBoard($user);
    }

    /**
     * Dokument herunterladen – gleiche Regel wie view.
     */
    public function download(User $user, MemberDocument $document): bool
    {
        return $this->view($user, $document);
    }

    /**
     * Dokument löschen (Soft Delete) – nur Admin.
     * Vorstand darf nicht löschen, nur einsehen.
     */
    public function delete(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Gelöschte Dokumente wiederherstellen – nur Admin.
     */
    public function restore(User $user, MemberDocument $document): bool
    {
        return $this->isAdmin($user);
    }

    // -------------------------------------------------------------------------
    // Hilfsmethoden
    // -------------------------------------------------------------------------

    private function isAdmin(User $user): bool
    {
        // Passe das an deine bestehende Admin-Logik an
        return $user->is_admin;
    }

    private function isAdminOrBoard(User $user): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // Über den verknüpften Member prüfen ob Vorstand
        return $user->member?->isBoardMember() ?? false;
    }

    private function isOwnMember(User $user, Member $member): bool
    {
        return $user->id === $member->user_id;
    }

    private function isOwnDocument(User $user, MemberDocument $document): bool
    {
        return $user->id === $document->member->user_id;
    }
}
