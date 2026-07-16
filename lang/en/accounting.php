<?php

declare(strict_types=1);

return [
    'subtype' => [
        'bank' => 'Bank',
        'cash' => 'Cash',
        'receivable' => 'Receivable',
        'payable' => 'Payable',
    ],

    'export' => [
        'type' => [
            'buchungsstapel' => 'DATEV posting batch',
            'buchungsstapel_desc' => 'Posting data in DATEV format (EXTF) for import at the tax advisor.',
            'stammdaten' => 'DATEV account labels',
            'stammdaten_desc' => 'General ledger accounts as DATEV-compatible master data CSV.',
        ],
    ],
    'datev' => [
        'settings' => [
            'tab' => 'DATEV',
            'heading' => 'DATEV settings',
            'subheading' => 'Credentials for the DATEV export (posting batch). You receive the consultant number and client number from your tax advisor.',
            'not_configured_heading' => 'DATEV not configured yet',
            'not_configured_text' => 'As long as consultant number and client number are missing, the DATEV export contains placeholders and cannot be imported at the tax advisor.',
            'berater_nr' => 'Consultant number (Beraternummer)',
            'berater_nr_description' => '4–7 digits (1001–9999999), provided by the tax advisor',
            'mandant_nr' => 'Client number (Mandantennummer)',
            'mandant_nr_description' => '1–5 digits (1–99999), provided by the tax advisor',
            'skr' => 'Chart of accounts (SKR)',
            'skr_description' => 'The chart of accounts recommended by DATEV for associations.',
            'skr_42' => 'Associations, foundations, non-profit GmbH',
            'skr_49' => 'Associations (legacy, 4 digits)',
            'konto_laenge' => 'G/L account number length',
            'konto_laenge_description' => 'Derived automatically from the chart of accounts (SKR42 = 5).',
            'application_info' => 'Exported by',
            'application_info_description' => 'Appears in the DATEV header (max. 25 characters)',
            'recipient_email' => 'Email for DATEV dispatch',
            'recipient_email_description' => 'DATEV exports can be sent to the tax advisor by email.',
            'info' => [
                'heading' => 'Notes on the DATEV export',
                'numbers_text' => 'Consultant number and client number identify the client in the DATEV firm software. Both numbers must match the tax advisor\'s values exactly, otherwise the import fails.',
                'validation_text' => 'Exported files can be checked technically with the official DATEV format check program (developer.datev.de → DATEV-Format → Tools) before handover. Final validation happens via a test import in DATEV Rechnungswesen.',
            ],
            'btn' => [
                'save' => 'Save DATEV settings',
            ],
            'toast' => [
                'save_success_heading' => 'Saved',
                'save_success_text' => 'The DATEV settings have been saved.',
            ],
        ],
        'mail' => [
            'subject' => 'DATEV export :period',
            'greeting' => 'Hello',
            'heading' => 'DATEV export for :period',
            'body' => 'attached is the DATEV export for account :account for the period :period.',
            'zip_structure' => 'The ZIP archive contains the booking CSV and the associated receipts, organised by account (e.g. <b>Barkasse (Vereinskasse)</b>, <b>Bankkonto (Girokonto)</b>).',
            'download_link_label' => 'Download DATEV export',
            'link_expiry' => 'The download link is valid for 7 days.',
            'checksum_label' => 'Checksum (SHA-256) – verify after download with shasum -a 256:',
        ],
        'download' => [
            'link_expired' => 'The download link has expired. Please export the DATEV report again.',
            'not_found' => 'The export file was not found. It may have been deleted already.',
        ],
    ],
];
