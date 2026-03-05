<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Membership\Member;
use App\Models\Membership\MemberApplication;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class UniqueApplicantEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        if (Member::query()->where('email', $value)->exists()) {
            $fail(__('members.apply.validation.email.already_member'));

            return;
        }

        if (MemberApplication::query()
            ->where('email', $value)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->exists()
        ) {
            $fail(__('members.apply.validation.email.application_pending'));
        }
    }
}
