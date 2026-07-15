<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Accounts overview',
        'title_no_state' => 'Select account',
        'btn' => [
            'fetch_data' => 'Fetch account data',
            'create_report' => 'Create report',
            'create_vcashcount' => 'Create cash count list',
            'create_account' => 'Create new account',
        ],
    ],
    'area' => [

        'ideal' => [
            'label' => 'Ideal area',
            'description' => 'Association work',
        ],

        'asset_management' => [
            'label' => 'Asset management',
            'description' => 'Interest, rental',
        ],
        'purpose_operation' => [
            'label' => 'Purpose operation',
            'description' => 'Association events',
        ],
        'economic_business' => [
            'label' => 'Economic business',
            'description' => 'Sales, catering',
        ],
    ],

    'category' => [
        'asset' => 'Asset',
        'liability' => 'Liability',
        'income' => 'Income',
        'expense' => 'Expense',
    ],
    'dashboard' => [
        'heading' => 'Fiscal year :year',
        'transactions' => [
            'title' => 'Transactions',
            'columns' => [
                'label' => 'Label',
                'amount' => 'Amount',
            ],
            'btn' => [
                'overview' => 'Overview',
                'create' => 'Submit transaction',
                'create_short' => 'Submit',
            ],
        ],
        'sections' => [
            'balance_sheet' => 'Account overview',
            'cash_counts' => 'Cash counts',
        ],
        'reports' => [
            'title' => 'Reports',
            'columns' => [
                'period' => 'Period',
                'status' => 'Status',
            ],
            'btn' => [
                'print' => 'print',
            ],
        ],
    ],
    'cashcount' => [
        'heading' => 'Overview',
        'dated' => 'dated',
        'empty_state' => 'No counts recorded',
        'btn' => [
            'delete' => 'delete',
            'edit' => 'edit',
        ],
        'delete' => [
            'heading' => 'Delete cash count list',
            'label' => 'Please confirm deletion of cash count list :label',
            'warning' => 'Deletion cannot be undone!',
            'btn' => [
                'cancel' => 'Cancel',
                'submit' => 'Delete',
            ],
            'confirmationtoast' => [
                'head' => 'Success',
                'txt' => 'Cash count list has been successfully deleted!',
            ],
        ],
        'create' => [
            'heading' => 'Create new cash count list',
            'btn' => [
                'submit' => 'Record',
            ],
        ],
        'edit' => [
            'heading' => 'Edit cash count list',
            'btn' => [
                'submit' => 'Update',
            ],
        ],

    ],
    'balance_sheet' => [
        'total' => 'Total account balance',
        'dated' => 'Balance',
    ],

    'toast' => [
        'created' => [
            'heading' => 'Success',
            'text' => 'The account has been created.',
        ],
        'updated' => [
            'heading' => 'Success',
            'text' => 'The account has been updated.',
        ],
        'payment_account_created' => [
            'heading' => 'Success',
            'text' => 'The payment account has been created',
        ],
        'booking_account_created' => [
            'heading' => 'Success',
            'text' => 'The booking account has been created',
        ],
    ],

    'select_placeholder' => 'Select account ...',

    'tabs' => [
        'details' => 'Details',
        'transactions' => 'Transactions',
        'reports' => 'Reports',
        'cash_counts' => 'Cash counts',
    ],

    'columns' => [
        'label' => 'Label',
        'amount' => 'Amount',
        'type' => 'Type',
        'status' => 'Status',
    ],

];
