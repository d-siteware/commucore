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
            'print' => 'Print',
            'audit' => 'Audit',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ],
        'export_warning' => [
            'title' => 'Report already exported',
            'body' => 'This report has already been transmitted as a DATEV export to the tax advisor. A re-audit may invalidate the existing export.',
            'steuerberater_hint' => 'If you continue, please inform your tax advisor about the corrected export.',
            'confirm' => 'Proceed anyway',
        ],

        'datev_export' => [
            'not_possible' => 'DATEV export not possible',
            'only_audited' => 'Only audited reports can be exported.',
            'failed' => 'DATEV export failed',
            'checklist' => [
                'heading' => 'DATEV export – Checklist',
                'subheading' => 'The following checks are performed before the export:',
                'all_ok' => 'All checks passed!',
                'not_ready' => 'Please resolve the issues and try again.',
            ],
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
    'get_transactions_short' => 'Get transactions',
    'transactions_found' => ':count transactions found',
    'no_transactions_in_period' => 'No transactions found in this period!',
    'no_email_for_auditor' => 'No email found for :email',
    'no_auditors_selected' => 'Please select auditors for the audit!',
    'delete_error' => 'The report could not be deleted: :message',
    'delete_success' => 'The report has been deleted successfully',
    'data_updated' => 'Report data updated',
    'default_filename' => 'Report',
    'audits_found_heading' => 'Audits found',
    'audits_delete_warning' => 'The report to be deleted has linked audits. These will be lost when the report is deleted.',
    'delete_all' => 'Delete all',
    'select_member_placeholder' => 'Select member',
    'add_auditor' => 'Add',
    'nobody' => 'Nobody',
    'create_report_btn' => 'Create report',
    'auditor' => 'Auditor',
    'board_member_not_allowed_as_auditor' => 'Board members cannot be selected as auditors',
];
