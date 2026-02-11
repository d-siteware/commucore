<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CommuCoreInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commucore:install
           {--skip-admin : Skip admin user creation}
           {--skip-organization : Skip organization setup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initial setup wizard for CommuCore installation';

    protected SettingsService $settings;

    /**
     * Execute the console command.
     */
    public function handle(SettingsService $settings): int
    {
        $this->settings = $settings;
        $this->displayWelcome();

        // Run migrations
        if ($this->confirm('Run database migrations?', true)) {
            $this->info('Running migrations...');
            Artisan::call('migrate', ['--force' => true]);
            $this->info('✓ Migrations completed');
        }

        // Setup admin user
        if (!$this->option('skip-admin')) {
            $this->newLine();
            $this->setupAdminUser();
        }

        // Setup organization
        if (!$this->option('skip-organization')) {
            $this->newLine();
            $this->setupOrganization();
        }

        $this->displayCompletion();

        return 0;
    }

    protected function displayWelcome(): void
    {
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║                                                            ║');
        $this->info('║              CommuCore Installation Wizard                 ║');
        $this->info('║                                                            ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();
        $this->line('This wizard will guide you through the initial setup of your');
        $this->line('CommuCore instance.');
        $this->newLine();
    }

    protected function setupAdminUser(): void
    {
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('  Administrator Account Setup');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->newLine();

        // Check if admin already exists
        if (User::where('is_admin', 1)->exists()) {
            $this->warn('An administrator account already exists.');
            if (!$this->confirm('Do you want to create another administrator?', false)) {
                return;
            }
        }

        $name = $this->askWithValidation(
            'Administrator name',
            'required|string|max:255'
        );

        $email = $this->askWithValidation(
            'Administrator email',
            'required|email|unique:users,email'
        );

        $password = $this->secretWithValidation(
            'Administrator password',
            'required|min:8'
        );

        $passwordConfirm = $this->secret('Confirm password');

        while ($password !== $passwordConfirm) {
            $this->error('Passwords do not match. Please try again.');
            $password = $this->secretWithValidation(
                'Administrator password',
                'required|min:8'
            );
            $passwordConfirm = $this->secret('Confirm password');
        }

        try {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => 1,
                'email_verified_at' => now(),
            ]);

            $this->info('✓ Administrator account created successfully');
        } catch (\Exception $e) {
            $this->error('Failed to create administrator account: ' . $e->getMessage());
        }
    }

    protected function setupOrganization(): void
    {
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('  Organization Information Setup');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->newLine();

        // Basic Information
        $this->comment('Basic Information:');
        $this->newLine();

        $orgName = $this->ask('Organization name', config('app.name'));
        $this->settings->set('organization.name', $orgName, 'string');

        $orgEmail = $this->ask('Organization email');
        if ($orgEmail) {
            $this->settings->set('organization.email', $orgEmail, 'string');
        }

        $orgWeb = $this->ask('Organization website');
        if ($orgWeb) {
            $this->settings->set('organization.website', $orgWeb, 'string');
        }

        // Multilingual Slogan
        $this->newLine();
        $this->comment('Organization Slogan (multilingual):');
        $this->newLine();

        $languages = $this->getAvailableLanguages();
        $slogans = [];

        foreach ($languages as $lang => $langName) {
            $slogan = $this->ask("Slogan ({$langName})");
            if ($slogan) {
                $slogans[$lang] = $slogan;
            }
        }

        if (!empty($slogans)) {
            $this->settings->set('organization.slogan', $slogans, 'json');
        }

        // Multilingual Description
        $this->newLine();
        $this->comment('Organization Description (multilingual):');
        $this->newLine();

        $descriptions = [];
        foreach ($languages as $lang => $langName) {
            $description = $this->ask("Description ({$langName})");
            if ($description) {
                $descriptions[$lang] = $description;
            }
        }

        if (!empty($descriptions)) {
            $this->settings->set('organization.description', $descriptions, 'json');
        }

        // Legal Information
        if ($this->confirm('Do you want to add legal/registration information?', true)) {
            $this->newLine();
            $this->comment('Legal Information:');
            $this->newLine();

            $registerId = $this->ask('Register ID / Registration Number');
            if ($registerId) {
                $this->settings->set('organization.register_id', $registerId, 'string');
            }

            $registeredDate = $this->ask('Registration Date (YYYY-MM-DD)');
            if ($registeredDate) {
                $this->settings->set('organization.registered_date', $registeredDate, 'string');
            }

            $court = $this->ask('Responsible Court');
            if ($court) {
                $this->settings->set('organization.court', $court, 'string');
            }

            $taxId = $this->ask('Tax ID');
            if ($taxId) {
                $this->settings->set('organization.tax_id', $taxId, 'string');
            }

            $vatId = $this->ask('VAT ID');
            if ($vatId) {
                $this->settings->set('organization.vat_id', $vatId, 'string');
            }
        }

        $this->newLine();
        $this->info('✓ Organization information saved successfully');
    }

    protected function displayCompletion(): void
    {
        $this->newLine(2);
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║                                                            ║');
        $this->info('║              Installation Completed Successfully!         ║');
        $this->info('║                                                            ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();
        $this->line('Your CommuCore instance is now ready to use.');
        $this->line('You can further customize your organization settings in the');
        $this->line('admin panel under Settings > Branding.');
        $this->newLine();
    }

    protected function askWithValidation(string $question, string $rules, $default = null): string
    {
        $value = $this->ask($question, $default);

        while (!$this->validate($value, $rules)) {
            $value = $this->ask($question, $default);
        }

        return $value;
    }

    protected function secretWithValidation(string $question, string $rules): string
    {
        $value = $this->secret($question);

        while (!$this->validate($value, $rules)) {
            $value = $this->secret($question);
        }

        return $value;
    }

    protected function validate($value, string $rules): bool
    {
        $validator = Validator::make(
            ['value' => $value],
            ['value' => $rules]
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first('value'));
            return false;
        }

        return true;
    }

    protected function getAvailableLanguages(): array
    {
        return config('app.available_locales', [
            'de' => 'German',
            'en' => 'English',
        ]);
    }
}