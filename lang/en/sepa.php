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
            'sepa_documents' => 'Mandate Authorization (PDF)',
            'sepa_documents_dropzone_heading' => 'Drop file or select',
            'sepa_documents_dropzone_text' => 'Upload signed SEPA mandate as PDF',
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
            'updated' => 'SEPA mandate has been updated successfully.',
            'pending_fees_warning' => 'Warning: There are pending direct debits. Already submitted transactions will still be processed.',
            'replaced' => 'The previous SEPA mandate has been cancelled.',
        ],
    ],
    'settings' => [
        'tab' => 'SEPA',
        'creditor' => [
            'heading' => 'Creditor & Payment Format',
            'subheading' => 'SEPA Creditor ID and default account for direct debits.',
            'creditor_id' => 'Creditor ID',
            'account' => 'Creditor Account',
            'account_placeholder' => 'Select bank account…',
            'due_date_offset' => 'Due Date Offset (days)',
            'pain_format' => 'Pain Format',
        ],
        'info' => [
            'heading' => 'Information',
            'creditor_id_label' => 'Where do I get a Creditor ID?',
            'creditor_id_text' => 'The Creditor Identifier (Gläubiger-ID) is issued by the Deutsche Bundesbank. Apply online at www.bundesbank.de – search for "Gläubiger-ID beantragen". The ID is valid throughout Germany, e.g. DE00ZZZ00000000000.',
            'pain_formats_label' => 'What do the PAIN formats mean?',
            'pain_02' => 'Older format, supported by most German banks.',
            'pain_09' => 'Current standard format (ISO 20022). Recommended for new customers. Used in Germany and Austria.',
            'pain_at' => 'Austrian format (ISO 20022), identical to pain.008.001.09.',
            'pain_301' => 'Swiss format (ISO 20022), for accounts in Switzerland.',
            'pain_recommendation' => 'Recommendation: use pain.008.001.09 unless your bank requires a specific format.',
        ],
        'transfer' => [
            'mode' => 'Transfer Mode',
            'mode_manual' => 'Manual (XML download)',
            'mode_ebics' => 'Automatic via EBICS',
        ],
        'ebics' => [
            'heading' => 'EBICS Configuration',
            'subheading' => 'Credentials for automatic SEPA submission to the bank.',
            'host' => 'EBICS Host URL',
            'host_id' => 'EBICS Host ID',
            'partner_id' => 'EBICS Partner ID',
            'user_id' => 'EBICS User ID',
            'passphrase' => 'EBICS Passphrase',
        ],
        'btn' => [
            'save' => 'Save SEPA settings',
        ],
        'toast' => [
            'save_success_heading' => 'SEPA settings saved',
            'save_success_text' => 'SEPA configuration has been updated successfully.',
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
    'return_debit' => [
        'heading' => 'Returned Debits',
        'actions' => [
            'mark_returned' => 'Mark as returned',
            'recollect' => 'Re-collect',
        ],
        'messages' => [
            'marked_returned' => 'Transaction has been marked as returned.',
            'recollected' => 'Re-collection has been initiated.',
        ],
        'errors' => [
            'no_active_mandate' => 'No active SEPA mandate for re-collection.',
            'no_transaction' => 'Transaction not found.',
        ],
        'columns' => [
            'date' => 'Date',
            'member' => 'Member',
            'amount' => 'Amount',
            'reason' => 'Reason',
            'actions' => 'Actions',
        ],
        'no_returns' => 'No returned debits found.',
    ],
    'notifications' => [
        'return_debit' => [
            'subject' => 'Returned debit of :amount',
            'intro' => 'Hello :name, a direct debit of :amount was returned by your bank.',
            'reason' => 'Reason: :reason',
            'action' => 'Please check your payment details and ensure sufficient funds. Contact your association if you have questions.',
        ],
    ],
    'collection' => [
        'heading' => 'SEPA Collection Overview',
        'subheading' => 'Overview of pending and past direct debit collections.',
        'tabs' => [
            'pending' => 'Pending',
            'history' => 'History',
            'returns' => 'Returns',
        ],
        'pending_none' => 'No pending direct debits.',
        'no_history' => 'No SEPA collections yet.',
        'create_none' => 'No new fee transactions to create – all eligible members already have transactions.',
        'transactions_created' => ':count fee transaction(s) have been created.',
        'actions' => [
            'create_transactions' => 'Create Transactions',
            'generate_xml' => 'Generate XML',
            'download_xml' => 'Download XML',
            'generate_and_download' => 'Create & Download XML',
            'upload_ebics' => 'Submit via EBICS',
        ],
        'columns' => [
            'member' => 'Member',
            'mandate' => 'Mandate Reference',
            'amount' => 'Amount',
            'fee_year' => 'Fee Year',
            'status' => 'Status',
        ],
        'errors' => [
            'ebics_not_configured' => 'EBICS is not configured. Please complete the EBICS setup first.',
        ],
        'messages' => [
            'ebics_upload_success' => 'SEPA XML has been submitted via EBICS successfully. All transactions have been marked as booked.',
        ],
    ],
    'validation' => [
        'passed' => '✅ XML validation passed: the SEPA file is formally correct.',
        'failed' => '⚠️ XML validation failed (:count error(s)):',
        'error_line' => '  Line :line: :message',
        'step_validate' => 'Validating XML…',
    ],
];
