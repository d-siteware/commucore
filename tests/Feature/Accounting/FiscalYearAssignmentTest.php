<?php

declare(strict_types=1);

use App\Models\Accounting\BookingAccountType;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use Carbon\Carbon;

afterEach(function (): void {
    Carbon::setTestNow();
});

// =========================================================================
// getActive() — geklemmte Semantik (Beschluss 2)
// =========================================================================

describe('getActive clamped semantics', function (): void {

    it('does not activate an open future fiscal year', function (): void {
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Europe/Berlin'));

        FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);
        FiscalYear::factory()->create(['year' => 2027, 'closed_at' => null]); // Vorausbuchung

        expect(FiscalYear::getActive()->year)->toBe(2026);
    });

    it('does not activate an open backfill year when current year is open', function (): void {
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Europe/Berlin'));

        FiscalYear::factory()->create(['year' => 2023, 'closed_at' => null]); // Backfill-Altjahr
        FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);

        expect(FiscalYear::getActive()->year)->toBe(2026);
    });

    it('falls back to an older open year when current year has no fiscal year', function (): void {
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Europe/Berlin'));

        FiscalYear::factory()->create(['year' => 2025, 'closed_at' => null]);

        expect(FiscalYear::getActive()->year)->toBe(2025);
    });

    it('activates a future fiscal year after the calendar year change', function (): void {
        FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);
        FiscalYear::factory()->create(['year' => 2027, 'closed_at' => null]);

        Carbon::setTestNow(Carbon::parse('2026-12-31 12:00:00', 'Europe/Berlin'));
        expect(FiscalYear::getActive()->year)->toBe(2026);

        Carbon::setTestNow(Carbon::parse('2027-01-01 00:00:01', 'Europe/Berlin'));
        expect(FiscalYear::getActive()->year)->toBe(2027);
    });

    it('counts the new year in the new year edge hour (UTC vs Berlin)', function (): void {
        // 31.12. 23:30 UTC = 1.1. 00:30 Berlin → Berlin ist schon im neuen Jahr
        Carbon::setTestNow(Carbon::parse('2026-12-31 23:30:00', 'UTC'));

        FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);
        FiscalYear::factory()->create(['year' => 2027, 'closed_at' => null]);

        expect(FiscalYear::getActive()->year)->toBe(2027);
    });

    it('returns null when only future fiscal years exist', function (): void {
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Europe/Berlin'));

        FiscalYear::factory()->create(['year' => 2027, 'closed_at' => null]);

        expect(FiscalYear::getActive())->toBeNull();
    });
});

// =========================================================================
// contextFiscalYear() — Kontenrahmen-Kontext (Beschluss 5)
// =========================================================================

describe('contextFiscalYear', function (): void {

    it('follows the session fiscal year', function (): void {
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Europe/Berlin'));

        FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);
        $sessionFy = FiscalYear::factory()->create(['year' => 2023, 'closed_at' => null]);

        session(['fiscalYearId' => $sessionFy->id]);

        expect(FiscalYear::contextFiscalYear()->id)->toBe($sessionFy->id);
    });

    it('falls back to getActive without a session', function (): void {
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Europe/Berlin'));

        $active = FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);

        expect(session('fiscalYearId'))->toBeNull()
            ->and(FiscalYear::contextFiscalYear()->id)->toBe($active->id);
    });
});

// =========================================================================
// TransactionObserver — Auto-Zuordnung (Phase 2)
// =========================================================================

