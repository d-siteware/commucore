<?php

declare(strict_types=1);

use App\Console\Commands\PseudonymizeMembersCommand;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Helpers ──────────────────────────────────────────────────────────────────

/**
 * @param  array<string, mixed>  $overrides
 */
function createMemberRow(array $overrides = []): int
{
    return DB::table('members')->insertGetId(array_merge([
        'name' => 'Max Mustermann',
        'first_name' => 'Max',
        'email' => 'max@example.com',
        'phone' => '+49123456789',
        'mobile' => '+4917612345678',
        'address' => 'Musterstraße 1',
        'zip' => '12345',
        'city' => 'Musterstadt',
        'country' => 'DE',
        'gender' => 'male',
        'birth_date' => '1990-01-01',
        'birth_place' => 'Musterstadt',
        'citizenship' => 'DE',
        'family_status' => 'single',
        'applied_at' => now()->subYears(5)->toDateString(),
        'entered_at' => now()->subYears(5)->toDateString(),
        'gdpr_consent_at' => now()->subYears(5)->toDateTimeString(),
        'gdpr_legal_basis' => 'contract',
        'fee_type' => 'full',
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString(),
    ], $overrides));
}

// ── Core pseudonymisation ─────────────────────────────────────────────────────

it('pseudonymises a member whose retention period has expired', function (): void {
    $memberId = createMemberRow([
        'left_at' => Carbon::now()->subYears(4)->toDateString(),
    ]);

    $this->artisan('gdpr:pseudonymize-members --years=3')
        ->assertSuccessful();

    $member = DB::table('members')->find($memberId);

    expect($member->name)->toBe("PSEUDONYMIZED_{$memberId}")
        ->and($member->first_name)->toBeNull()
        ->and($member->email)->toBeNull()
        ->and($member->phone)->toBeNull()
        ->and($member->mobile)->toBeNull()
        ->and($member->address)->toBeNull()
        ->and($member->zip)->toBeNull()
        ->and($member->city)->toBeNull()
        ->and($member->birth_date)->toBeNull()
        ->and($member->gdpr_consent_at)->toBeNull()
        ->and($member->newsletter_consent_at)->toBeNull()
        ->and($member->photo_consent_at)->toBeNull()
        ->and($member->pseudonymized_at)->not->toBeNull();
});

it('does not pseudonymise a member still within the retention window', function (): void {
    $memberId = createMemberRow([
        'left_at' => Carbon::now()->subYears(2)->toDateString(),
    ]);

    $this->artisan('gdpr:pseudonymize-members --years=3')
        ->assertSuccessful();

    $member = DB::table('members')->find($memberId);

    expect($member->pseudonymized_at)->toBeNull()
        ->and($member->email)->toBe('max@example.com');
});

it('skips members who are still active (no left_at)', function (): void {
    $memberId = createMemberRow(['left_at' => null]);

    $this->artisan('gdpr:pseudonymize-members --years=3')
        ->assertSuccessful();

    $member = DB::table('members')->find($memberId);

    expect($member->pseudonymized_at)->toBeNull();
});

it('skips members already pseudonymised', function (): void {
    $memberId = createMemberRow([
        'left_at' => Carbon::now()->subYears(5)->toDateString(),
        'pseudonymized_at' => Carbon::now()->subYear()->toDateTimeString(),
        'name' => 'PSEUDONYMIZED_99',
        'email' => null,
    ]);

    $this->artisan('gdpr:pseudonymize-members --years=3')
        ->assertSuccessful();

    // pseudonymized_at should not be updated again
    $member = DB::table('members')->find($memberId);
    expect($member->name)->toBe('PSEUDONYMIZED_99');
});

// ── Dry run ───────────────────────────────────────────────────────────────────

it('does not modify data in dry-run mode', function (): void {
    $memberId = createMemberRow([
        'left_at' => Carbon::now()->subYears(4)->toDateString(),
    ]);

    $this->artisan('gdpr:pseudonymize-members --dry-run')
        ->assertSuccessful();

    $member = DB::table('members')->find($memberId);

    expect($member->pseudonymized_at)->toBeNull()
        ->and($member->email)->toBe('max@example.com');
});

// ── Validation ────────────────────────────────────────────────────────────────

it('returns failure when --years is less than 1', function (): void {
    $this->artisan('gdpr:pseudonymize-members --years=0')
        ->assertFailed();
});

// ── Audit log ─────────────────────────────────────────────────────────────────

it('writes a log entry for each pseudonymised member', function (): void {
    Log::spy();

    $memberId = createMemberRow([
        'left_at' => Carbon::now()->subYears(4)->toDateString(),
    ]);

    $this->artisan('gdpr:pseudonymize-members --years=3');

    Log::shouldHaveReceived('info')
        ->once()
        ->withArgs(static function (string $channel, array $context) use ($memberId): bool {
            return $channel === 'gdpr.pseudonymized_member'
                && $context['member_id'] === $memberId;
        });
});

// ── Field shape (PHPStan-friendly) ────────────────────────────────────────────

it('pseudonymisedFields returns correct structure for PHPStan', function (): void {
    $command = new PseudonymizeMembersCommand;
    $fields = $command->pseudonymisedFields(42);

    expect($fields)->toBeArray()
        ->and($fields['name'])->toBe('PSEUDONYMIZED_42')
        ->and(array_key_exists('email', $fields))->toBeTrue()
        ->and(array_key_exists('pseudonymized_at', $fields))->toBeTrue();
});

// ── Financial data preservation ───────────────────────────────────────────────

it('preserves member_transactions FK after pseudonymisation', function (): void {
    $memberId = createMemberRow([
        'left_at' => Carbon::now()->subYears(4)->toDateString(),
    ]);

    // Simulate a transaction linked to this member.
    $transactionId = DB::table('transactions')->insertGetId([
        'label' => 'pseudonymisation',
        'date' => now()->toDateString(),
        'amount_gross' => 6000,
        'amount_net' => 2000,
        'vat' => 19,
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString(),
        'account_id' => \App\Models\Accounting\Account::factory()->create()->id,
        'type' => \App\Enums\TransactionType::Deposit,
        'status' => \App\Enums\TransactionStatus::booked,
    ]);

    DB::table('member_transactions')->insert([
        'member_id' => $memberId,
        'transaction_id' => $transactionId,
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString(),
    ]);

    $this->artisan('gdpr:pseudonymize-members --years=3')
        ->assertSuccessful();

    // Financial link must still exist.
    $link = DB::table('member_transactions')->where('member_id', $memberId)->first();
    expect($link)->not->toBeNull()
        ->and($link->transaction_id)->toBe($transactionId);
});
