<?php

declare(strict_types=1);

return [

    'page' => [
        'title' => 'Fundings',
    ],

    'index' => [
        'search_placeholder' => 'Search funding or funder...',
        'ongoing' => 'ongoing',
        'btn' => [
            'create' => 'New funding',
        ],
        'table' => [
            'title' => 'Title',
            'funder' => 'Funder',
            'status' => 'Status',
            'approved_amount' => 'Approved',
            'period' => 'Period',
            'projects' => 'Projects',
        ],
    ],

    'create' => [
        'page' => [
            'title' => 'New funding',
        ],
        'btn' => [
            'submit' => 'Create funding',
        ],
        'success' => [
            'title' => 'Funding created',
            'content' => 'The funding has been saved successfully.',
        ],
    ],

    'show' => [
        'title' => 'Funding:',
        'page' => [
            'title' => 'Funding',
        ],
        'toast' => [
            'updated' => 'Funding saved.',
        ],
    ],

    'reports' => [
        'actions' => [
            'executive' => 'Create executive summary',
            'detailed' => 'Create detailed report',
            'statusbericht' => 'Create status report',
        ],
        'toast' => [
            'created' => 'The funding report has been created and stored in documents.',
        ],
    ],

    'form' => [
        'title' => 'Title',
        'funder' => 'Funder',
        'reference' => 'File number / Reference',
        'reference_hint' => 'Internal reference number of the funder',
        'status' => 'Status',
        'description' => 'Description / Notes',
        'approved_amount' => 'Approved amount',
        'period_start' => 'Funding start',
        'period_end' => 'Funding end',
        'btn' => [
            'save' => 'Save',
            'delete' => 'Delete',
        ],
        'confirm' => [
            'delete' => 'Really delete funding? This cannot be undone.',
        ],
    ],

    'tabs' => [
        'details' => 'Details',
        'receipts' => 'Receipts',
        'projects' => 'Projects',
        'positions' => 'Positions',
        'documents' => 'Documents',
    ],

    'receipts' => [
        'stat' => [
            'approved' => 'Approved',
            'received' => 'Received',
            'remaining' => 'Outstanding',
        ],
        'table' => [
            'date' => 'Date',
            'label' => 'Description',
            'allocated' => 'Allocation',
            'amount' => 'Amount',
            'full_amount' => 'Full amount',
        ],
        'empty' => 'No receipts recorded yet.',
    ],

    'projects' => [
        'stat' => [
            'approved' => 'Approved',
            'allocated' => 'Allocated to projects',
            'unallocated' => 'Unallocated',
        ],
        'table' => [
            'title' => 'Project',
            'status' => 'Status',
            'period' => 'Period',
            'allocated' => 'Allocated',
        ],
        'empty' => 'No projects linked yet.',
    ],

    'status' => [
        'applied' => 'Applied',
        'approved' => 'Approved',
        'active' => 'Active',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
    ],

    'link_project' => [
        'btn' => ['open' => 'Link project'],
        'heading' => [
            'new' => 'Link project',
            'edit' => 'Edit allocation',
        ],
        'form' => [
            'project' => 'Project',
            'project_placeholder' => 'Select project...',
            'allocated_amount' => 'Allocated (per decision notice)',
            'allocated_amount_hint' => 'Amount per funding decision for this project.',
            'editing_hint' => 'Change the allocation amount.',
            'remaining_hint' => 'Still available from this funding',
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
            'attached' => 'Project linked successfully.',
            'updated' => 'Allocation updated.',
            'detached' => 'Link has been removed.',
        ],
        'error' => [
            'already_linked' => 'This project is already linked.',
            'invalid_amount' => 'Please enter a valid amount greater than 0.',
            'exceeds_remaining' => 'Amount exceeds the remaining available (:remaining).',
        ],
    ],
    // -------------------------------------------------------------------------
    // Tab: Positions (plan/actual per funding position)
    // -------------------------------------------------------------------------
    'positions' => [
        'btn' => [
            'create' => 'Add position',
        ],
        'table' => [
            'title' => 'Position',
            'category' => 'Category',
            'budget' => 'Plan',
            'actual' => 'Actual',
            'remaining' => 'Remaining',
            'due_date' => 'Due',
            'responsible' => 'Responsible',
        ],
        'empty' => 'No positions yet. Add positions to track planned budgets against actual expenses.',
        'menu' => [
            'edit' => 'Edit',
            'delete' => 'Delete',
            'delete_confirm' => 'Really delete this position? Transaction assignments will be lost.',
        ],
        'modal' => [
            'heading_create' => 'Add position',
            'heading_edit' => 'Edit position',
        ],
        'form' => [
            'title' => 'Title',
            'budget' => 'Planned budget (gross)',
            'budget_hint' => 'Planned budget according to the grant notification for this position.',
            'category' => 'Category',
            'category_placeholder' => 'No category',
            'responsible' => 'Responsible person',
            'responsible_placeholder' => 'Nobody assigned',
            'due_date' => 'Due date',
            'description' => 'Description / Notes',
            'btn' => [
                'save' => 'Save',
            ],
        ],
        'toast' => [
            'saved' => 'Position saved.',
            'deleted' => 'Position deleted.',
        ],
        'warning' => [
            'budget_exceeded' => [
                'heading' => 'Position budgets exceed the approved amount',
                'text' => 'The sum of position budgets (:sum) is higher than the approved amount (:approved). Please check the plan against the grant notification.',
            ],
        ],
        'categories' => [
            'heading' => 'Manage categories',
            'system_badge' => 'System',
            'new_label' => 'Custom category',
            'new_placeholder' => 'Category name...',
            'btn' => [
                'add' => 'Add',
            ],
            'delete_confirm' => 'Really delete this custom category? Positions keep their data but lose the category.',
            'toast' => [
                'created' => 'Category created.',
                'deleted' => 'Category deleted.',
            ],
            'error' => [
                'duplicate' => 'A category with this name already exists.',
                'system_readonly' => 'System categories cannot be changed or deleted.',
            ],
        ],
    ],

    'documents' => [
        'category' => [
            'approval_notice' => 'Funding decision',
            'usage_proof' => 'Usage proof',
            'correspondence' => 'Correspondence / Emails',
            'contract' => 'Contract / Agreement',
            'report' => 'Activity report',
            'other' => 'Other',
        ],
    ],

];
