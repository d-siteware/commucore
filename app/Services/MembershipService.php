<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Notifications\MembershipCancelledNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MembershipService
{
    public function cancelMembership(Member $member, Carbon $leftAt): void
    {
        $member->update([
            'left_at' => $leftAt,
            'member_type' => MemberType::EX,
        ]);

        // User-Account bleibt aktiv bis zur Pseudonymisierung
        // Notification an Mitglied
        $member->notify(new MembershipCancelledNotification($member, $leftAt));
    }

    public function pseudonymizeMember(Member $member): void
    {
        // Prüfen ob referenzierte Daten existieren
        $hasReferences = $member->transactions()->exists()
            || $member->documents()->exists()
            || $member->roles()->exists();

        /**
         * TODO
         * || $member->attendees()->exists()
         * || $member->eventAssignments()->exists()
         * || $member->actionItems()->exists()
         */
        DB::transaction(function () use ($member, $hasReferences) {
            $member->update([
                'first_name' => 'Ehem.',
                'last_name' => 'Mitglied',
                'email' => null,
                'phone' => null,
                'address' => null,
                // weitere personenbezogene Felder...
                'pseudonymized_at' => now(),
            ]);

            // User-Account deaktivieren/anonymisieren
            if ($member->user) {
                $member->user->update([
                    'name' => 'Ehemaliges Mitglied',
                    'email' => 'deleted-'.$member->id.'@pseudonymized.invalid',
                    'password' => bcrypt(Str::random(64)),
                    'email_verified_at' => null,
                ]);
            }

            // DB-Delete nur wenn keine Referenzen
            if (! $hasReferences) {
                $member->delete();
            }
        });
    }
}