describe('fiscal year auto-assignment on create', function (): void {

    it('assigns the fiscal year matching the transaction date', function (): void {
        $fy = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => null]);

        $transaction = Transaction::factory()->create(['date' => '2025-06-15']);

        expect($transaction->fiscal_year_id)->toBe($fy->id);
    });

    it('creates a missing fiscal year via getOrCreate', function (): void {
        expect(FiscalYear::where('year', 2024)->exists())->toBeFalse();

        $transaction = Transaction::factory()->create(['date' => '2024-03-01']);

        $fy = FiscalYear::where('year', 2024)->first();

        expect($fy)->not->toBeNull()
            ->and($fy->isOpen())->toBeTrue()
            ->and($transaction->fiscal_year_id)->toBe($fy->id);
    });

    it('lets an explicit override win over auto-assignment', function (): void {
        $overrideFy = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => null]);
        FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);

        // 10-Tage-Regel: Buchung vom 3. Januar gehört wirtschaftlich ins Vorjahr
        $transaction = Transaction::factory()->create([
            'date' => '2026-01-03',
            'fiscal_year_id' => $overrideFy->id,
        ]);

        expect($transaction->fiscal_year_id)->toBe($overrideFy->id);
    });

    it('creates a future fiscal year on advance booking without changing getActive', function (): void {
        Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'Europe/Berlin'));

        $current = FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);

        $transaction = Transaction::factory()->create(['date' => '2027-02-01']);

        $futureFy = FiscalYear::where('year', 2027)->first();

        expect($futureFy)->not->toBeNull()
            ->and($transaction->fiscal_year_id)->toBe($futureFy->id)
            ->and(FiscalYear::getActive()->id)->toBe($current->id);
    });
});

// =========================================================================
// Schreibschutz — GoBD / § 146 Abs. 4 AO (Phase 4)
// =========================================================================

describe('closed fiscal year write protection', function (): void {

    it('throws on updating a transaction in a closed fiscal year', function (): void {
        $closedFy = FiscalYear::factory()->create(['year' => 2024, 'closed_at' => now()]);
        $transaction = Transaction::factory()->create([
            'date' => '2024-06-15',
            'fiscal_year_id' => $closedFy->id,
        ]);

        expect(fn () => $transaction->update(['label' => 'Manipulation']))
            ->toThrow(\App\Exceptions\FiscalYearClosedException::class);
    });

    it('throws on deleting a transaction in a closed fiscal year', function (): void {
        $closedFy = FiscalYear::factory()->create(['year' => 2024, 'closed_at' => now()]);
        $transaction = Transaction::factory()->create([
            'date' => '2024-06-15',
            'fiscal_year_id' => $closedFy->id,
        ]);

        expect(fn () => $transaction->delete())
            ->toThrow(\App\Exceptions\FiscalYearClosedException::class);
    });

    it('blocks moving a transaction out of a closed fiscal year', function (): void {
        $closedFy = FiscalYear::factory()->create(['year' => 2024, 'closed_at' => now()]);
        $openFy = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => null]);
        $transaction = Transaction::factory()->create([
            'date' => '2024-06-15',
            'fiscal_year_id' => $closedFy->id,
        ]);

        expect(fn () => $transaction->update(['fiscal_year_id' => $openFy->id]))
            ->toThrow(\App\Exceptions\FiscalYearClosedException::class);
    });

    it('blocks moving a transaction into a closed fiscal year', function (): void {
        $openFy = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => null]);
        $closedFy = FiscalYear::factory()->create(['year' => 2024, 'closed_at' => now()]);
        $transaction = Transaction::factory()->create([
            'date' => '2025-06-15',
            'fiscal_year_id' => $openFy->id,
        ]);

        expect(fn () => $transaction->update(['fiscal_year_id' => $closedFy->id]))
            ->toThrow(\App\Exceptions\FiscalYearClosedException::class);
    });

    it('allows updating and deleting a transaction in an open fiscal year', function (): void {
        $openFy = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => null]);
        $transaction = Transaction::factory()->create([
            'date' => '2025-06-15',
            'fiscal_year_id' => $openFy->id,
        ]);

        $transaction->update(['label' => 'Korrigiert']);
        expect($transaction->fresh()->label)->toBe('Korrigiert');

        $transaction->delete();
        expect(Transaction::find($transaction->id))->toBeNull();
    });

    it('provides a user-friendly translated exception message', function (): void {
        $closedFy = FiscalYear::factory()->create(['year' => 2024, 'closed_at' => now()]);
        $transaction = Transaction::factory()->create([
            'date' => '2024-06-15',
            'fiscal_year_id' => $closedFy->id,
            'label' => 'Mitgliedsbeitrag Meier',
        ]);

        try {
            $transaction->update(['label' => 'X']);
            $this->fail('Expected FiscalYearClosedException');
        } catch (\App\Exceptions\FiscalYearClosedException $e) {
            expect($e->getMessage())->toContain('Mitgliedsbeitrag Meier')
                ->and($e->transaction->id)->toBe($transaction->id);
        }
    });
});

