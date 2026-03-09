<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Alle eingeloggten Nutzer dürfen die Dokumentenliste sehen.
     */
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    /**
     * Zugriff auf ein Dokument: delegiert an die Policy des Eltern-Models.
     * Falls documentable nicht geladen oder gelöscht → verweigern.
     */
    public function view(User $user, Document $document): bool
    {
        $documentable = $document->documentable;

        if ($documentable === null) {
            return false;
        }

        // Policy des Eltern-Models prüfen (FundingPolicy, ProjectPolicy, etc.)
        return $user->can('view', $documentable);
    }

    /**
     * Upload: delegiert an 'update' des Eltern-Models.
     */
    public function create(User $user, Document $document): bool
    {
        $documentable = $document->documentable;

        if ($documentable === null) {
            return false;
        }

        return $user->can('update', $documentable);
    }

    /**
     * Löschen: delegiert an 'update' des Eltern-Models,
     * oder eigener Upload (Uploader darf immer löschen).
     */
    public function delete(User $user, Document $document): bool
    {
        if ($document->uploaded_by_user_id === $user->id) {
            return true;
        }

        $documentable = $document->documentable;

        if ($documentable === null) {
            return false;
        }

        return $user->can('update', $documentable);
    }

    public function update(User $user, Document $document): bool
    {
        return false;
    }

    public function restore(User $user, Document $document): bool
    {
        return false;
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return false;
    }
}