<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminCommand extends Command
{
    protected $signature = 'commucore:create-admin
        {--email= : E-Mail-Adresse des Admins}
        {--first-name=  : Vorname des Admins}
        {--last-name=  : Name des Admins}
        {--send-invite : Einladungs-E-Mail versenden}';

    protected $description = 'Erstellt den ersten Admin-User in einer neuen Instanz';

    public function handle(): int
    {
        $email = $this->option('email');
        $name = $this->option('last-name');
        $first_name = $this->option('first-name');


        if (!$email) {
            $this->components->error('E-Mail ist erforderlich.');

            return 1;
        }
        if (!$name) {
            $this->components->error('Name ist erforderlich.');

            return 1;
        }

        if (User::where('email', $email)
            ->exists()) {
            $this->components->warn("User {$email} existiert bereits.");

            return 0;
        }

        // Temporäres Passwort – wird beim ersten Login geändert
        $tempPassword = Str::random(32);

        $this->components->task('Admin-User erstellen', function () use ($email, $name, $first_name, $tempPassword) {
           return User::create([
                'first_name'        => $first_name,
                'name'              => $name,
                'email'             => $email,
                'password'          => Hash::make($tempPassword),
                'is_admin'          => true,
                'locale'            => 'de',
                'email_verified_at' => now(),
            ]);
        });

        // Einladungs-E-Mail
        if ($this->option('send-invite')) {
            $token = app('auth.password.broker')->createToken($user);
            $user->sendPasswordResetNotification($token);
            $this->components->info("Einladungs-E-Mail gesendet an: {$email}");
        }

        return 0;
    }
}