// =========================================================================
// Backfill-Migration (Phase 1)
// =========================================================================

describe('backfill migration', function (): void {

    function runBackfill(): void
    {
        $migration = require database_path('migrations/2026_07_16_000002_backfill_fiscal_year_id_on_transactions.php');
        $migration->up();
    }

    it('assigns transactions to their year and skips gap years', function (): void {
        // Buchungen 2023 und 2025 – 2024 ist Lücke
        Transaction::factory()->create(['date' => '2023-05-01']);
        Transaction::factory()->create(['date' => '2025-08-01']);

        // Observer-Zuordnung + auto-erzeugte FYs zurücksetzen → Altdaten simulieren
        Transaction::query()->update(['fiscal_year_id' => null]);
        FiscalYear::query()->delete();

        runBackfill();

        expect(Transaction::whereNull('fiscal_year_id')->count())->toBe(0)
            ->and(FiscalYear::where('year', 2023)->exists())->toBeTrue()
            ->and(FiscalYear::where('year', 2024)->exists())->toBeFalse() // Lücke → kein leeres FY
            ->and(FiscalYear::where('year', 2025)->exists())->toBeTrue()
            ->and(FiscalYear::where('year', 2023)->first()->isOpen())->toBeTrue(); // Beschluss 1
    });

    it('copies booking_account_type_id onto backfilled fiscal years', function (): void {
        $type = BookingAccountType::factory()->create();

        Transaction::factory()->create(['date' => '2023-05-01']);
        Transaction::query()->update(['fiscal_year_id' => null]);
        FiscalYear::query()->delete();

        // Bestehendes FY mit Kontenrahmen als Kopierquelle
        FiscalYear::factory()->create([
            'year' => 2026,
            'closed_at' => null,
            'booking_account_type_id' => $type->id,
        ]);

        runBackfill();

        expect(FiscalYear::where('year', 2023)->first()->booking_account_type_id)
            ->toBe($type->id);
    });

    it('is idempotent on re-run', function (): void {
        Transaction::factory()->create(['date' => '2023-05-01']);
        Transaction::query()->update(['fiscal_year_id' => null]);
        FiscalYear::query()->delete();

        runBackfill();
        $firstAssignment = Transaction::first()->fiscal_year_id;

        runBackfill(); // zweiter Lauf darf nichts ändern

        expect(Transaction::first()->fiscal_year_id)->toBe($firstAssignment)
            ->and(FiscalYear::where('year', 2023)->count())->toBe(1);
    });
});

// =========================================================================
// Service-Guards (Phase 7)
// =========================================================================

