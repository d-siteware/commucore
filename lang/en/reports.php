<?php

declare(strict_types=1);

return [
    'event.title' => 'Event report',
    'event.subject' => 'Evaluation of event :name',
    'event.visitor.name' => 'Visitors',

    'account' => [
        'title' => 'Cash report',
        'timespan' => 'Time period',
        'heading' => 'Header data',
        'start' => 'Start',
        'end' => 'End',
        'starting_amount' => 'Starting amount',
        'end_amount' => 'Ending amount',
        'total_income' => 'Total income',
        'total_expenditure' => 'Total expenses',
        'notes' => 'Notes',
        'new' => [
            'header' => 'Create new report',
        ],
        'edit' => [
            'heading' => 'Edit',
        ],
        'btn' => [
            'get_transactions' => 'Get transactions for period',
            'store_data' => 'Save data',
        ],
    ],

    'table.header.date' => 'Created on',
    'table.header.name' => 'Account',
    'table.header.status' => 'Status',
    'table.header.range' => 'Period',
    'table.header.audited' => 'Audited',

    'initiate-report-audit-modal.title' => 'Start report audit',
    'initiate-report-audit-modal.content' => 'Please select the members who should perform the audit.',
    'initiate-report-audit-modal.btn.submit' => 'Send invitations',
    'initiate-report-audit-modal.select_member_id' => 'Member',

    'index' => [
        'title' => 'Monthly reports',
        'actions' => [
            'datev_export' => 'DATEV CSV',
        ],
        'export_warning' => [
            'title' => 'Report already exported',
            'body' => 'This report has already been transmitted as a DATEV export to the tax advisor. A re-audit may invalidate the existing export.',
            'steuerberater_hint' => 'If you continue, please inform your tax advisor about the corrected export.',
            'confirm' => 'Proceed anyway',
        ],

    ],

    'status' => [
        'eingereicht' => 'in review',
        'entwurf' => 'submitted',
        'geprueft' => 'audited',
        'draft' => 'draft',
        'submitted' => 'submitted',
        'audited' => 'audited',
        'rejected' => 'rejected',
    ],
    '' => '',
];
