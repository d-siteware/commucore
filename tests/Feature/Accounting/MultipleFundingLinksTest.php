<?php

declare(strict_types=1);

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Livewire\Accounting\Transaction\Index\Page as TransactionIndexPage;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;
use App\Models\Membership\Member;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(Member::factory()->withUser()->create([
        'user_id' => User::factory()->create(['email_verified_at' => now()])->id,
    ])->user);

    $fy = FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);
});

function multiFundingTx(): Transaction
{
    return Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
        'amount_gross' => 1_000_00,
        'date' => now()->year(2025)->startOfYear(),
    ]);
}

it('blocks a second funding link without an allocated amount', function (): void {
    $transaction = multiFundingTx();
    $fundingA = Funding::factory()->create();
    $fundingB = Funding::factory()->create();

    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $fundingA->id)
        ->set('target_funding_allocated', '600,00')
        ->call('appendFunding')
        ->assertHasNoErrors();

    // Zweite Zuordnung OHNE Teilbetrag → würde auf den vollen Bruttobetrag
    // zurückfallen (Überzählung) und wird blockiert.
    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $fundingB->id)
        ->call('appendFunding');

    expect(FundingTransaction::where('transaction_id', $transaction->id)->count())->toBe(1);
});

it('blocks a second link while an existing link has no allocated amount', function (): void {
    $transaction = multiFundingTx();
    $fundingA = Funding::factory()->create();
    $fundingB = Funding::factory()->create();

    // Erste Zuordnung ohne Teilbetrag – bei Einfach-Zuordnung zulässig
    // (null = voller Betrag).
    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $fundingA->id)
        ->call('appendFunding')
        ->assertHasNoErrors();

    // Zweite Zuordnung MIT Teilbetrag, aber die bestehende Zeile hat keinen →
    // Invariante verletzt, Block mit Hinweis.
    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $fundingB->id)
        ->set('target_funding_allocated', '400,00')
        ->call('appendFunding');

    expect(FundingTransaction::where('transaction_id', $transaction->id)->count())->toBe(1);
});

it('shows all linked fundings in the transaction index', function (): void {
    $transaction = multiFundingTx();
    $fundingA = Funding::factory()->create(['title' => 'Alpha Förderung']);
    $fundingB = Funding::factory()->create(['title' => 'Beta Förderung']);

    FundingTransaction::create([
        'funding_id' => $fundingA->id,
        'transaction_id' => $transaction->id,
        'allocated_amount' => 600_00,
    ]);
    FundingTransaction::create([
        'funding_id' => $fundingB->id,
        'transaction_id' => $transaction->id,
        'allocated_amount' => 400_00,
    ]);

    Livewire::test(TransactionIndexPage::class)
        ->assertSee('Alpha Förderung')
        ->assertSee('Beta Förderung');
});
