<?php

declare(strict_types=1);

use App\Models\Accounting\Account;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;

it('rewraps bank fields encrypted with the central key to the instance key', function () {
    $centralKey = Encrypter::generateKey('AES-256-CBC');
    $centralEncrypter = new Encrypter($centralKey, 'AES-256-CBC');
    $environmentFile = tempnam(sys_get_temp_dir(), 'commucore-legacy-key-');
    file_put_contents($environmentFile, 'APP_KEY=base64:'.base64_encode($centralKey).PHP_EOL);

    $legacyAccount = Account::factory()->create();
    DB::table('accounts')->where('id', $legacyAccount->id)->update([
        'iban' => $centralEncrypter->encryptString('DE12345678901234567890'),
        'bic' => $centralEncrypter->encryptString('BELADEBEXXX'),
    ]);

    $currentIban = 'DE98765432101234567890';
    $currentAccount = Account::factory()->create([
        'iban' => $currentIban,
        'bic' => 'GENODEF1XXX',
    ]);

    $previousEnvironmentFile = getenv('COMMCORE_LEGACY_APP_ENV_FILE');
    putenv('COMMCORE_LEGACY_APP_ENV_FILE='.$environmentFile);

    try {
        $migration = require database_path('migrations/2026_10_26_000002_reencrypt_central_key_bank_fields.php');
        $migration->up();
        $migration->up();
    } finally {
        if ($previousEnvironmentFile === false) {
            putenv('COMMCORE_LEGACY_APP_ENV_FILE');
        } else {
            putenv('COMMCORE_LEGACY_APP_ENV_FILE='.$previousEnvironmentFile);
        }

        unlink($environmentFile);
    }

    expect($legacyAccount->fresh()->iban)->toBe('DE12345678901234567890')
        ->and($legacyAccount->fresh()->bic)->toBe('BELADEBEXXX')
        ->and($currentAccount->fresh()->iban)->toBe($currentIban)
        ->and($currentAccount->fresh()->bic)->toBe('GENODEF1XXX');
});
