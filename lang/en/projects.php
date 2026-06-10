<?php

declare(strict_types=1);

return [

    'page' => [
        'title' => 'Projects',
    ],

    'index' => [
        'search_placeholder' => 'Search project...',
        'btn' => [
            'create' => 'New project',
        ],
        'table' => [
            'title' => 'Title',
            'status' => 'Status',
            'start_date' => 'Start',
            'end_date' => 'End',
            'fundings' => 'Fundings',
            'transactions' => 'Transactions',
        ],
    ],

    'create' => [
        'page' => [
            'title' => 'New project',
        ],
        'btn' => [
            'submit' => 'Create project',
        ],
        'success' => [
            'title' => 'Project created',
            'content' => 'The project has been saved successfully.',
        ],
    ],

    'show' => [
        'title' => 'Project:',
        'page' => [
            'title' => 'Project',
        ],
        'toast' => [
            'updated' => 'Project saved.',
        ],
    ],

    'form' => [
        'title' => 'Title',
        'description' => 'Description / Notes',
        'status' => 'Status',
        'start_date' => 'Start date',
        'end_date' => 'End date',
        'btn' => [
            'save' => 'Save',
            'delete' => 'Delete',
        ],
        'confirm' => [
            'delete' => 'Really delete project? This cannot be undone.',
        ],
    ],

    'tabs' => [
        'details' => 'Details',
        'financials' => 'Finances',
        'fundings' => 'Fundings',
        'posts' => 'Blog',
        'documents' => 'Documents',
    ],

    'financials' => [
        'income' => 'Income',
        'expense' => 'Expenses',
        'balance' => 'Balance',
        'empty' => 'No transactions recorded yet.',
        'table' => [
            'date' => 'Date',
            'label' => 'Description',
            'type' => 'Type',
            'allocated' => 'Allocation',
            'amount' => 'Amount',
            'full_amount' => 'Full amount',
        ],
    ],

    'fundings' => [
        'stat' => [
            'allocated' => 'Funding allocated',
            'expense' => 'Project expenses',
            'coverage' => 'Coverage ratio',
        ],
        'table' => [
            'title' => 'Funding',
            'funder' => 'Funder',
            'status' => 'Status',
            'allocated' => 'Allocated',
        ],
        'empty' => 'No fundings linked yet.',
    ],

    'posts' => [
        'btn' => ['create' => 'New post'],
        'table' => [
            'title' => 'Title',
            'author' => 'Author',
            'status' => 'Status',
            'published_at' => 'Published',
        ],
        'empty' => 'No posts yet.',
    ],

    'link_funding' => [
        'btn' => ['open' => 'Link funding'],
        'heading' => [
            'new' => 'Link funding',
            'edit' => 'Edit allocation',
        ],
        'form' => [
            'funding' => 'Funding',
            'funding_placeholder' => 'Select funding...',
            'allocated_amount' => 'Allocated (per decision notice)',
            'allocated_amount_hint' => 'Amount per funding decision for this project.',
            'editing_hint' => 'Change the allocation amount.',
            'btn' => [
                'attach' => 'Link',
                'update' => 'Update',
            ],
        ],
        'menu' => [
            'edit' => 'Edit amount',
            'detach' => 'Unlink',
            'detach_confirm' => 'Really unlink? The allocated amount will be lost.',
        ],
        'success' => [
            'attached' => 'Funding linked successfully.',
            'updated' => 'Allocation updated.',
            'detached' => 'Link has been removed.',
        ],
        'error' => [
            'already_linked' => 'This funding is already linked.',
            'invalid_amount' => 'Please enter a valid amount greater than 0.',
            'exceeds_remaining' => 'Amount exceeds the remaining available (:remaining).',
        ],
    ],

    'status' => [
        'planned' => 'Planned',
        'active' => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'documents' => [
        'category' => [
            'planning' => 'Planning / Concept',
            'contract' => 'Contract',
            'report' => 'Activity report / Final report',
            'invoice' => 'Invoice / Cost breakdown',
            'correspondence' => 'Correspondence / Emails',
            'photo' => 'Photos / Documentation',
            'other' => 'Other',
        ],
    ],

];
