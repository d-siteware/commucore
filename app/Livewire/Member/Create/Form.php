<?php

declare(strict_types=1);

namespace App\Livewire\Member\Create;

use App\Enums\Gender;
use App\Enums\MemberFamilyStatus;
use App\Enums\MemberType;
use App\Livewire\Forms\Member\MemberForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Membership\Member;
use App\Models\Membership\MemberApplication;
use App\Notifications\MemberApplicationVerifyEmail;
use App\Rules\UniqueApplicantEmail;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

final class Form extends Component
{
    use HandlesErrors;
    use HasPrivileges;

    public MemberForm $form;

    public bool $isExternalMemberApplication = false;

    public mixed $turnstile = null;

    /** @var Collection<int, \App\Models\Accounting\Account> */
    public Collection $bankAccounts;

    /** @var Collection<int, \App\Models\Accounting\Account> */
    public Collection $payPalAccounts;

    public bool $nomail = false;

    public function mount(bool $isExternalMemberApplication = false): void
    {
        $this->isExternalMemberApplication = $isExternalMemberApplication;
        $this->form->locale = app()->getLocale();
        $this->form->gender = Gender::ma->value;
        $this->form->applied_at = Carbon::now('Europe/Berlin')->format('Y-m-d');
        $this->form->verified_at = Carbon::now('Europe/Berlin')->format('Y-m-d');
        $this->form->gdpr_consent_at = Carbon::now('Europe/Berlin')->format('Y-m-d');
        $this->form->newsletter_consent_at = Carbon::now('Europe/Berlin')->format('Y-m-d');
        $this->form->photo_consent_at = Carbon::now('Europe/Berlin')->format('Y-m-d');
        $this->form->entered_at = Carbon::now('Europe/Berlin')->format('Y-m-d');

        $this->form->family_status = MemberFamilyStatus::NN->value;
        $this->form->type = MemberType::AP->value;
        $this->form->country = 'Deutschland';

        if (! $isExternalMemberApplication) {
            $this->checkPrivilege(Member::class);
        }

        $this->bankAccounts = \App\Models\Accounting\Account::whereType(
            \App\Enums\AccountType::bank->value
        )
            ->get();

        $this->payPalAccounts = \App\Models\Accounting\Account::whereType(
            \App\Enums\AccountType::paypal->value
        )
            ->get();
    }

    public function checkEmail(): void
    {
        $this->nomail = $this->form->email === '' || $this->form->email === null;
    }

    public function checkBirthDate(): void
    {
        if ($this->form->birth_date === null || $this->form->birth_date === '') {
            return;
        }

        $birthDate = new Carbon($this->form->birth_date);

        if ($birthDate->diffInYears(now()) > Member::$age_discounted) {
            $this->form->is_deducted = true;
            $this->form->deduction_reason = __('members.deduction_reason', ['age' => Member::$age_discounted]);
        } else {
            $this->form->is_deducted = false;
            $this->form->deduction_reason = '';
        }
    }

    public function store(): void
    {
        try {
            $this->form->validate();

            if ($this->isExternalMemberApplication && app()->environment() !== 'testing' && config('turnstile.enabled', false)) {
                $this->validate([
                    'turnstile' => ['required', new Turnstile],
                ]);
            } else {
                $this->checkPrivilege(Member::class);
            }

            if ($this->isExternalMemberApplication) {
                $this->validate([
                    'form.email' => ['nullable', new UniqueApplicantEmail],
                ]);

                $application = MemberApplication::createFromFormData(
                    $this->form->toApplicationData()
                );

                $application->notify(new MemberApplicationVerifyEmail($application));

                Flux::toast(
                    text: __('members.apply.submission.success.text'),
                    heading: __('members.apply.submission.success.head'),
                    variant: 'success',
                );

                $this->dispatch('application-submitted');
            } else {
                $member = $this->form->create();

                Flux::toast(
                    text: __('members.create.message.success'),
                    heading: __('members.apply.submission.success.head'),
                    variant: 'success',
                );

                $this->redirect(route('backend.members.show', ['member' => $member]), true);
            }
        } catch (\Throwable $e) {
            $this->handleError('Member erstellen fehlgeschlagen', $e);
        }
    }

    public function addDummyData(): void
    {
        if (! app()->isProduction()) {
            $this->form->name = 'Doe';
            $this->form->first_name = 'John';
            $this->form->birth_date = Carbon::now('Europe/Berlin')
                ->subYears(51)
                ->format('Y-m-d');
            $this->form->gender = 'male';
            $this->form->birth_place = 'Frankfurt a. M.';
            $this->form->zip = '60311';
            $this->form->address = 'Grünspechtweg 12';
            $this->form->city = 'Hamburg';
            $this->form->country = 'Deutschland';
            $this->form->email = 'daniel@gmail.com';
            $this->form->phone = '0123456789';
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.create.form');
    }
}
