<?php

declare(strict_types=1);

namespace App\Livewire\Member\Create;

use App\Enums\Gender;
use App\Enums\MemberFamilyStatus;
use App\Enums\MemberType;
use App\Livewire\Forms\Member\MemberForm;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Membership\Member;
use App\Notifications\ApplianceReceivedNotification;
use App\Notifications\NewMemberApplied;
use Carbon\Carbon;
use Faker\Provider\de_DE\Address;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

final class Form extends Component
{
    use HasPrivileges;

    public MemberForm $form;

    public ?bool $isExternalMemberApplication = null;

    public $turnstile;

    public Collection $bankAccounts;

    public Collection $payPalAccounts;

    public $nomail;

    public function mount(bool $isExternalMemberApplication = false): void
    {
        $this->form->locale = app()->getLocale();
        $this->form->gender = Gender::ma;
        $this->form->applied_at = Carbon::now('Europe/Berlin');
        $this->form->family_status = MemberFamilyStatus::NN->value;
        $this->form->type = MemberType::AP->value;
        $this->form->country = 'Deutschland';

        if (! $isExternalMemberApplication) {
            $this->checkPrivilege(Member::class);
        }
        $this->bankAccounts = \App\Models\Accounting\Account::whereType(\App\Enums\AccountType::bank->value)->get();
        $this->payPalAccounts = \App\Models\Accounting\Account::whereType(\App\Enums\AccountType::paypal->value)->get();
    }

    public function checkEmail(): void
    {

        $this->nomail = $this->form->email === '';

    }

    public function checkBirthDate(): void
    {
        $birthDate = new Carbon($this->form->birth_date);

        if ($birthDate->diffInYears(now()) > Member::$age_discounted) {
            $this->form->is_deducted = true;
            $this->form->deduction_reason = 'Älter als '.Member::$age_discounted;
        } else {
            $this->form->is_deducted = false;
            $this->form->deduction_reason = '';
        }
    }

    protected function printApplication(Member $member): \Illuminate\Http\RedirectResponse
    {

        return redirect(route('members.print_isExternalMemberApplication', ['member' => $member]));

    }

    protected function sendApplication(): \Illuminate\Http\RedirectResponse
    {
        return redirect(route('home'));
    }

    public function store(): void
    {
        $this->form->validate();

        /**
         *  If the store method is called from the external member isExternalMemberApplication
         *  page then $this->isExternalMemberApplication is set true
         *
         * TODO: fake Turstile pass test in testing enviornment
         */
        if ($this->isExternalMemberApplication && app()->environment() !== 'testing') {
            $this->validate([
                'turnstile' => ['required', new Turnstile],
            ]);
        } else {
            $this->checkPrivilege(Member::class);
        }

        if ($this->isExternalMemberApplication) {
            $this->form->applied_at = Carbon::now('Europe/Berlin');

            $member = $this->form->create();

            Notification::send(Member::getBoardMembers(), new NewMemberApplied($member));

            Notification::send($member, new ApplianceReceivedNotification($member));

            if ($this->nomail) {
                $this->printApplication($member);
            }

            Flux::toast(
                text: __('members.apply.submission.success.msg'),
                heading: __('members.apply.submission.success.head'),
                variant: 'success',
            );

        } else {

            $member = $this->form->create();

            Flux::toast(
                text: __('members.create.message.success'),
                heading: __('members.apply.submission.success.head'),
                variant: 'success',
            );

            $this->redirect(route('backend.members.show', ['member' => $member]), true);
        }

    }

    public function addDummyData()
    {
        if (! app()->isProduction()) {

            $this->form->name = 'Doe';
            $this->form->first_name = 'John';
            $this->form->birth_date = Carbon::now('Europe/Berlin')->subYears(51);
            $this->form->gender = 'male';
            $this->form->birth_place = 'Frankfurt a. M.';
            $this->form->zip = Address::postcode();
            $this->form->address = 'Grünspowhczo 12';
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
