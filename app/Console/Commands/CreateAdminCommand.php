<?php

namespace App\Console\Commands;

use App\Enums\Role as RoleEnum;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminCommand extends Command
{
    protected $signature = 'commucore:create-admin
        {--email= : E-Mail-Adresse des Admins}
        {--name=  : Name des Admins}
        {--send-invite : Einladungs-E-Mail versenden}';

    protected $description = 'Erstellt den ersten Admin-User in einer neuen Instanz';

    public function handle(): int
    {
        $email  = $this->option('email');
        $name   = $this->option('name');

        if (!$email || !$name) {
            $this->components->error('E-Mail und Name sind erforderlich.');
            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->components->warn("User {$email} existiert bereits.");
            return 0;
        }

        // Temporäres Passwort – wird beim ersten Login geändert
        $tempPassword = Str::random(32);

        $user = User::create([
            'name'              => $name,
            'email'             => $email,
            'password'          => Hash::make($tempPassword),
            'is_admin'          => true,
            'locale'            => 'de',
            'email_verified_at' => now(),
        ]);

        $this->components->info("Admin-User erstellt: {$email}");

        // Einladungs-E-Mail
        if ($this->option('send-invite')) {
            $token = app('auth.password.broker')->createToken($user);
            $user->sendPasswordResetNotification($token);
            $this->components->info("Einladungs-E-Mail gesendet an: {$email}");
        }

        return 0;
    }
}