<?php

declare(strict_types=1);

use App\Enums\MemberFamilyStatus;
use App\Enums\MemberType;

test('any visitor can apply as member', function (): void {

    Livewire::test(\App\Livewire\Member\Apply\Page::class)->assertOk();

    Livewire::test(\App\Livewire\Member\Create\Form::class, [
        'locale' => 'de',
        'gender' => \App\Enums\Gender::ma,
        'applied_at' => \Carbon\Carbon::now()->format('Y-m-d'),
        'family_status' => MemberFamilyStatus::NN->value,
        'type' => MemberType::AP->value,
        'country' => 'de',
    ])->assertOk();

});
