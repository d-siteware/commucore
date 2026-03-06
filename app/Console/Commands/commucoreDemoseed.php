<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class commucoreDemoseed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commucore:demoseeder
           {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Empty current database and fill it with demo datasets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        intro('CommuCore Demo Seeder');

        if (getenv('APP_ENV') !== 'demo') {
            if (! $this->loginSysAdmin()) {
                return 1;
            }
        }

        if ($this->option('force')) {
            $this->seedDatabase();

            return 0;
        }

        $this->components->warn('This seeder will reset your entire database. All data will be lost and cannot be restored!');
        $this->newLine();

        if (confirm('Do you want to proceed?', default: false)) {
            $this->seedDatabase();

            return 0;
        }

        $this->components->info('Seeding canceled. No changes were made.');

        return 1;
    }

    protected function seedDatabase(): void
    {
        $this->components->task('Enabling maintenance mode', fn () => Artisan::call('down --render="maintenance"') === 0);

        $this->components->task('Resetting database', fn () => Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]) === 0);

        $this->components->task('Seeding demo data', fn () => Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]) === 0);

        $this->components->task('Disabling maintenance mode', fn () => Artisan::call('up') === 0);

        outro('Demo data seeded successfully!');
    }

    protected function loginSysAdmin(): bool
    {
        $this->components->warn('Your CommuCore instance is not running in demo mode. Please authenticate with a SysAdmin account to continue.');
        $this->newLine();

        $email = text(
            label: 'SysAdmin e-mail address',
            placeholder: 'admin@example.com',
            required: 'Please provide an e-mail address.',
            validate: fn ($val) => filter_var($val, FILTER_VALIDATE_EMAIL) ? null : 'Please enter a valid e-mail address.',
        );

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $pw = password(
                label: 'Password',
                required: 'Please provide the password.',
            );

            if ($this->verifySysAdmin($email, $pw)) {
                return true;
            }

            $remaining = 3 - $attempt;
            if ($remaining > 0) {
                $this->components->warn("Invalid credentials. {$remaining} attempt(s) remaining.");
            }
        }

        $this->components->error('Too many failed login attempts. Seeding canceled.');

        return false;
    }

    protected function verifySysAdmin(string $email, string $password): bool
    {
        $user = User::select(['password', 'is_admin'])->where('email', $email)->first();

        return $user && $user->is_admin && password_verify($password, $user->password);
    }
}
