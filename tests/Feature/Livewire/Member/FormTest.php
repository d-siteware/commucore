<?php

declare(strict_types=1);

use App\Livewire\Member\Create\Form;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Traits\TranslationTestTrait;

uses(TranslationTestTrait::class);

// uses(RefreshDatabase::class);

test('defaults are set on mount', function (): void {
    $user = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user;
    $this->actingAs($user);

    $component = Livewire::test(Form::class);

    expect($component->form->locale)
        ->toBe(app()->getLocale())
        ->and($component->form->gender)
        ->toBe(\App\Enums\Gender::ma)
        ->and($component->form->family_status)
        ->toBe(\App\Enums\MemberFamilyStatus::NN->value)
        ->and($component->form->type)
        ->toBe(\App\Enums\MemberType::AP->value)
        ->and($component->form->country)
        ->toBe('Deutschland')
        ->and($component->form->applied_at)
        ->toBeInstanceOf(\Carbon\Carbon::class);
});

test('checkEmail sets nomail correctly', function (): void {
    $user = User::factory()
        ->create(['is_admin' => true]);
    $this->actingAs($user);

    $component = Livewire::test(Form::class)
        ->set('form.email', '') // Empty email
        ->call('checkEmail');

    expect($component->nomail)->toBeTrue();

    $component->set('form.email', 'test@example.com')
        ->call('checkEmail');

    expect($component->nomail)->toBeFalse();
});

test('checkBirthDate sets deduction based on age', function (): void {
    $user = User::factory()
        ->create(['is_admin' => true]);
    $this->actingAs($user);

    $ageDiscounted = Member::$age_discounted; // e.g., 65

    // Older than threshold
    $oldDate = now()
        ->subYears($ageDiscounted + 1)
        ->toDateString();
    $component = Livewire::test(Form::class)
        ->set('form.birth_date', $oldDate)
        ->call('checkBirthDate');

    expect($component->form->is_deducted)->toBeTrue();
    expect($component->form->deduction_reason)->toBe("Älter als $ageDiscounted");

    // Younger than threshold
    $youngDate = now()
        ->subYears($ageDiscounted - 1)
        ->toDateString();
    $component->set('form.birth_date', $youngDate)
        ->call('checkBirthDate');

    expect($component->form->is_deducted)->toBeFalse();
    expect($component->form->deduction_reason)->toBe('');
});

test('checkBirthDate applies deduction for older members', function (): void {
    $user = User::factory()
        ->create(['is_admin' => true]);
    $this->actingAs($user);

    $ageDiscounted = Member::$age_discounted;

    $component = Livewire::test(Form::class)
        ->set('form.birth_date', now()
            ->subYears($ageDiscounted + 1)
            ->toDateString())
        ->call('checkBirthDate');

    expect($component->form->is_deducted)->toBeTrue();
    expect($component->form->deduction_reason)->toBe("Älter als $ageDiscounted");

    $component->set('form.birth_date', now()
        ->subYears($ageDiscounted - 1)
        ->toDateString())
        ->call('checkBirthDate');

    expect($component->form->is_deducted)->toBeFalse();
    expect($component->form->deduction_reason)->toBe('');
});

test('Non useres can apply as member and recieve notifications after creation', function (): void {

    Notification::fake();

    $boardMember = Member::factory()
        ->create([
            'type' => \App\Enums\MemberType::MD,
            'name' => 'Board Guy',
            'email' => 'board@example.com',
        ]);

    $component = Livewire::test(Form::class, ['isExternalMemberApplication' => true])
        ->set('isExternalMemberApplication', true)
        ->set('form.name', 'John Doe')
        ->set('form.email', 'john@example.com')
        ->set('form.gender', \App\Enums\Gender::ma)
        ->set('form.birth_date', now()
            ->subYears(30)
            ->toDateString())
        ->set('form.family_status', \App\Enums\MemberFamilyStatus::NN)
        ->set('form.type', \App\Enums\MemberType::AP)
        ->set('form.country', 'Deutschland')
        ->call('store');

    $member = Member::where('email', 'john@example.com')
        ->first();

    expect($member)->not->toBeNull()
        ->and($member->name)
        ->toBe('John Doe')
        ->and($member->email)
        ->toBe('john@example.com');

    // Re-fetch to match Livewire’s hydrated instance
    $boardMemberFresh = Member::find($boardMember->id);

    Notification::assertSentTo($boardMemberFresh, \App\Notifications\NewMemberApplied::class);

    Notification::assertSentTo($member, \App\Notifications\ApplianceReceivedNotification::class);
    $this->assertDatabaseHas('members', ['email' => 'john@example.com']);
    //    $component->assertDispatched('toast-show', function ($event, $payload) {
    //        return $payload['dataset']['variant'] === 'success' && $payload['slots']['text'] === __('members.apply.submission.success.msg') && $payload['slots']['heading'] === __('members.apply.submission.success.head');
    //    });
});

test('store creates member without application with authorization', function (): void {
    $adminUser = User::factory()
        ->create([
            'is_admin' => true,
        ]);

    //    \Illuminate\Support\Facades\Gate::define('create', fn ($adminUser, $class) => true);

    $component = Livewire::test(Form::class, ['isExternalMemberApplication' => false, 'user' => $adminUser])
        ->set('isExternalMemberApplication', false)
        ->set('form.name', 'Jane Doe')
        ->set('form.email', 'jane@example.com')
        ->set('form.gender', \App\Enums\Gender::ma)
        ->set('form.birth_date', now()
            ->subYears(30)
            ->toDateString())
        ->set('form.family_status', \App\Enums\MemberFamilyStatus::NN)
        ->set('form.type', \App\Enums\MemberType::MD)
        ->set('form.country', 'Deutschland')
        ->call('store');

    $member = Member::latest()
        ->first();

    expect($member)->not->toBeNull();
    expect($member->name)->toBe('Jane Doe');

    //    $component->assertDispatched('toast-show', function ($event, $payload) {
    //        return $payload['dataset']['variant'] === 'success' && $payload['slots']['text'] === __('members.apply.submission.success.msg') && $payload['slots']['heading'] === __('members.apply.submission.success.head');
    //    });
    $component->assertRedirect(route('backend.members.show', ['member' => $member]));
});

test('all translations are rendered', function (): void {
    $user = \App\Models\User::factory()
        ->create(['is_admin' => true]);
    $this->actingAs($user);

    $member = Member::factory()->create(['user_id' => $user->id]);

    $this->assertTranslationsRendered(
        Form::class,
        [],
        'members',
        'members.',
    );
});
