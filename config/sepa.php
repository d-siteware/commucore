<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | SEPA Creditor Information
    |--------------------------------------------------------------------------
    |
    | Your organisation's SEPA Creditor Identifier (Gläubiger-ID).
    | Issued by the Deutsche Bundesbank. Format: DE00ZZZ00000000000
    |
    */
    'creditor_id' => env('SEPA_CREDITOR_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Creditor Account
    |--------------------------------------------------------------------------
    |
    | The default bank account (App\Models\Accounting\Account ID) used
    | for SEPA direct debit collections.
    |
    */
    'creditor_account_id' => env('SEPA_CREDITOR_ACCOUNT_ID', null),

    /*
    |--------------------------------------------------------------------------
    | SEPA Pain Format
    |--------------------------------------------------------------------------
    |
    | The ISO20022 pain.008 version to use for XML generation.
    | Recommended: pain.008.001.09 (widely supported by German banks)
    |
    */
    'pain_format' => env('SEPA_PAIN_FORMAT', 'pain.008.001.09'),

    /*
    |--------------------------------------------------------------------------
    | Due Date Offset
    |--------------------------------------------------------------------------
    |
    | How many weekdays in the future the direct debit should be executed.
    | For CORE: minimum 2 banking days (we use 5 for safety).
    | For B2B: minimum 1 banking day.
    |
    */
    'due_date_offset_workdays' => env('SEPA_DUE_DATE_OFFSET', 5),

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | Which disk to store generated SEPA XML files on.
    |
    */
    'storage_disk' => env('SEPA_STORAGE_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | EBICS Configuration (optional, for automatic submission)
    |--------------------------------------------------------------------------
    |
    | When using EBICS to submit SEPA files directly to your bank.
    |
    */
    'ebics' => [
        'host' => env('EBICS_HOST', ''),
        'host_id' => env('EBICS_HOST_ID', ''),
        'partner_id' => env('EBICS_PARTNER_ID', ''),
        'user_id' => env('EBICS_USER_ID', ''),
        'passphrase' => env('EBICS_PASSPHRASE', ''),
        'cert_dir' => env('EBICS_CERT_DIR', storage_path('ebics')),
    ],
];
