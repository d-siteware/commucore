<?php

declare(strict_types=1);

use App\Actions\Accounting\AppendFundingTransaction;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Livewire\Accounting\Funding\Show\Page as FundingShowPage;
use App\Livewire\Accounting\Transaction\Index\Page as TransactionIndexPage;
use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingPosition;
use App\Models\Funding\FundingPositionCategory;
use App\Models\Funding\FundingTransaction;
use App\Models\User;
use App\Services\ProjectFundingReportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

// -----------------------------------------------------------------------------
// Kategorie-Taxonomie: Seed-Idempotenz + Guards
// -----------------------------------------------------------------------------

it('seeds system categories via data migration and keeps the seeder idempotent', function (): void {
    // Data-Migration lief mit RefreshDatabase bereits → System-Default existiert.
    expect(FundingPositionCategory::system()->count())->toBe(6)
        ->and(FundingPositionCategory::where('slug', 'personalkosten')->exists())->toBeTrue();

    (new \Database\Seeders\FundingPositionCategorySeeder)->run();
    (new \Database\Seeders\FundingPositionCategorySeeder)->run();

    expect(FundingPositionCategory::system()->count())->toBe(6);
});

it('lets tenants add custom categories in the reserved slug namespace', function (): void {
    $custom = FundingPositionCategory::factory()->create();

    expect($custom->slug)->toStartWith(FundingPositionCategory::CUSTOM_SLUG_PREFIX)
        ->and($custom->is_system)->toBeFalse()
        ->and($custom->source)->toBe('custom');

    // Ein System-Reseed darf nie in den Custom-Namensraum hineinlaufen.
    (new \Database\Seeders\FundingPositionCategorySeeder)->run();

    expect(FundingPositionCategory::where('id', $custom->id)->exists())->toBeTrue()
        ->and(FundingPositionCategory::system()->count())->toBe(6);
});

it('guards system categories against deletion and slug changes', function (): void {
    $category = FundingPositionCategory::where('slug', 'personalkosten')->firstOrFail();

    expect(fn () => $category->delete())->toThrow(ValidationException::class);
    expect(fn () => $category->refresh()->update(['slug' => 'geaendert']))->toThrow(ValidationException::class);
    expect(fn () => $category->refresh()->update(['source' => 'custom']))->toThrow(ValidationException::class);

    // Namensänderung (z. B. Übersetzung) ist erlaubt.
    $category->refresh()->update(['name' => 'Personalkosten neu']);
    expect($category->refresh()->name)->toBe('Personalkosten neu');
});

// -----------------------------------------------------------------------------
// Plan/Ist-Berechnung
// -----------------------------------------------------------------------------

it('computes actual against budget per position', function (): void {
    $funding = Funding::factory()->create();
    $position = FundingPosition::factory()->for($funding)->withBudget(1_000_00)->create();

    $full = Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
        'amount_gross' => 400_00,
    ]);
    $partial = Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
        'amount_gross' => 250_00,
    ]);

    FundingTransaction::create([
        'funding_id' => $funding->id,
        'transaction_id' => $full->id,
        'funding_position_id' => $position->id,
    ]);
    FundingTransaction::create([
        'funding_id' => $funding->id,
        'transaction_id' => $partial->id,
        'allocated_amount' => 100_00,
        'funding_position_id' => $position->id,
    ]);

    // Dürfen nicht ins Ist einfließen: Einnahmen und nicht gebuchte Ausgaben.
    $deposit = Transaction::factory()->create([
        'type' => TransactionType::Deposit,
        'status' => TransactionStatus::booked,
        'amount_gross' => 999_00,
    ]);
    $submitted = Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::submitted,
        'amount_gross' => 888_00,
    ]);

    foreach ([$deposit, $submitted] as $ignored) {
        FundingTransaction::create([
            'funding_id' => $funding->id,
            'transaction_id' => $ignored->id,
            'funding_position_id' => $position->id,
        ]);
    }

    expect($position->actualAmount())->toBe(500_00)
        ->and($position->remainingBudget())->toBe(500_00);
});

it('links one transaction to two fundings with different positions each', function (): void {
    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
        'amount_gross' => 1_000_00,
    ]);

    $fundingA = Funding::factory()->create();
    $fundingB = Funding::factory()->create();
    $positionA = FundingPosition::factory()->for($fundingA)->withBudget(500_00)->create();
    $positionB = FundingPosition::factory()->for($fundingB)->withBudget(500_00)->create();

    AppendFundingTransaction::handle($transaction, $fundingA, 600_00, $positionA->id);
    AppendFundingTransaction::handle($transaction, $fundingB, 400_00, $positionB->id);

    expect(FundingTransaction::where('transaction_id', $transaction->id)->count())->toBe(2)
        ->and($positionA->actualAmount())->toBe(600_00)
        ->and($positionB->actualAmount())->toBe(400_00)
        ->and($positionA->remainingBudget())->toBe(-100_00);
});