describe('FiscalYearService close guards', function (): void {

    it('blocks closing when an older fiscal year is still open', function (): void {
        $user = \App\Models\User::factory()->create(['is_admin' => true]);

        Carbon::setTestNow(Carbon::parse('2025-06-15', 'Europe/Berlin'));
        FiscalYear::factory()->create(['year' => 2024, 'closed_at' => null]); // älter, offen
        $fy2025 = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => null]);

        $service = app(\App\Services\Accounting\FiscalYearService::class);

        expect(fn () => $service->closeFiscalYear(2025, $user->id))
            ->toThrow(\RuntimeException::class, 'oldest open');
    });

    it('blocks closing when transactions have fiscal_year_id = NULL in the year', function (): void {
        $user = \App\Models\User::factory()->create(['is_admin' => true]);

        Carbon::setTestNow(Carbon::parse('2026-06-15', 'Europe/Berlin'));
        $fy = FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);

        // QB-Insert bypasses Observer – so entsteht eine echte Waise
        \Illuminate\Support\Facades\DB::table('transactions')->insert([
            'date' => '2026-05-01 00:00:00',
            'label' => 'Orphan',
            'amount_gross' => 1000,
            'amount_net' => 810,
            'vat' => 19,
            'account_id' => \App\Models\Accounting\Account::factory()->create()->id,
            'type' => \App\Enums\TransactionType::Deposit->value,
            'status' => \App\Enums\TransactionStatus::booked->value,
            'fiscal_year_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = app(\App\Services\Accounting\FiscalYearService::class);

        expect(fn () => $service->closeFiscalYear(2026, $user->id))
            ->toThrow(\RuntimeException::class, 'have no fiscal year');
    });

    it('allows closing a fiscal year with valid transactions', function (): void {
        $user = \App\Models\User::factory()->create(['is_admin' => true]);

        Carbon::setTestNow(Carbon::parse('2026-06-15', 'Europe/Berlin'));
        $fy = FiscalYear::factory()->create(['year' => 2026, 'closed_at' => null]);
        $tx = Transaction::factory()->create([
            'date' => '2026-05-01',
            'fiscal_year_id' => $fy->id,
        ]);

        $service = app(\App\Services\Accounting\FiscalYearService::class);
        $result = $service->closeFiscalYear(2026, $user->id);

        expect($result->isClosed())->toBeTrue()
            ->and($tx->fresh()->isEditable())->toBeFalse();
    });
});

// =========================================================================
// Form-Validierung (Phase 3)
// =========================================================================

describe('TransactionForm fiscal year validation', function (): void {

    it('rejects a closed fiscal year in validation', function (): void {
        $closedFy = FiscalYear::factory()->create(['year' => 2024, 'closed_at' => now()]);

        $form = new \App\Livewire\Forms\Accounting\TransactionForm(
            new \App\Livewire\Accounting\Transaction\Create\Form,
            'form'
        );
        $form->date = '2024-06-15';
        $form->fiscal_year_id = $closedFy->id;
        $form->amount_gross = '100';
        $form->label = 'Test';

        expect(fn () => $form->validate())
            ->toThrow(\Illuminate\Validation\ValidationException::class);
    });

    it('rejects a fiscal year ±2 years from the transaction date', function (): void {
        $fyFar = FiscalYear::factory()->create(['year' => 2028, 'closed_at' => null]);

        $form = new \App\Livewire\Forms\Accounting\TransactionForm(
            new \App\Livewire\Accounting\Transaction\Create\Form,
            'form'
        );
        $form->date = '2026-06-15';
        $form->fiscal_year_id = $fyFar->id;
        $form->amount_gross = '100';
        $form->label = 'Test';

        expect(fn () => $form->validate())
            ->toThrow(\Illuminate\Validation\ValidationException::class);
    });

    it('passes validation with a valid fiscal year ±1', function (): void {
        $fyPrev = FiscalYear::factory()->create(['year' => 2025, 'closed_at' => null]);

        $form = new \App\Livewire\Forms\Accounting\TransactionForm(
            new \App\Livewire\Accounting\Transaction\Create\Form,
            'form'
        );
        $form->date = '2026-06-15';
        $form->fiscal_year_id = $fyPrev->id;
        $form->amount_gross = '100';
        $form->label = 'Test';
        $form->amount_net = '81';
        $form->vat = 19;
        $form->account_id = \App\Models\Accounting\Account::factory()->create()->id;
        $form->type = \App\Enums\TransactionType::Deposit;
        $form->status = \App\Enums\TransactionStatus::submitted;

        $form->validate(); // sollte nicht fehlschlagen
        expect(true)->toBeTrue();
    });
});
