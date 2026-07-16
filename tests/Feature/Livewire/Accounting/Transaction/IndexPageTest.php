<?php

declare(strict_types=1);

use App\Livewire\Accounting\Transaction\Index\Page;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\User;
use Tests\Traits\TranslationTestTrait;

uses(TranslationTestTrait::class);

test('if backend transactions index page component renders correctly', function (): void {

    // Nutzer erstellen aus Mitglied authentifizieren
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    $fy = \App\Models\Accounting\FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transactions = \App\Models\Accounting\Transaction::factory(30)->create([
        'date' => now()->year(2025)->startOfYear(),
    ]);

    Livewire::test(Page::class, ['transactionList' => $transactions])
        ->assertSeeLivewire(Page::class) // Ensures Livewire renders
        ->assertStatus(200)
//        ->assertSee('Keine Buchungen gefunden'); // Assuming pagination is visible
        ->assertSee(Transaction::first()->label); // Check if first transaction is listed
});

test('if backend transaction pagination works correctly', function (): void {
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);
    // Nutzer erstellen aus Mitglied authentifizieren
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    $fy = \App\Models\Accounting\FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transactions = \App\Models\Accounting\Transaction::factory(30)->create([
        'date' => now()->year(2025)->startOfYear(),
    ]);

    Livewire::test(Page::class)
        ->call('gotoPage', 2)
//        ->assertSee(Transaction::skip(10)->first()->label) // Check second page content
        ->assertDontSee(Transaction::first()->label); // First page transaction should not be here
});

test('if backend transaction index page transactions can be searched', function (): void {

    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    $fy = \App\Models\Accounting\FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transactions = \App\Models\Accounting\Transaction::factory(30)->create([
        'date' => now()->year(2025)->startOfYear(),
    ]);

    Transaction::factory()->create([
        'date' => now()->year(2025)->startOfYear(),
        'label' => 'Laravel Conference',
    ]);
    Transaction::factory()->create([
        'date' => now()->year(2025)->startOfYear(),
        'label' => 'VueJS Meetup',
    ]);

    Livewire::test(Page::class)
        ->set('search', 'Laravel')
        ->assertSee('Laravel Conference')
        ->assertDontSee('VueJS Meetup');
});

test('if all translations are rendered on backend transaction index page', function (): void {
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    // Nutzer erstellen aus Mitglied authentifizieren
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    $fy = \App\Models\Accounting\FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transactions = \App\Models\Accounting\Transaction::factory(30)->create([
        'date' => now()->year(2025)->startOfYear(),
    ]);

    $this->assertTranslationsRendered(
        Page::class, [],
        'transaction',
        'transaction.',
    );
});