// -----------------------------------------------------------------------------
// Verantwortlicher: member_id nullOnDelete
// -----------------------------------------------------------------------------

it('keeps the position when the responsible member is deleted', function (): void {
    $member = \App\Models\Membership\Member::factory()->create();
    $position = FundingPosition::factory()->create(['member_id' => $member->id]);

    $member->delete();

    $position = $position->fresh();
    expect($position)->not->toBeNull()
        ->and($position->member_id)->toBeNull()
        ->and($position->responsible)->toBeNull();
});

// -----------------------------------------------------------------------------
// Statusbericht
// -----------------------------------------------------------------------------

it('creates and stores a funding status report document', function (): void {
    $this->actingAs(User::factory()->create());
    Storage::fake('local');

    $funding = Funding::factory()->create(['title' => 'Demokratie leben']);
    FundingPosition::factory()->for($funding)->withBudget(1_000_00)->create([
        'title' => 'Honorare',
        'funding_position_category_id' => FundingPositionCategory::where('slug', 'honorare')->value('id'),
    ]);

    $document = app(ProjectFundingReportService::class)->createFundingReport($funding, 'statusbericht');

    expect($document->documentable_id)->toBe($funding->id)
        ->and($document->mime_type)->toBe('application/pdf')
        ->and($document->category)->toBe('report')
        ->and($document->label)->toBe('Status- und Mittelverwendungsbericht')
        ->and($document->original_name)->toContain('Foerderbericht-statusbericht-demokratie-leben');

    Storage::disk('local')->assertExists($document->path);
});

// -----------------------------------------------------------------------------
// Livewire: Positions-CRUD + Kategorien in der Funding-Ansicht
// -----------------------------------------------------------------------------

it('creates, updates and deletes positions via the funding show page', function (): void {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $funding = Funding::factory()->create();
    $category = FundingPositionCategory::where('slug', 'honorare')->firstOrFail();

    Livewire::test(FundingShowPage::class, ['funding' => $funding])
        ->set('positionForm.title', 'Honorare Workshops')
        ->set('positionForm.budget', '400,00')
        ->set('positionForm.funding_position_category_id', $category->id)
        ->call('savePosition')
        ->assertHasNoErrors();

    $position = $funding->fundingPositions()->firstOrFail();
    expect($position->budget)->toBe(400_00)
        ->and($position->funding_position_category_id)->toBe($category->id);

    Livewire::test(FundingShowPage::class, ['funding' => $funding])
        ->call('editPosition', $position->id)
        ->set('positionForm.budget', '600,00')
        ->call('savePosition')
        ->assertHasNoErrors();

    expect($position->refresh()->budget)->toBe(600_00);

    Livewire::test(FundingShowPage::class, ['funding' => $funding])
        ->call('deletePosition', $position->id);

    expect($funding->fundingPositions()->count())->toBe(0);
});

it('shows the soft budget warning when positions exceed the approved amount', function (): void {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $funding = Funding::factory()->create(['approved_amount' => 1_000_00]);
    FundingPosition::factory()->for($funding)->withBudget(1_500_00)->create();

    Livewire::test(FundingShowPage::class, ['funding' => $funding])
        ->assertSee(__('fundings.positions.warning.budget_exceeded.heading'));
});

it('lets admins add custom categories but keeps system categories read-only', function (): void {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $funding = Funding::factory()->create();

    Livewire::test(FundingShowPage::class, ['funding' => $funding])
        ->set('newCategoryName', 'Veranstaltungstechnik')
        ->call('addCategory')
        ->assertHasNoErrors();

    $category = FundingPositionCategory::where('source', 'custom')->firstOrFail();
    expect($category->slug)->toBe('custom:veranstaltungstechnik')
        ->and($category->is_system)->toBeFalse();
});

it('forbids non-admins from adding categories', function (): void {
    $this->actingAs(User::factory()->create(['is_admin' => false]));
    $funding = Funding::factory()->create();

    Livewire::test(FundingShowPage::class, ['funding' => $funding])
        ->set('newCategoryName', 'Veranstaltungstechnik')
        ->call('addCategory');

    expect(FundingPositionCategory::where('source', 'custom')->count())->toBe(0);
});

// -----------------------------------------------------------------------------
// Livewire: Positions-Zuordnung im Transaktions-Flow
// -----------------------------------------------------------------------------

it('assigns a funding position when appending a transaction to a funding', function (): void {
    $this->actingAs(\App\Models\Membership\Member::factory()->withUser()->create([
        'user_id' => User::factory()->create(['email_verified_at' => now()])->id,
    ])->user);

    $fy = \App\Models\Accounting\FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
        'amount_gross' => 100_00,
        'date' => now()->year(2025)->startOfYear(),
    ]);
    $funding = Funding::factory()->create();
    $position = FundingPosition::factory()->for($funding)->create();

    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $funding->id)
        ->set('target_funding_position', $position->id)
        ->call('appendFunding')
        ->assertHasNoErrors();

    expect(FundingTransaction::where('transaction_id', $transaction->id)->firstOrFail()->funding_position_id)
        ->toBe($position->id);
});

