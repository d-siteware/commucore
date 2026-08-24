<?php

namespace App\Console\Commands;

use App\Enums\Gender;
use App\Enums\MemberFeeType;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Console\Command;

class CreateMemberCommand extends Command
{
    protected $signature = 'commucore:create-member
        {--email=      : E-Mail des Users (muss in der Instanz existieren)}
        {--first-name= : Vorname}
        {--last-name=  : Nachname}
        {--type=MD     : Member-Typ (AP, MD, AD, ...)}
        {--fee=full    : Mitgliedsbeitrag (full, discounted, free)}';

    protected $description = 'Legt einen Member-Eintrag für einen existierenden User an';

    public function handle(): int
    {
        $email = $this->option('email');
        $firstName = $this->option('first-name');
        $lastName = $this->option('last-name');
        $type = $this->option('type');
        $fee = $this->option('fee');

        if (! $email) {
            $this->components->error('--email ist erforderlich.');

            return 1;
        }

        if (! $lastName) {
            $this->components->error('--last-name ist erforderlich.');

            return 1;
        }

        // Duplikat-Schutz unabhängig davon, ob ein verknüpfter User existiert
        if (Member::where('email', $email)->exists()) {
            $this->components->warn("Member mit '{$email}' existiert bereits — wird übersprungen.");

            return 0;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {

            $this->components->info("User '{$email}' nicht gefunden. Mitglied ohne verknüpften Nutzer angelegt.");
            $user_id=null;

        } else {

            $user_id=$user->id;

            if (Member::where('user_id', $user->id)
                ->exists()) {
                $this->components->warn("Member für '{$email}' existiert bereits — wird übersprungen.");

                return 0;
            }
        }

        $memberType = MemberType::tryFrom($type) ?? MemberType::MD;
        $memberFee = MemberFeeType::tryFrom($fee) ?? MemberFeeType::FULL;

        Member::create([
            'user_id' => $user_id,
            'email' => $email,
            'name' => $lastName,
            'first_name' => $firstName ?? '',
            'applied_at' => now(),
            // Kein gdpr_consent_at: Einwilligung wurde nicht erteilt,
            // der Zeitstempel darf nicht fabriziert werden (DSGVO-Audit-Daten).
            'type' => $memberType,
            'fee_type' => $memberFee,
            'gender' => Gender::na,
        ]);

        $this->components->info("Member erstellt für '{$email}'.");

        return 0;
    }
}
