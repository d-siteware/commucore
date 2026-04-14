<?php

declare(strict_types=1);

namespace App\Actions\Member;

use App\Enums\MemberType;
use App\Livewire\Forms\Member\MemberForm;
use App\Models\Membership\Member;
use Illuminate\Support\Facades\DB;

final class CreateMember
{
    public static function handle(MemberForm $form): Member
    {

        return DB::transaction(function () use ($form) {
            return Member::create([
                'applied_at' => $form->applied_at,
                'verified_at' => $form->verified_at,
                'entered_at' => $form->entered_at,
                'left_at' => $form->left_at,
                'is_deducted' => isset($form->is_deducted),
                'deduction_reason' => $form->deduction_reason,
                'birth_date' => $form->birth_date,
                'birth_place' => $form->birth_place,
                'name' => $form->name,
                'first_name' => $form->first_name,
                'email' => $form->email,
                'phone' => $form->phone,
                'mobile' => $form->mobile,
                'address' => $form->address,
                'zip' => $form->zip,
                'city' => $form->city,
                'country' => $form->country,
                'citizenship' => $form->citizenship,
                'family_status' => $form->family_status,
                'locale' => $form->locale,
                'gender' => $form->gender,
                'type' => $form->type ?? MemberType::AP->value,
                'user_id' => $form->user_id,
                'gdpr_consent_at' => $form->gdpr_consent_at,
                'newsletter_consent_at' => $form->newsletter_consent_at,
                'photo_consent_at' => $form->photo_consent_at,
            ]);
        });
    }
}
