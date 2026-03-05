<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Member;

use App\Actions\Member\CreateMember;
use App\Enums\Gender;
use App\Enums\MemberFeeType;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class MemberForm extends Form
{
    public Member $member;

    /** @var int|string|null */
    public mixed $id = null;

    /** @var string|null */
    public mixed $applied_at = null;

    /** @var string|null */
    public mixed $verified_at = null;

    /** @var string|null */
    public mixed $entered_at = null;

    /** @var string|null */
    public mixed $left_at = null;

    /** @var bool|null */
    public mixed $is_deducted = null;

    /** @var string|null */
    public mixed $deduction_reason = null;

    /** @var string|null */
    public mixed $birth_date = null;

    /** @var string|null */
    public mixed $birth_place = null;

    /** @var string|null */
    public mixed $name = null;

    /** @var string|null */
    public mixed $first_name = null;

    /** @var string|null */
    public mixed $email = null;

    /** @var string|null */
    public mixed $phone = null;

    /** @var string|null */
    public mixed $mobile = null;

    /** @var string|null */
    public mixed $address = null;

    /** @var string|null */
    public mixed $zip = null;

    /** @var string|null */
    public mixed $city = null;

    /** @var string|null */
    public mixed $country = null;

    /** @var string|null */
    public mixed $citizenship = null;

    /** @var string|null */
    public mixed $family_status = null;

    /** @var string|null */
    public mixed $locale = null;

    /** @var string|null stored as enum value, e.g. 'male' */
    public mixed $gender = null;

    /** @var string|null stored as enum value, e.g. 'AP' */
    public mixed $type = null;

    /** @var string|null */
    public mixed $fee_type = null;

    /** @var int|null */
    public mixed $user_id = null;

    public string $linked_user_name = '';

    /** @var string|null */
    public mixed $gdpr_consent_at = null;

    /** @var string|null */
    public mixed $newsletter_consent_at = null;

    /** @var string|null */
    public mixed $photo_consent_at = null;

    public function set(Member $member): void
    {
        $this->member = $member;

        // Dates → string|null
        $this->applied_at = optional($member->applied_at)->format('Y-m-d');
        $this->verified_at = optional($member->verified_at)->format('Y-m-d');
        $this->entered_at = optional($member->entered_at)->format('Y-m-d');
        $this->left_at = optional($member->left_at)->format('Y-m-d');
        $this->birth_date = optional($member->birth_date)->format('Y-m-d');
        $this->gdpr_consent_at = optional($member->gdpr_consent_at)->format('Y-m-d H:i:s');
        $this->newsletter_consent_at = optional($member->newsletter_consent_at)->format('Y-m-d H:i:s');
        $this->photo_consent_at = optional($member->photo_consent_at)->format('Y-m-d H:i:s');

        // Enums → string|null (value)
        $this->gender = $member->gender->value;
        $this->type = $member->type->value;
        $this->fee_type = $member->fee_type->value;

        // Scalars
        $this->deduction_reason = $member->deduction_reason;
        $this->is_deducted = $member->is_deducted;
        $this->name = $member->name;
        $this->first_name = $member->first_name;
        $this->email = $member->email;
        $this->phone = $member->phone;
        $this->mobile = $member->mobile;
        $this->address = $member->address;
        $this->zip = $member->zip;
        $this->city = $member->city;
        $this->country = $member->country;
        $this->user_id = $member->user_id;
        $this->birth_place = $member->birth_place;
        $this->citizenship = $member->citizenship;
        $this->family_status = $member->family_status;
        $this->locale = $member->locale;
        $this->linked_user_name = $this->setUser();
    }

    public function setUser(): string
    {
        /** @var User|null $user */
        $user = User::query()->find($this->user_id);

        return $user !== null
            ? $user->first_name.' '.$user->name
            : __('members.backend.form.no-user-found');
    }

    public function updateData(): bool
    {
        // Dates: string → Carbon|null
        $this->member->entered_at = is_string($this->entered_at) && $this->entered_at !== ''
            ? Carbon::parse($this->entered_at)
            : null;

        $this->member->left_at = is_string($this->left_at) && $this->left_at !== ''
            ? Carbon::parse($this->left_at)
            : null;

        $this->member->applied_at = is_string($this->applied_at) && $this->applied_at !== ''
            ? Carbon::parse($this->applied_at)
            : Carbon::now();

        $this->member->verified_at = is_string($this->verified_at) && $this->verified_at !== ''
            ? Carbon::parse($this->verified_at)
            : null;

        $this->member->birth_date = is_string($this->birth_date) && $this->birth_date !== ''
            ? Carbon::parse($this->birth_date)
            : null;

        $this->member->gdpr_consent_at = is_string($this->gdpr_consent_at) && $this->gdpr_consent_at !== ''
            ? Carbon::parse($this->gdpr_consent_at)
            : null;

        $this->member->newsletter_consent_at = is_string($this->newsletter_consent_at) && $this->newsletter_consent_at !== ''
            ? Carbon::parse($this->newsletter_consent_at)
            : null;

        $this->member->photo_consent_at = is_string($this->photo_consent_at) && $this->photo_consent_at !== ''
            ? Carbon::parse($this->photo_consent_at)
            : null;

        // Enums: string → Enum|null
        $this->member->gender = is_string($this->gender) && $this->gender !== ''
            ? Gender::from($this->gender)
            : null;

        $this->member->type = is_string($this->type) && $this->type !== ''
            ? MemberType::from($this->type)
            : MemberType::AP;

        $this->member->fee_type = is_string($this->fee_type) && $this->fee_type !== ''
            ? MemberFeeType::from($this->fee_type)
            : MemberFeeType::FULL;

        // Scalars
        $this->member->deduction_reason = $this->deduction_reason;
        $this->member->is_deducted = $this->is_deducted;
        $this->member->name = $this->name;
        $this->member->first_name = $this->first_name;
        $this->member->email = $this->email;
        $this->member->phone = $this->phone;
        $this->member->mobile = $this->mobile;
        $this->member->address = $this->address;
        $this->member->zip = $this->zip;
        $this->member->city = $this->city;
        $this->member->country = $this->country;
        $this->member->user_id = $this->user_id;
        $this->member->birth_place = $this->birth_place;
        $this->member->citizenship = $this->citizenship;
        $this->member->family_status = $this->family_status;
        $this->member->locale = $this->locale;

        return $this->member->save();
    }

    public function create(): Member
    {
        $this->validate();

        return CreateMember::handle($this);
    }

    public function cancelMembership(): bool
    {
        $this->member->left_at = now();

        return $this->member->save();
    }

    public function reactivateMembership(): bool
    {
        $this->member->left_at = null;

        return $this->member->save();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'applied_at' => ['required', 'date'],
            'verified_at' => ['nullable', 'date'],
            'entered_at' => ['nullable', 'date'],
            'left_at' => ['nullable', 'date'],
            'is_deducted' => ['nullable', 'boolean'],
            'deduction_reason' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date'],
            'name' => ['required', 'string'],
            'first_name' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'mobile' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'zip' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'birth_place' => ['nullable', 'string'],
            'citizenship' => ['nullable', 'string'],
            'family_status' => ['nullable', 'string'],
            'locale' => [
                'nullable',
                Rule::exists('locales', 'name')->where(
                    fn (Builder $query) => $query->where('active', 1)
                ),
            ],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'type' => ['nullable', Rule::enum(MemberType::class)],
            'user_id' => ['nullable', 'exists:'.User::class.',id'],
            'gdpr_consent_at' => ['nullable', 'date'],
            'newsletter_consent_at' => ['nullable', 'date'],
            'photo_consent_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => __('members.application.errors.name-required'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toApplicationData(): array
    {
        return [
            'email' => is_string($this->email) ? $this->email : '',
            'name' => is_string($this->name) ? $this->name : '',
            'first_name' => is_string($this->first_name) ? $this->first_name : null,
            'gender' => is_string($this->gender) ? $this->gender : null,
            'birth_date' => is_string($this->birth_date) ? $this->birth_date : null,
            'birth_place' => is_string($this->birth_place) ? $this->birth_place : null,
            'locale' => is_string($this->locale) ? $this->locale : 'de',
            'address' => is_string($this->address) ? $this->address : null,
            'zip' => is_string($this->zip) ? $this->zip : null,
            'city' => is_string($this->city) ? $this->city : null,
            'country' => is_string($this->country) ? $this->country : 'Deutschland',
            'phone' => is_string($this->phone) ? $this->phone : null,
            'mobile' => is_string($this->mobile) ? $this->mobile : null,
            'family_status' => is_string($this->family_status) ? $this->family_status : null,
            'type' => is_string($this->type) ? $this->type : null,
            'is_deducted' => (bool) $this->is_deducted,
            'deduction_reason' => is_string($this->deduction_reason) ? $this->deduction_reason : null,
        ];
    }
}
