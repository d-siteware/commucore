<?php

declare(strict_types=1);

return [
    'mandate' => [
        'heading' => 'SEPA Direct Debit Mandate',
        'status' => [
            'pending' => 'Pending',
            'active' => 'Active',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
        ],
        'type' => [
            'core' => 'Core Direct Debit (CORE)',
            'b2b' => 'Business Direct Debit (B2B)',
        ],
        'fields' => [
            'mandate_reference' => 'Mandate Reference',
            'iban' => 'IBAN',
            'bic' => 'BIC',
            'account_holder' => 'Account Holder',
            'mandate_date' => 'Mandate Date',
            'mandate_type' => 'Mandate Type',
            'status' => 'Status',
            'signed_document' => 'Signed Mandate (PDF)',
            'notes' => 'Notes',
        ],
        'actions' => [
            'create' => 'Create SEPA Mandate',
            'cancel' => 'Cancel Mandate',
            'view' => 'View Mandate',
        ],
        'messages' => [
            'created' => 'SEPA mandate has been created successfully.',
            'cancelled' => 'SEPA mandate has been cancelled.',
            'active' => 'Active SEPA mandate exists.',
            'no_mandate' => 'This member has no active SEPA mandate.',
            'has_active_mandate' => 'This member already has an active SEPA mandate.',
        ],
    ],
    'direct_debit' => [
        'heading' => 'SEPA Direct Debits',
        'actions' => [
            'generate_xml' => 'Generate SEPA XML',
            'run_collection' => 'Run Direct Debit',
        ],
        'messages' => [
            'xml_generated' => 'SEPA XML has been generated successfully.',
            'collection_initiated' => 'Direct debit collection has been initiated.',
        ],
        'errors' => [
            'no_account' => 'No SEPA creditor account configured.',
        ],
    ],
];
