<?php

declare(strict_types=1);

test('A list of applicant can be seen', function (): void {
    $applicants = \App\Models\Membership\MemberApplication::create([
        'token' => \Illuminate\Support\Str::random(10),
        'email' => 'hello@example.com',
        'name' => 'tester',
        'verified_at' => now(),
        'gdpr_consent_at' => now(),
        'applied_at' => now(),

    ]);

    $component = Livewire::test(\App\Livewire\Dashboard\Widgets\Applicants::class)
        ->assertOk();

});

test('all translations are rendered', function (): void {

    $user = \App\Models\User::factory()
        ->create(['is_admin' => true]);
    $this->actingAs($user);

    $keys = [];
    $prefix = 'members.';
    foreach (\App\Enums\Locale::cases() as $locale) {
        $translations = require "lang/{$locale->value}/members.php";
        $keys = array_merge($keys, array_keys(Arr::dot($translations, $prefix)));
    }

    $component = Livewire::test(\App\Livewire\Dashboard\Widgets\Applicants::class);

    foreach ($keys as $key) {
        if ($key !== $prefix) {
            $component->assertDontSee($key);
        }
    }

});
