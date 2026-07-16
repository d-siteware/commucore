<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class CheckWorkerHealth extends Command
{
    protected $signature = 'queue:check-health
        {--supervisor-program=commucore-worker : Name des Supervisor-Programms}
        {--stale-threshold=10 : Minuten, ab denen ein Job als veraltet gilt}';

    protected $description = 'Prüft, ob der Queue-Worker läuft, und sendet eine Mail bei Ausfall';

    public function handle(): int
    {
        if ($this->checkSupervisor()) {
            $this->info('Worker läuft (Supervisor).');

            return self::SUCCESS;
        }

        if ($this->checkStaleJobs()) {
            $this->info('Worker läuft (keine veralteten Jobs).');

            return self::SUCCESS;
        }

        $this->notifyAdmin('Der Queue-Worker scheint nicht zu laufen.');
        $this->error('Worker nicht erreichbar – Admin wurde benachrichtigt.');

        return self::FAILURE;
    }

    private function checkSupervisor(): bool
    {
        $program = $this->option('supervisor-program');

        $output = null;
        $exitCode = null;

        exec("supervisorctl status {$program} 2>/dev/null", $output, $exitCode);

        if ($exitCode !== 0) {
            return false;
        }

        $line = implode(' ', $output);

        if (str_contains($line, 'RUNNING')) {
            return true;
        }

        if (str_contains($line, 'STOPPED') || str_contains($line, 'FATAL') || str_contains($line, 'BACKOFF')) {
            $this->notifyAdmin("Supervisor-Programm {$program} ist nicht RUNNING: {$line}");
        }

        return false;
    }

    private function checkStaleJobs(): bool
    {
        $threshold = now()->subMinutes((int) $this->option('stale-threshold'));
        $connection = config('queue.default', 'database');

        if ($connection !== 'database') {
            return false;
        }

        $table = config('queue.connections.database.table', 'jobs');

        try {
            $staleCount = \DB::connection(config('queue.connections.database.connection'))
                ->table($table)
                ->where('created_at', '<', $threshold->timestamp)
                ->count();

            if ($staleCount > 0) {
                $this->notifyAdmin("{$staleCount} Job(s) seit mehr als {$this->option('stale-threshold')} Minuten in der Warteschlange – Worker läuft möglicherweise nicht.");

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('CheckWorkerHealth: jobs-Tabelle nicht lesbar', ['error' => $e->getMessage()]);

            return false;
        }
    }

    private function notifyAdmin(string $message): void
    {
        Log::error("CheckWorkerHealth: {$message}");

        try {
            $recipients = \App\Models\User::where('is_admin', true)->get();

            if ($recipients->isEmpty()) {
                Log::warning('CheckWorkerHealth: keine Admin-User gefunden');

                return;
            }

            $appName = config('app.name', 'CommuCore');

            Mail::raw(
                "{$appName} – Worker-Health-Check\n\n{$message}\n\nZeitpunkt: " . now()->toIso8601String(),
                function ($mail) use ($recipients, $appName): void {
                    foreach ($recipients as $user) {
                        $mail->to($user->email);
                    }
                    $mail->subject("{$appName}: Worker-Ausfall festgestellt");
                },
            );
        } catch (\Throwable $e) {
            Log::error('CheckWorkerHealth: Mail-Versand fehlgeschlagen', ['error' => $e->getMessage()]);
        }
    }
}
