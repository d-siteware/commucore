<?php

declare(strict_types=1);

use App\Enums\TransactionStatus;
use App\Livewire\Accounting\Transaction\Index\Page;
use App\Models\Accounting\Account;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Traits\TranslationTestTrait;

uses(TranslationTestTrait::class);

test('if backend transactions index page component renders correctly', function (): void {

    // Nutzer erstellen aus Mitglied authentifizieren
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    $fy = FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transactions = Transaction::factory(30)->create([
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

    $fy = FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transactions = Transaction::factory(30)->create([
        'date' => now()->year(2025)->startOfYear(),
    ]);

    Livewire::test(Page::class)
        ->call('gotoPage', 2)
//        ->assertSee(Transaction::skip(10)->first()->label) // Check second page content
        ->assertDontSee(Transaction::first()->label); // First page transaction should not be here
});

test('if backend transaction index page transactions can be searched', function (): void {

    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    $fy = FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transactions = Transaction::factory(30)->create([
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

    $fy = FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transactions = Transaction::factory(30)->create([
        'date' => now()->year(2025)->startOfYear(),
    ]);

    $this->assertTranslationsRendered(
        Page::class, [],
        'transaction',
        'transaction.',
    );
});

test('transaction index action arguments do not serialize encrypted account fields', function (): void {
    $member = Member::factory()->withAdminUser()->create();
    $this->actingAs($member->user);

    $fiscalYear = FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fiscalYear->id]);

    $transaction = Transaction::factory()->create([
        'date' => now()->year(2025)->startOfYear(),
        'status' => TransactionStatus::submitted,
    ]);

    DB::table('accounts')->where('id', $transaction->account_id)->update([
        'iban' => 'legacy-plaintext-iban',
        'bic' => '',
    ]);

    Livewire::test(Page::class)
        ->assertStatus(200)
        ->assertSee('confirmTransactionDeletion('.$transaction->id.')');

    Livewire::test(Page::class)
        ->call('confirmTransactionDeletion', $transaction->id);

    expect(Transaction::whereKey($transaction->id)->exists())->toBeFalse();
});

test('a transaction in a closed fiscal year cannot be deleted through the index action', function (): void {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $account = Account::factory()->create();
    $fiscalYear = FiscalYear::factory()->create([
        'year' => 2024,
        'opened_at' => '2024-01-01',
        'closed_at' => '2024-12-31',
    ]);
    $transaction = Transaction::factory()->create([
        'account_id' => $account->id,
        'date' => '2024-06-01',
        'fiscal_year_id' => $fiscalYear->id,
        'status' => TransactionStatus::submitted,
    ]);

    Livewire::test(Page::class)
        ->call('deleteTransaction', $transaction->id)
        ->assertForbidden();

    expect(Transaction::whereKey($transaction->id)->exists())->toBeTrue();
});
