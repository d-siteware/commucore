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
        ->toBe(\App\Enums\Gender::ma->value)
        ->and($component->form->family_status)
        ->toBe(\App\Enums\MemberFamilyStatus::NN->value)
        ->and($component->form->type)
        ->toBe(\App\Enums\MemberType::AP->value)
        ->and($component->form->country)
        ->toBe('Deutschland')
        ->and($component->form->applied_at)
        ->toBeString();
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
    expect($component->form->deduction_reason)->toBe("Älter als $ageDiscounted Jahre");

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
    expect($component->form->deduction_reason)->toBe("Älter als $ageDiscounted Jahre");

    $component->set('form.birth_date', now()
        ->subYears($ageDiscounted - 1)
        ->toDateString())
        ->call('checkBirthDate');

    expect($component->form->is_deducted)->toBeFalse();
    expect($component->form->deduction_reason)->toBe('');
});

test('Non useres can apply as member and recieve notifications after creation', function (): void {

    Notification::fake();

    \App\Models\Locale::create(['name' => 'de', 'label' => 'Deutsch', 'active' => true]);

    $user = User::factory()->admin()->create();

    $boardMember = Member::factory()
        ->create([
            'type' => \App\Enums\MemberType::MD,
            'name' => 'Board Guy',
            'email' => 'board@example.com',
            'user_id' => $user->id,
        ]);

    $this->actingAs($user);

    $component = Livewire::test(Form::class, ['isExternalMemberApplication' => true])
        ->set('isExternalMemberApplication', true)
        ->set('form.name', 'John Doe')
        ->set('form.locale', 'de')
        ->set('form.email', 'john@example.com')
        ->set('form.gender', \App\Enums\Gender::ma)
        ->set('form.birth_date', now()
            ->subYears(30)
            ->toDateString())
        ->set('form.family_status', \App\Enums\MemberFamilyStatus::NN)
        ->set('form.type', \App\Enums\MemberType::AP)
        ->set('form.country', 'Deutschland')
        ->call('store')
        ->assertHasNoErrors();

    $member = \App\Models\Membership\MemberApplication::where('email', 'john@example.com')
        ->first();

    expect($member)->not->toBeNull()
        ->and($member->name)
        ->toBe('John Doe')
        ->and($member->email)
        ->toBe('john@example.com');

    Notification::assertSentTo($member, \App\Notifications\MemberApplicationVerifyEmail::class);

});
test('Member application is confirmed after email verification', function (): void {

    Notification::fake();

    \App\Models\Locale::create(['name' => 'de', 'label' => 'Deutsch', 'active' => true]);

    $user = User::factory()->admin()->create();

    $boardMember = Member::factory()->create([
        'type' => \App\Enums\MemberType::MD,
        'email' => 'board@example.com',
        'user_id' => $user->id,
    ]);

    $application = \App\Models\Membership\MemberApplication::create([
        'token' => \Illuminate\Support\Str::random(64),
        'email' => 'john@example.com',
        'name' => 'Doe',
        'first_name' => 'John',
        'locale' => 'de',
        'country' => 'Deutschland',
        'applied_at' => now(),
        'expires_at' => now()->addHours(48),
    ]);

    Livewire::test(\App\Livewire\Member\Apply\Page::class)
        ->set('application', $application)
        ->set('step', 'verify')
        ->set('gdpr_consent', true)
        ->set('newsletter_consent', false)
        ->set('photo_consent', false)
        ->call('confirmConsents')
        ->assertSet('step', 'done');

    // Kein Member erstellt — nur Application verifiziert
    $this->assertDatabaseMissing('members', ['email' => 'john@example.com']);

    $this->assertDatabaseHas('member_applications', [
        'email' => 'john@example.com',
        'verified_at' => now()->toDateTimeString(),
    ]);

    Notification::assertSentTo(
        Member::find($boardMember->id),
        \App\Notifications\NewMemberAppliedNotification::class
    );
});

