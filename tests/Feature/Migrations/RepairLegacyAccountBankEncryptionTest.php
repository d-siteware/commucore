<?php

declare(strict_types=1);

use App\Models\Accounting\Account;
use Illuminate\Support\Facades\DB;

it('encrypts legacy plaintext account bank fields and preserves valid ciphertext', function () {
    $legacyIban = 'DE12345678901234567890';
    $legacyAccount = Account::factory()->create();
    DB::table('accounts')->where('id', $legacyAccount->id)->update([
        'iban' => $legacyIban,
        'bic' => '',
    ]);

    $encryptedIban = 'DE98765432101234567890';
    $encryptedAccount = Account::factory()->create([
        'iban' => $encryptedIban,
        'bic' => 'BELADEBEXXX',
    ]);

    $migration = require database_path('migrations/2026_10_26_000000_repair_legacy_account_bank_encryption.php');
    $migration->up();
    $migration->up();

    expect($legacyAccount->fresh()->iban)->toBe($legacyIban)
        ->and($legacyAccount->fresh()->bic)->toBeNull()
        ->and($encryptedAccount->fresh()->iban)->toBe($encryptedIban)
        ->and($encryptedAccount->fresh()->bic)->toBe('BELADEBEXXX');
});