it('rejects positions that do not belong to the chosen funding', function (): void {
    $this->actingAs(\App\Models\Membership\Member::factory()->withUser()->create([
        'user_id' => User::factory()->create(['email_verified_at' => now()])->id,
    ])->user);

    $fy = \App\Models\Accounting\FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
        'amount_gross' => 100_00,
        'date' => now()->year(2025)->startOfYear(),
    ]);
    $funding = Funding::factory()->create();
    $foreignPosition = FundingPosition::factory()->create();

    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $funding->id)
        ->set('target_funding_position', $foreignPosition->id)
        ->call('appendFunding')
        ->assertHasErrors(['target_funding_position']);

    expect(FundingTransaction::where('transaction_id', $transaction->id)->exists())->toBeFalse();
});

it('links one transaction to two fundings via the UI, each validated against its own funding', function (): void {
    $this->actingAs(\App\Models\Membership\Member::factory()->withUser()->create([
        'user_id' => User::factory()->create(['email_verified_at' => now()])->id,
    ])->user);

    $fy = \App\Models\Accounting\FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
        'amount_gross' => 1_000_00,
        'date' => now()->year(2025)->startOfYear(),
    ]);

    $fundingA = Funding::factory()->create();
    $fundingB = Funding::factory()->create();
    $positionA = FundingPosition::factory()->for($fundingA)->create();
    $positionB = FundingPosition::factory()->for($fundingB)->create();

    // Erste Verknüpfung: Förderung A mit Position A (mit Teilbetrag – Pflicht
    // sobald die Buchung an mehr als einer Förderung hängt).
    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $fundingA->id)
        ->set('target_funding_position', $positionA->id)
        ->set('target_funding_allocated', '600,00')
        ->call('appendFunding')
        ->assertHasNoErrors();

    // Zweite Verknüpfung: Förderung B mit Position B – unabhängige Validierung
    // gegen Förderung B (Position A von Förderung A würde hier abgelehnt).
    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $fundingB->id)
        ->set('target_funding_position', $positionB->id)
        ->set('target_funding_allocated', '400,00')
        ->call('appendFunding')
        ->assertHasNoErrors();

    $links = FundingTransaction::where('transaction_id', $transaction->id)->get();
    expect($links)->toHaveCount(2)
        ->and($links->firstWhere('funding_id', $fundingA->id)->funding_position_id)->toBe($positionA->id)
        ->and($links->firstWhere('funding_id', $fundingB->id)->funding_position_id)->toBe($positionB->id);
});

it('rejects the second link when its position belongs to the first funding', function (): void {
    $this->actingAs(\App\Models\Membership\Member::factory()->withUser()->create([
        'user_id' => User::factory()->create(['email_verified_at' => now()])->id,
    ])->user);

    $fy = \App\Models\Accounting\FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
        'amount_gross' => 1_000_00,
        'date' => now()->year(2025)->startOfYear(),
    ]);

    $fundingA = Funding::factory()->create();
    $fundingB = Funding::factory()->create();
    $positionA = FundingPosition::factory()->for($fundingA)->create();

    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $fundingA->id)
        ->set('target_funding_position', $positionA->id)
        ->call('appendFunding')
        ->assertHasNoErrors();

    // Position A gehört zu Förderung A – für Förderung B ungültig.
    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $fundingB->id)
        ->set('target_funding_position', $positionA->id)
        ->call('appendFunding')
        ->assertHasErrors(['target_funding_position']);

    // Nur die erste Zeile existiert.
    $links = FundingTransaction::where('transaction_id', $transaction->id)->get();
    expect($links)->toHaveCount(1)
        ->and($links->first()->funding_id)->toBe($fundingA->id);
});

it('still blocks linking the same transaction twice to the same funding', function (): void {
    $this->actingAs(\App\Models\Membership\Member::factory()->withUser()->create([
        'user_id' => User::factory()->create(['email_verified_at' => now()])->id,
    ])->user);

    $fy = \App\Models\Accounting\FiscalYear::getOrCreate(2025);
    session(['fiscalYearId' => $fy->id]);

    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
        'amount_gross' => 100_00,
        'date' => now()->year(2025)->startOfYear(),
    ]);
    $funding = Funding::factory()->create();

    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $funding->id)
        ->call('appendFunding')
        ->assertHasNoErrors();

    Livewire::test(TransactionIndexPage::class)
        ->call('appendToFunding', $transaction->id)
        ->set('target_funding', $funding->id)
        ->call('appendFunding')
        ->assertHasErrors(['transaction.id']);

    expect(FundingTransaction::where('transaction_id', $transaction->id)->count())->toBe(1);
});
