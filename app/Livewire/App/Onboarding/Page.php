<?php

namespace App\Livewire\App\Onboarding;

use App\Enums\MemberFeeType;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
class Page extends Component
{
    public int $step = 1;

    public int $totalSteps = 4;

    // Step 1 – Organisation
    #[Validate('required|string|max:255')]
    public string $org_name = '';

    #[Validate('nullable|email')]
    public string $org_email = '';

    #[Validate('nullable|url')]
    public string $org_web = '';

    #[Validate('nullable|string')]
    public string $org_address = '';

    #[Validate('nullable|string|max:10')]
    public string $org_zip = '';

    #[Validate('nullable|string')]
    public string $org_city = '';

    // Step 1 – Rechtliches
    #[Validate('nullable|string')]
    public string $org_register_id = '';

    #[Validate('nullable|date')]
    public string $org_registered_date = '';

    #[Validate('nullable|string')]
    public string $org_court = '';

    #[Validate('nullable|string')]
    public string $org_tax_id = '';

    #[Validate('nullable|string')]
    public string $org_vat_id = '';

    // Step 2 – Einstellungen
    #[Validate('required|integer|min:2000|max:2100')]
    public int $fiscal_year;

    public array $active_locales = ['de'];

    // Step 3 – Team
    #[Validate('required|string|max:255')]
    public string $user_name = '';

    #[Validate('required|email')]
    public string $user_email = '';

    #[Validate('nullable|string|max:255')]
    public string $user_first_name = '';

    #[Validate('nullable|string|max:255')]
    public string $user_username = '';

    // invites als Array of arrays
    public array $invites = [['name' => '', 'first_name' => '', 'email' => '', 'username' => '']];

    public bool $smtpConfigured = false;

    public function mount(): void
    {
        /**
         * Load default settings from config/branding.php
         */
        $this->org_name = setting('organization.name', '');
        $this->org_email = setting('organization.email', '');
        $this->org_web = setting('organization.web', '');
        $this->org_address = setting('organization.address', '');
        $this->org_zip = setting('organization.zip', '');
        $this->org_city = setting('organization.city', '');
        $this->org_register_id = setting('organization.register_id', '');
        $this->org_registered_date = setting('organization.registered_date', '');
        $this->org_court = setting('organization.court', '');
        $this->org_tax_id = setting('organization.tax_id', '');
        $this->org_vat_id = setting('organization.vat_id', '');

        /**
         *  Set FiscalYear
         */
        $this->fiscal_year = (int) date('Y');

        /**
         *  Prefill User Name
         */
        $this->user_name = Auth::user()->name ?? '';
        $this->user_first_name = Auth::user()->first_name ?? '';
        $this->user_email = Auth::user()->email ?? '';
        $this->user_username = Auth::user()->username ?? '';

        $this->smtpConfigured = config('mail.default') === 'smtp';
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->step) {
            $this->step = $step;
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validateOnly('org_name');
        }

        if ($this->step === 2) {
            $this->validateOnly('fiscal_year');
            $this->validate([
                'active_locales' => 'required|array|min:1',
            ],
            [
                'active_locales.required' => 'Es muss mindestens eine Sprache ausgewählt werden.',
                'active_locales.min' => 'Es muss mindestens eine Sprache ausgewählt werden.',
            ]);
        }

        if ($this->step === 3 && count($this->invites) > 1) {
            $this->validate([
                'invites.*.name'       => 'required|string|max:255',
                'invites.*.first_name' => 'nullable|string|max:255',
                'invites.*.email'      => 'required|email',
            ]);
        }

        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function addInvite(): void
    {
        $this->validate([
            'invites.*.name'       => 'required|string|max:255',
            'invites.*.first_name' => 'nullable|string|max:255',
            'invites.*.email'      => 'required|email',
        ]);
        $this->invites[] = ['name' => '', 'first_name' => '', 'email' => ''];
    }

    public function removeInvite(int $index): void
    {
        unset($this->invites[$index]);
        $this->invites = array_values($this->invites);
    }

    public function finish(): void
    {
        $settings = app(SettingsService::class);


        $this->setProfile();

        $settings->setMany([
            'organization.name'            => $this->org_name,
            'organization.email'           => $this->org_email,
            'organization.web'             => $this->org_web,
            'organization.address'         => $this->org_address,
            'organization.zip'             => $this->org_zip,
            'organization.city'            => $this->org_city,
            'organization.register_id'     => $this->org_register_id,
            'organization.registered_date' => $this->org_registered_date,
            'organization.court'           => $this->org_court,
            'organization.tax_id'          => $this->org_tax_id,
            'organization.vat_id'          => $this->org_vat_id,
        ]);

        // Einladungen versenden
        $validInvites = array_filter($this->invites, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));
        foreach ($validInvites as $email) {
            // TODO: Invitation Mail
        }

        // onboarded_at via API auf app.commucore setzen
        $subdomain = config('commucore.subdomain');
        // TODO: HTTP-Call oder Artisan-Command

        $this->redirect('/backend/dashboard');
    }

    protected function setProfile(): void
    {
        Auth::user()
            ->update([
                'name'       => $this->user_name,
                'first_name' => $this->user_first_name,
            ]);

        if (Member::where('user_id', Auth::id())
            ->doesntExist()) {
            Member::create([
                'applied_at' => now(),
                'user_id'    => Auth::id(),
                'email'      => Auth::user()->email,
                'name'       => Auth::user()->name,
                'first_name' => Auth::user()->first_name,
                'type'       => MemberType::MD,
                'fee_type'   => MemberFeeType::FULL,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.app.onboarding.page');
    }
}
