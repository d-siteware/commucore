<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('sepa_mandates')->orderBy('id')->lazyById()->each(function (object $row): void {
            DB::table('sepa_mandates')->where('id', $row->id)->update([
                'iban' => Crypt::encryptString($row->iban),
                'bic' => $row->bic !== null ? Crypt::encryptString($row->bic) : null,
                'account_holder' => Crypt::encryptString($row->account_holder),
            ]);
        });

        DB::table('members')
            ->whereNotNull('iban')
            ->orWhereNotNull('bic')
            ->orWhereNotNull('account_holder')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $row): void {
                DB::table('members')->where('id', $row->id)->update([
                    'iban' => $row->iban !== null ? Crypt::encryptString($row->iban) : null,
                    'bic' => $row->bic !== null ? Crypt::encryptString($row->bic) : null,
                    'account_holder' => $row->account_holder !== null ? Crypt::encryptString($row->account_holder) : null,
                ]);
            });

        DB::table('accounts')
            ->whereNotNull('iban')
            ->orWhereNotNull('bic')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $row): void {
                DB::table('accounts')->where('id', $row->id)->update([
                    'iban' => $row->iban !== null ? Crypt::encryptString($row->iban) : null,
                    'bic' => $row->bic !== null ? Crypt::encryptString($row->bic) : null,
                ]);
            });
    }

    public function down(): void
    {
        DB::table('sepa_mandates')->orderBy('id')->lazyById()->each(function (object $row): void {
            DB::table('sepa_mandates')->where('id', $row->id)->update([
                'iban' => Crypt::decryptString($row->iban),
                'bic' => $row->bic !== null ? Crypt::decryptString($row->bic) : null,
                'account_holder' => Crypt::decryptString($row->account_holder),
            ]);
        });

        DB::table('members')
            ->whereNotNull('iban')
            ->orWhereNotNull('bic')
            ->orWhereNotNull('account_holder')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $row): void {
                DB::table('members')->where('id', $row->id)->update([
                    'iban' => $row->iban !== null ? Crypt::decryptString($row->iban) : null,
                    'bic' => $row->bic !== null ? Crypt::decryptString($row->bic) : null,
                    'account_holder' => $row->account_holder !== null ? Crypt::decryptString($row->account_holder) : null,
                ]);
            });

        DB::table('accounts')
            ->whereNotNull('iban')
            ->orWhereNotNull('bic')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $row): void {
                DB::table('accounts')->where('id', $row->id)->update([
                    'iban' => $row->iban !== null ? Crypt::decryptString($row->iban) : null,
                    'bic' => $row->bic !== null ? Crypt::decryptString($row->bic) : null,
                ]);
            });
    }
};
