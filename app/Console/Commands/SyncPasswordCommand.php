<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SyncPasswordCommand extends Command
{
    protected $signature = 'commucore:sync-password
        {--email= : E-Mail-Adresse des Users}
        {--hash=  : Bereits gehashtes Passwort}';

    protected $description = 'Synchronisiert das Passwort eines Users aus der App-DB in die Instanz';

    public function handle(): int
    {
        $email = $this->option('email');
        $hash  = $this->option('hash');

        if (! $email || ! $hash) {
            $this->components->error('E-Mail und Hash sind erforderlich.');
            return 1;
        }

        $affected = User::where('email', $email)
            ->update(['password' => $hash]);

        if ($affected === 0) {
            $this->components->warn("Kein User mit E-Mail {$email} gefunden.");
            return 1;
        }

        $this->components->info("Passwort für {$email} erfolgreich synchronisiert.");
        return 0;
    }
}