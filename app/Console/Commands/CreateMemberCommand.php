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
            'email' => $user->email,
            'name' => $lastName ?? $user->name ?? '',
            'first_name' => $firstName ?? $user->first_name ?? '',
            'applied_at' => now(),
            'gdpr_consent_at' => now(),
            'type' => $memberType,
            'fee_type' => $memberFee,
            'gender' => Gender::na,
        ]);

        $this->components->info("Member erstellt für '{$email}'.");

        return 0;
    }
}
