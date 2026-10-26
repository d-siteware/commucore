<?php

declare(strict_types=1);

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('accounts')
            ->select(['id', 'iban', 'bic'])
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $account): void {
                $updates = [];

                foreach (['iban' => 34, 'bic' => 11] as $field => $maxLength) {
                    $value = $account->{$field};
                    if ($value === null) {
                        continue;
                    }

                    try {
                        Crypt::decryptString($value);

                        continue;
                    } catch (DecryptException) {
                        if ($value !== '' && mb_strlen($value) > $maxLength) {
                            Log::warning('Account bank field has an invalid encrypted payload', [
                                'account_id' => $account->id,
                                'field' => $field,
                            ]);

                            continue;
                        }
                    }

                    if ($value === '') {
                        $updates[$field] = null;

                        continue;
                    }

                    $updates[$field] = Crypt::encryptString($value);
                }

                if ($updates !== []) {
                    DB::table('accounts')->where('id', $account->id)->update($updates);
                }
            });
    }

    public function down(): void
    {
        // The plaintext values are intentionally not restored.
    }
};