test('store creates member without application with authorization', function (): void {
    $adminUser = User::factory()
        ->create([
            'is_admin' => true,
        ]);

    \App\Models\Locale::create(['name' => 'de', 'label' => 'Deutsch', 'active' => true]);

    $this->actingAs($adminUser);
    $component = Livewire::test(Form::class, ['isExternalMemberApplication' => false])
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
        ->call('store')
        ->assertOk()
        ->assertHasNoErrors();

    $member = \App\Models\Membership\Member::latest()
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

test('Duplicate email in members table is rejected', function (): void {

    Notification::fake();

    \App\Models\Locale::create(['name' => 'de', 'label' => 'Deutsch', 'active' => true]);

    Member::factory()->create(['email' => 'existing@example.com']);

    Livewire::test(Form::class, ['isExternalMemberApplication' => true])
        ->set('form.name', 'John Doe')
        ->set('form.locale', 'de')
        ->set('form.email', 'existing@example.com')
        ->set('form.gender', \App\Enums\Gender::ma)
        ->set('form.family_status', \App\Enums\MemberFamilyStatus::NN)
        ->set('form.type', \App\Enums\MemberType::AP)
        ->set('form.country', 'Deutschland')
        ->call('store')
        ->assertHasErrors(['form.email']);

    $this->assertDatabaseMissing('member_applications', ['email' => 'existing@example.com']);
});

test('Duplicate email in open member_applications is rejected', function (): void {

    Notification::fake();

    \App\Models\Locale::create(['name' => 'de', 'label' => 'Deutsch', 'active' => true]);

    \App\Models\Membership\MemberApplication::create([
        'token' => \Illuminate\Support\Str::random(64),
        'email' => 'pending@example.com',
        'name' => 'Existing Applicant',
        'locale' => 'de',
        'country' => 'Deutschland',
        'applied_at' => now(),
        'expires_at' => now()->addHours(48),
    ]);

    Livewire::test(Form::class, ['isExternalMemberApplication' => true])
        ->set('form.name', 'John Doe')
        ->set('form.locale', 'de')
        ->set('form.email', 'pending@example.com')
        ->set('form.gender', \App\Enums\Gender::ma)
        ->set('form.family_status', \App\Enums\MemberFamilyStatus::NN)
        ->set('form.type', \App\Enums\MemberType::AP)
        ->set('form.country', 'Deutschland')
        ->call('store')
        ->assertHasErrors(['form.email']);

    // Nur ein Eintrag in der DB — der ursprüngliche
    $this->assertDatabaseCount('member_applications', 1);
});

test('Member receives accepted notification when entered_at is set', function (): void {

    Notification::fake();

    $member = Member::factory()->create([
        'email' => 'member@example.com',
        'entered_at' => null,
    ]);

    $member->entered_at = now();
    $member->save();

    Notification::assertSentTo($member, \App\Notifications\MemberAcceptedNotification::class);
});
test('Expired unverified applications are pruned', function (): void {

    // Abgelaufen + unverifiziert → soll gelöscht werden
    \App\Models\Membership\MemberApplication::create([
        'token' => \Illuminate\Support\Str::random(64),
        'email' => 'expired@example.com',
        'name' => 'Expired',
        'locale' => 'de',
        'country' => 'Deutschland',
        'applied_at' => now()->subDays(3),
        'expires_at' => now()->subDay(),
    ]);

    // Abgelaufen + verifiziert → soll NICHT gelöscht werden
    \App\Models\Membership\MemberApplication::create([
        'token' => \Illuminate\Support\Str::random(64),
        'email' => 'verified@example.com',
        'name' => 'Verified',
        'locale' => 'de',
        'country' => 'Deutschland',
        'applied_at' => now()->subDays(3),
        'expires_at' => now()->subDay(),
        'verified_at' => now()->subDays(2),
    ]);

    // Noch gültig + unverifiziert → soll NICHT gelöscht werden
    \App\Models\Membership\MemberApplication::create([
        'token' => \Illuminate\Support\Str::random(64),
        'email' => 'pending@example.com',
        'name' => 'Pending',
        'locale' => 'de',
        'country' => 'Deutschland',
        'applied_at' => now(),
        'expires_at' => now()->addHours(48),
    ]);

    $this->artisan('members:prune-applications')
        ->expectsOutput('Gelöscht: 1 abgelaufene Bewerbungen.')
        ->assertExitCode(0);

    $this->assertDatabaseMissing('member_applications', ['email' => 'expired@example.com']);
    $this->assertDatabaseHas('member_applications', ['email' => 'verified@example.com']);
    $this->assertDatabaseHas('member_applications', ['email' => 'pending@example.com']);
});
