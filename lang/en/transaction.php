<?php

declare(strict_types=1);

return [
    'edit-text-modal' => [
        'heading' => 'Edit Transaction Texts',
        'label' => 'Label',
        'reference' => 'Reference',
        'description' => 'Description',
        'btn' => [
            'label' => 'Save',
        ],
        'update-success' => [
            'text' => 'The texts have been successfully updated',
            'heading' => 'Success!',
        ],
    ],
    'detach-member-success' => [
        'text' => 'The link between the transaction and the member has been successfully removed',
        'heading' => 'Success',
    ],
    'attach-member-success' => [
        'text' => 'The link between the transaction and the member has been successfully created',
        'heading' => 'Success',
    ],
    'attach-event-success' => [
        'heading' => 'Success',
        'text' => 'The link between the transaction and the event has been successfully created',
    ],
    'detach-event-success' => [
        'text' => 'The link between the transaction and the event has been successfully removed',
        'heading' => 'Success',
    ],
    'access' => [
        'denied' => 'You do not have permission to manage transactions: ',
    ],
    'cancel-transaction-modal' => [
        'reason' => [
            'label' => 'Specify reason for cancellation',
            'error' => 'A reason for the cancellation must be provided!',
        ],
        'heading' => 'Cancel Transaction',
        'btn' => [
            'submit' => [
                'label' => 'Cancel',
            ],
        ],
    ],
    'index' => [
        'title' => 'Transaction Overview',
        'menu-item' => [
            'book' => 'Book',
            'edit' => 'Edit',
            'cancel' => 'Cancel',
            'edit_text' => 'Edit Texts',
            'rebook' => 'Transfer',
            'attach_event' => 'Event',
            'attach_member' => 'Member',
            'detach_event' => 'Event',
            'detach_member' => 'Member',
            'send_invoice' => 'Send Email',
            'print_invoice' => 'Print',
        ],
        'menu-group' => [
            'booking' => 'Transaction',
            'receipt' => 'Receipt',
        ],
        'menu-submenu' => [
            'assign' => 'Assign',
            'detach' => 'Detach',
        ],
        'table' => [
            'empty-results' => 'No transactions found',
            'columns' => [
                'booking' => 'Transaction',
                'date' => 'Date',
                'created' => 'Submitted',
                'status' => 'Status',
                'account' => 'Account',
                'amount' => 'Amount [EUR]',
                'type' => 'Type',
                'receipt' => 'Receipt',
                'linked' => 'Linked',
            ],
            'tooltip' => [
                'reference' => 'Reference',
                'description' => 'Description',
                'event_assigned' => 'Event assigned',
                'member_assigned' => 'Member assigned',
                'receipt_sent' => 'Receipt sent on',
            ],
        ],
        'search' => [
            'placeholder' => 'Search ...',
        ],
        'filter' => [
            'date_range' => [
                'placeholder' => 'filter by date range',
            ],
            'type' => [
                'placeholder' => 'filter by type',
                'suffix' => 'Transaction Type',
            ],
            'status' => [
                'placeholder' => 'filter by status',
                'suffix' => 'Transaction Status',
            ],
        ],
        'btn' => [
            'create' => 'Create New Transaction',
        ],
        'confirm' => [
            'resend_invoice' => 'The email has already been sent. Send again?',
        ],
        'modal' => [
            'edit' => [
                'heading' => 'Edit Transaction',
            ],
            'append_event' => [
                'heading' => 'Assign Event',
                'select_placeholder' => 'Select event',
                'optional' => 'Optional',
                'btn' => [
                    'submit' => 'assign',
                ],
            ],
            'append_member' => [
                'heading' => 'Assign Member',
                'select_placeholder' => 'Select member',
                'membership_fees' => 'Membership Fees',
                'is_membership_fee' => 'Is membership payment',
                'fee_year' => 'Record for fiscal year',
                'btn' => [
                    'submit' => 'Assign Member',
                ],
            ],
        ],
    ],
    'create' => [
        'page' => [
            'title' => 'Create Transaction',
        ],
        'title' => 'New Transaction',
    ],
    'account-transfer-modal' => [
        'heading' => 'Transfer (Change Financial Account)',
        'content' => 'The transfer cancels the selected transaction and creates a new transaction with a reference to the new financial account',
        'reason' => 'Reason for transfer',
        'new_account' => 'New Financial Account',
        'account_placeholder' => 'Payment account e.g. cash register, bank account, etc.',
        'btn' => [
            'submit' => 'Transfer',
        ],
        'error' => [
            'transaction_id' => 'No transaction has been selected',
            'account_id' => 'No financial account has been selected',
            'identical' => 'The original account should not be selected',
            'reason' => 'A reason must be provided!',
        ],
    ],
    'account' => [
        'name' => 'Financial Account',
        'number' => 'Number',
        'institute' => 'Institute',
        'type' => 'Type',
        'iban' => 'IBAN',
        'bic' => 'BIC',
        'starting_amount' => 'Starting Balance',
    ],
    'mail' => [
        'receipt' => [
            'subject' => 'Receipt for Received Contribution',
            'title' => 'Receipt for Received Contribution',
            'greeting' => '',
            'header' => 'Overview',
            'body' => 'Thank you for your contribution! Attached you will find the receipt for your records. If you have any questions, please reply to this email.',
            'date' => 'Payment received on:',
            'amount' => 'Amount Received',
            'label' => 'Purpose/Subject',
            'reference' => 'Reference',
        ],
    ],
    'event' => [
        'boxoffice' => [
            'heading' => 'Box Office',
            'paymentsection' => 'Transaction Data',
            'visitorsection' => 'Visitor Data',
            'visitorname' => 'Name',
            'visitoremail' => 'Email',
            'submit' => 'Record Box Office',
        ],
    ],
];