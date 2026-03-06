<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class CommuCoreInstall extends Command
{
    protected $signature = 'commucore:install
           {--skip-admin : Skip admin user creation}
           {--skip-organization : Skip organization setup}';

    protected $description = 'Initial setup wizard for CommuCore installation';

    protected SettingsService $settings;

    public function handle(SettingsService $settings): int
    {
        $this->settings = $settings;

        intro('CommuCore Installation Wizard');

        $this->components->task('Enabling maintenance mode', fn (): bool => Artisan::call('down --render="maintenance"') === 0);

        // ── Step 1: .env setup (optional) ────────────────────────────────────
        if (confirm('Configure environment settings (.env)?', default: true) && ! $this->setupEnvironment()) {
            $this->components->error('Environment setup failed. Installation aborted.');

            return 1;
        }

        // ── Step 2: Migrations ────────────────────────────────────────────────
        if (confirm('Run database migrations?', default: true) && ! $this->runMigrations()) {
            $this->components->error('Migrations failed. Installation aborted.');

            return 1;
        }

        // ── Step 3: DatabaseSeeder ────────────────────────────────────────────
        if (confirm('Run DatabaseSeeder?', default: true)) {
            spin(
                callback: fn () => Artisan::call('db:seed', ['--force' => true]),
                message: 'Seeding database...'
            );
            $this->components->info('Database seeded');
        }

        // ── Step 4: Admin user ────────────────────────────────────────────────
        if (! $this->option('skip-admin')) {
            $this->newLine();
            $this->setupAdminUser();
        }

        // ── Step 5: Organization ──────────────────────────────────────────────
        if (! $this->option('skip-organization')) {
            $this->newLine();
            $this->setupOrganization();
        }

        $this->components->task('Disabling maintenance mode', fn (): bool => Artisan::call('up') === 0);

        outro('CommuCore installed successfully! Visit the admin panel to continue setup.');

        return 0;
    }

    // ── Environment Setup ─────────────────────────────────────────────────────

    protected function setupEnvironment(): bool
    {
        info('Environment Configuration');

        $envInstaller = base_path('.env.installer');

        if (! file_exists($envInstaller)) {
            $this->components->error('.env.installer not found in project root.');

            return false;
        }

        // Collect all values first, then write once at the end
        $config = [];

        // ── App ───────────────────────────────────────────────────────────────
        note('Application');

        $config['app_name'] = text(
            label: 'Application name',
            default: 'CommuCore',
            required: true,
        );

        $config['app_url'] = text(
            label: 'Application URL',
            placeholder: 'https://example.com',
            required: true,
            validate: fn ($val): ?string => filter_var($val, FILTER_VALIDATE_URL) ? null : 'Please enter a valid URL.',
        );

        // ── Database ──────────────────────────────────────────────────────────
        note('Database');

        $this->components->twoColumnDetail('Skip this section', 'uses SQLite by default');
        $this->newLine();

        if (confirm('Configure database connection?', default: true)) {
            $config['db_connection'] = 'mysql';
            $config['db_host'] = text(label: 'Database host', default: '127.0.0.1', required: true);
            $config['db_port'] = text(label: 'Database port', default: '3306', required: true);
            $config['db_name'] = text(label: 'Database name', required: true);
            $config['db_user'] = text(label: 'Database user', required: true);
            $config['db_password'] = password(label: 'Database password');

            $connected = spin(
                callback: fn (): bool => $this->testDbConnection(
                    $config['db_host'],
                    $config['db_port'],
                    $config['db_name'],
                    $config['db_user'],
                    $config['db_password']
                ),
                message: 'Testing database connection...'
            );

            if (! $connected) {
                $this->components->error('Could not connect to the database. Please check your credentials.');
                if (! confirm('Continue anyway?', default: false)) {
                    return false;
                }
            } else {
                $this->components->info('Database connection successful');
            }
        } else {
            $config['db_connection'] = 'sqlite';
            $this->components->info('Using SQLite – DB_CONNECTION=sqlite');
        }

        // ── Mail ──────────────────────────────────────────────────────────────
        note('Mail Server');

        $this->components->twoColumnDetail('Skip this section', 'mails are logged locally (MAIL_MAILER=log)');
        $this->newLine();

        if (confirm('Configure mail server?', default: true)) {
            $config['mail_mailer'] = 'smtp';
            $config['mail_host'] = text(label: 'Mail host', placeholder: 'smtp.example.com', required: true);
            $config['mail_port'] = text(label: 'Mail port', default: '587', required: true);
            $config['mail_from'] = text(
                label: 'Mail from address',
                placeholder: 'no-reply@example.com',
                required: true,
                validate: fn ($val): ?string => filter_var($val, FILTER_VALIDATE_EMAIL)
                    ? null
                    : 'Please enter a valid e-mail address.',
            );
            $config['mail_from_name'] = text(label: 'Mail from name', default: $config['app_name'], required: true);
            $config['mail_user'] = text(label: 'Mail username');
            $config['mail_password'] = password(label: 'Mail password');
        } else {
            $config['mail_mailer'] = 'log';
            $this->components->info('Using log mailer – outgoing mails will be written to the Laravel log');
        }

        // ── Turnstile ─────────────────────────────────────────────────────────
        note('Cloudflare Turnstile');

        $this->components->twoColumnDetail('Skip this section', 'Turnstile will be disabled (forms work without CAPTCHA)');
        $this->newLine();

        if (confirm('Enable Cloudflare Turnstile (CAPTCHA)?', default: false)) {
            if (confirm('Show instructions on how to get Turnstile keys?', default: false)) {
                $this->newLine();
                $this->components->bulletList([
                    'Go to https://dash.cloudflare.com and log in',
                    'Navigate to Turnstile in the left sidebar',
                    'Click "Add site" and enter your domain',
                    'Choose widget type: "Managed" is recommended',
                    'Copy the Site Key and Secret Key shown after creation',
                ]);
                $this->newLine();
            }

            $config['turnstile_enabled'] = 'true';
            $config['turnstile_site_key'] = text(
                label: 'Turnstile site key',
                placeholder: '0x4AAAAAAA...',
                required: true,
            );
            $config['turnstile_secret_key'] = text(
                label: 'Turnstile secret key',
                placeholder: '0x4AAAAAAA...',
                required: true,
            );
        } else {
            $config['turnstile_enabled'] = 'false';
            $this->components->info('Turnstile disabled – forms with CAPTCHA will skip validation');
        }

        // ── Write .env ────────────────────────────────────────────────────────
        $writeError = null;

        spin(
            callback: function () use ($envInstaller, $config, &$writeError): void {
                try {
                    if ($this->backupEnv()) {
                        $this->newLine();
                        $this->components->info('Generating backup of existing .env file...');
                        $this->newLine();
                    }

                    $envTarget = base_path('.env');
                    copy($envInstaller, $envTarget);

                    $lines = [
                        '',
                        '# Application',
                        'APP_NAME="'.addslashes($config['app_name']).'"',
                        'APP_URL='.$config['app_url'],
                        '',
                        '# Database',
                        'DB_CONNECTION='.$config['db_connection'],
                    ];

                    if ($config['db_connection'] === 'mysql') {
                        $lines[] = 'DB_HOST='.$config['db_host'];
                        $lines[] = 'DB_PORT='.$config['db_port'];
                        $lines[] = 'DB_DATABASE='.$config['db_name'];
                        $lines[] = 'DB_USERNAME='.$config['db_user'];
                        $lines[] = 'DB_PASSWORD="'.addslashes($config['db_password']).'"';
                    }

                    $lines[] = '';
                    $lines[] = '# Mail';
                    $lines[] = 'MAIL_MAILER='.$config['mail_mailer'];

                    if ($config['mail_mailer'] === 'smtp') {
                        $lines[] = 'MAIL_HOST='.$config['mail_host'];
                        $lines[] = 'MAIL_PORT='.$config['mail_port'];
                        $lines[] = 'MAIL_FROM_ADDRESS='.$config['mail_from'];
                        $lines[] = 'MAIL_FROM_NAME="'.addslashes($config['mail_from_name']).'"';
                        if ($config['mail_user'] !== '' && $config['mail_user'] !== '0') {
                            $lines[] = 'MAIL_USERNAME='.$config['mail_user'];
                        }
                        if ($config['mail_password'] !== '' && $config['mail_password'] !== '0') {
                            $lines[] = 'MAIL_PASSWORD="'.addslashes($config['mail_password']).'"';
                        }
                    }

                    $lines[] = '';
                    $lines[] = '# Cloudflare Turnstile';
                    $lines[] = 'TURNSTILE_ENABLED='.$config['turnstile_enabled'];

                    if ($config['turnstile_enabled'] === 'true') {
                        $lines[] = 'TURNSTILE_SITE_KEY='.$config['turnstile_site_key'];
                        $lines[] = 'TURNSTILE_SECRET_KEY='.$config['turnstile_secret_key'];
                    }

                    file_put_contents($envTarget, implode("\n", $lines), FILE_APPEND);
                } catch (\Exception $e) {
                    $writeError = $e->getMessage();
                }
            },
            message: 'Writing .env file...'
        );

        if ($writeError !== null) {
            $this->components->error('Failed to write .env file: '.$writeError);

            return false;
        }

        $this->components->info('.env created from env.installer');

        // Clear config cache so Laravel picks up the new .env before key:generate
        Artisan::call('config:clear');

        spin(
            callback: fn () => Artisan::call('key:generate', ['--force' => true]),
            message: 'Generating application key...'
        );
        $this->components->info('Application key generated');

        return true;
    }

    protected function testDbConnection(
        string $host,
        string $port,
        string $name,
        string $user,
        string $password
    ): bool {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
            new \PDO($dsn, $user, $password, [\PDO::ATTR_TIMEOUT => 5]);

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    // ── Migrations ────────────────────────────────────────────────────────────

    protected function runMigrations(): bool
    {
        $exitCode = null;

        spin(
            callback: function () use (&$exitCode): void {
                $exitCode = Artisan::call('migrate', ['--force' => true]);
            },
            message: 'Running migrations...'
        );

        if ($exitCode !== 0) {
            return false;
        }

        $this->components->info('Migrations completed');

        return true;
    }

    // ── Admin User ────────────────────────────────────────────────────────────

    protected function setupAdminUser(): void
    {
        info('Administrator Account');

        if (User::where('is_admin', 1)->exists()) {
            $this->components->warn('An administrator account already exists.');
            if (! confirm('Create another administrator?', default: false)) {
                return;
            }
        }

        $name = text(
            label: 'Full name',
            placeholder: 'Jane Doe',
            required: true,
            validate: fn ($val): ?string => $this->validateField($val, 'required|string|max:255'),
        );

        $email = text(
            label: 'E-mail address',
            placeholder: 'admin@example.com',
            required: true,
            validate: fn ($val): ?string => $this->validateField($val, 'required|email|unique:users,email'),
        );

        $adminPassword = password(
            label: 'Password',
            required: true,
            validate: fn ($val): ?string => $this->validateField($val, 'required|min:8'),
        );

        password(
            label: 'Confirm password',
            required: true,
            validate: fn ($val): ?string => $val !== $adminPassword ? 'Passwords do not match.' : null,
        );

        try {
            spin(
                callback: function () use ($name, $email, $adminPassword): void {
                    User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($adminPassword),
                        'is_admin' => 1,
                        'email_verified_at' => now(),
                    ]);
                },
                message: 'Creating administrator account...'
            );

            $this->components->info('Administrator account created');
        } catch (\Exception $e) {
            $this->components->error('Failed to create administrator: '.$e->getMessage());
        }
    }

    // ── Organization ──────────────────────────────────────────────────────────

    protected function setupOrganization(): void
    {
        info('Organization Setup');

        note('Basic Information');

        $orgName = text(label: 'Organization name', default: config('app.name'), required: true);
        $this->settings->set('organization.name', $orgName, 'string');

        $orgEmail = text(label: 'Organization e-mail', placeholder: 'contact@example.com');
        if ($orgEmail !== '' && $orgEmail !== '0') {
            $this->settings->set('organization.email', $orgEmail, 'string');
        }

        $orgWeb = text(label: 'Organization website', placeholder: 'https://example.com');
        if ($orgWeb !== '' && $orgWeb !== '0') {
            $this->settings->set('organization.website', $orgWeb, 'string');
        }

        $languages = $this->getAvailableLanguages();

        note('Slogan (multilingual)');
        $slogans = [];
        foreach ($languages as $lang => $langName) {
            $slogan = text(label: "Slogan ({$langName})");
            if ($slogan !== '' && $slogan !== '0') {
                $slogans[$lang] = $slogan;
            }
        }
        if (! empty($slogans)) {
            $this->settings->set('organization.slogan', $slogans, 'json');
        }

        note('Description (multilingual)');
        $descriptions = [];
        foreach ($languages as $lang => $langName) {
            $description = text(label: "Description ({$langName})");
            if ($description !== '' && $description !== '0') {
                $descriptions[$lang] = $description;
            }
        }
        if (! empty($descriptions)) {
            $this->settings->set('organization.description', $descriptions, 'json');
        }

        if (confirm('Add legal / registration information?', default: true)) {
            note('Legal Information');

            foreach ([
                ['key' => 'register_id',     'label' => 'Register ID / Registration Number'],
                ['key' => 'registered_date', 'label' => 'Registration date', 'placeholder' => 'YYYY-MM-DD'],
                ['key' => 'court',           'label' => 'Responsible court'],
                ['key' => 'tax_id',          'label' => 'Tax ID'],
                ['key' => 'vat_id',          'label' => 'VAT ID'],
            ] as $field) {
                $value = text(label: $field['label'], placeholder: $field['placeholder'] ?? '');
                if ($value !== '' && $value !== '0') {
                    $this->settings->set("organization.{$field['key']}", $value, 'string');
                }
            }
        }

        $this->components->info('Organization information saved');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    protected function validateField($value, string $rules): ?string
    {
        $validator = Validator::make(['value' => $value], ['value' => $rules]);

        return $validator->fails() ? $validator->errors()->first('value') : null;
    }

    protected function getAvailableLanguages(): array
    {
        return config('app.available_locales', [
            'de' => 'German',
            'en' => 'English',
        ]);
    }

    protected function backupEnv(): bool
    {
        if (! file_exists(base_path('.env'))) {
            return copy(base_path('.env'), base_path('.env.bak'));
        }

        return false;
    }
}
