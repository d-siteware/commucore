<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Account Overview',
        'btn' => [
            'fetch_data' => 'Fetch Account Data',
            'create_report' => 'Create Report',
            'create_vcashcount' => 'Create Count List',
        ],
    ],
    'dashboard' => [
        'heading' => 'Cash Year :year',
        'transactions' => [
            'title' => 'Transactions',
            'columns' => [
                'label' => 'Description',
                'amount' => 'Amount',
            ],
            'btn' => [
                'overview' => 'Overview',
                'create' => 'Submit Transaction',
                'create_short' => 'Submit',
            ],
        ],
        'sections' => [
            'balance_sheet' => 'Account Overview',
            'cash_counts' => 'Cash Counts',
        ],
        'reports' => [
            'title' => 'Reports',
            'columns' => [
                'period' => 'Period',
                'status' => 'Status',
            ],
            'btn' => [
                'print' => 'Print',
            ],
        ],
    ],
    'cashcount' => [
        'heading' => 'Overview',
        'dated' => 'dated',
        'empty_state' => 'No counts recorded',
        'btn' => [
            'delete' => 'Delete',
            'edit' => 'Edit',
        ],
        'delete' => [
            'heading' => 'Delete Count List',
            'label' => 'Please confirm deletion of count list :label',
            'warning' => 'Deletion cannot be undone!',
            'btn' => [
                'cancel' => 'Cancel',
                'submit' => 'Delete',
            ],
            'confirmationtoast' => [
                'head' => 'Success',
                'txt' => 'Count list was successfully deleted!',
            ],
        ],
        'create' => [
            'heading' => 'Create New Count List',
            'btn' => [
                'submit' => 'Save',
            ],
        ],
        'edit' => [
            'heading' => 'Edit Count List',
            'btn' => [
                'submit' => 'Update',
            ],
        ],

    ],
    'balance_sheet' => [
        'total' => 'Total Account Balance',
        'dated' => 'As of',
    ],

];
