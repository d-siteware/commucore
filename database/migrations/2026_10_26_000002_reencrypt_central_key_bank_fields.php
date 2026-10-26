<?php

declare(strict_types=1);

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        $legacyEncrypter = $this->legacyEncrypter();
        if ($legacyEncrypter === null) {
            return;
        }

        $this->reencryptTable('accounts', ['iban', 'bic'], $legacyEncrypter);
        $this->reencryptTable('sepa_mandates', ['iban', 'bic', 'account_holder'], $legacyEncrypter);
    }

    public function down(): void
    {
        // Data is deliberately kept encrypted with the instance key.
    }

    private function legacyEncrypter(): ?Encrypter
    {
        $environmentFile = getenv('COMMCORE_LEGACY_APP_ENV_FILE');
        if (! is_string($environmentFile) || ! is_readable($environmentFile)) {
            return null;
        }

        $environment = parse_ini_file($environmentFile, false, INI_SCANNER_RAW);
        $key = is_array($environment) ? ($environment['APP_KEY'] ?? null) : null;
        if (! is_string($key) || $key === '') {
            return null;
        }

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7), true);
            if ($key === false) {
                return null;
            }
        }

        try {
            return new Encrypter($key, (string) config('app.cipher'));
        } catch (Throwable) {
            return null;
        }
    }

    /** @param array<int, string> $fields */
    private function reencryptTable(string $table, array $fields, Encrypter $legacyEncrypter): void
    {
        DB::table($table)
            ->select(array_merge(['id'], $fields))
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $row) use ($table, $fields, $legacyEncrypter): void {
                $updates = [];

                foreach ($fields as $field) {
                    $value = $row->{$field};
                    if ($value === null) {
                        continue;
                    }

                    try {
                        Crypt::decryptString($value);

                        continue;
                    } catch (DecryptException) {
                        // Attempt to decrypt values previously encrypted with the central key.
                    }

                    try {
                        $plaintext = $legacyEncrypter->decryptString($value);
                        $updates[$field] = Crypt::encryptString($plaintext);
                    } catch (DecryptException) {
                        Log::warning('Could not re-encrypt bank field with the legacy central key', [
                            'table' => $table,
                            'row_id' => $row->id,
                            'field' => $field,
                        ]);
                    }
                }

                if ($updates !== []) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            });
    }
};
