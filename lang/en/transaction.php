<?php

declare(strict_types=1);

return [
    'documents' => [
        'heading' => 'Documents',
        'category' => [
            'label' => 'Category',
            'invoice' => 'Invoice',
            'receipt' => 'Receipt',
            'bank_statement' => 'Bank statement',
            'contract' => 'Contract',
            'other' => 'Other',
        ],
        'btn' => [
            'upload' => 'Upload document',
        ],
        'modal_title' => 'Attach documents to transaction',
        'drag_hint' => 'Drag files here or click to select',
    ],
    'edit-text-modal' => [
        'heading' => 'Change transaction texts',
        'label' => 'Label',
        'reference' => 'Reference',
        'description' => 'Description',
        'btn' => [
            'label' => 'Save',
        ],
        'update-success' => [
            'text' => 'The texts have been updated successfully',
            'heading' => 'Success!',
        ],
    ],
    'detach-member-success' => [
        'text' => 'The link between the transaction and the member has been removed successfully',
        'heading' => 'Success',
    ],
    'attach-member-success' => [
        'text' => 'The link between the transaction and the member has been created successfully',
        'heading' => 'Success',
    ],
    'attach-event-success' => [
        'heading' => 'Success',
        'text' => 'The link between the transaction and the event has been created successfully',
    ],
    'detach-event-success' => [
        'text' => 'The link between the transaction and the event has been removed successfully',
        'heading' => 'Success',
    ],
    'access' => [
        'denied' => 'You do not have permission to manage transactions: ',
    ],
    'cancel-transaction-modal' => [
        'reason' => [
            'label' => 'Provide reason for cancellation',
            'error' => 'A reason for cancellation must be provided!',
        ],
        'heading' => 'Cancel transaction',
        'btn' => [
            'submit' => [
                'label' => 'Cancel',
            ],
        ],
    ],
    'delete' => [
        'success' => [
            'heading' => 'Success',
            'msg' => 'The transaction has been deleted successfully',
        ],

    ],
    'delete-transaction-confirmation-modal' => [
        'heading' => 'Transaction has receipts',
        'has_documents' => 'The transaction has one linked receipt that will also be deleted. This action cannot be undone!|The transaction has :count linked receipts that will also be deleted. This action cannot be undone!',
        'btn' => 'Delete permanently',
    ],
    'index' => [
        'title' => 'Transactions overview',
        'menu-item' => [
            'book' => 'Book',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'cancel' => 'Cancel',
            'edit_text' => 'Change texts',
            'rebook' => 'Rebook',
            'attach_document' => 'Attach receipt',
            'attach_event' => 'Event',
            'attach_member' => 'Member',
            'detach_event' => 'Event',
            'detach_member' => 'Member',
            'send_invoice' => 'Send email',
            'print_invoice' => 'Print',
            'attach_project' => 'Assign project',
            'detach_project' => 'Remove project',
            'attach_funding' => 'Assign funding',
            'detach_funding' => 'Remove funding',
        ],
        'menu-group' => [
            'booking' => 'Booking',
            'receipt' => 'Receipt',
        ],
        'menu-submenu' => [
            'assign' => 'Assign',
            'detach' => 'Detach',
        ],
        'table' => [
            'empty-results' => 'No transactions found',
            'columns' => [
                'booking' => 'Booking',
                'date' => 'Booked on',
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
                'project_assigned' => 'Project',
                'funding_assigned' => 'Funding',
            ],
        ],
        'search' => [
            'placeholder' => 'Search ...',
        ],
        'filter' => [
            'date_range' => [
                'placeholder' => 'filter by period',
            ],
            'type' => [
                'placeholder' => 'filter by type',
                'suffix' => 'Transaction type',
            ],
            'status' => [
                'placeholder' => 'filter by status',
                'suffix' => 'Transaction status',
            ],
        ],
        'btn' => [
            'create' => 'New transaction',
        ],
        'confirm' => [
            'resend_invoice' => 'The email has already been sent. Send again?',
            'detach_project' => 'Really remove project assignment?',
            'detach_funding' => 'Really remove funding assignment?',
        ],
        'modal' => [
            'max' => 'Max',
            'edit' => [
                'heading' => 'Edit transaction',
            ],
            'append_event' => [
                'heading' => 'Assign event',
                'select_placeholder' => 'Select event',
                'optional' => 'Optional',
                'btn' => [
                    'submit' => 'assign',
                ],
            ],
            'append_member' => [
                'heading' => 'Assign member',
                'select_placeholder' => 'Select member',
                'membership_fees' => 'Membership fees',
                'is_membership_fee' => 'Is membership payment',
                'fee_year' => 'Record for fiscal year',
                'btn' => [
                    'submit' => 'Assign member',
                ],
            ],
            'append_project' => [
                'heading' => 'Assign project',
                'select_placeholder' => 'Select project...',
                'allocated_amount' => 'Allocated amount',
                'allocated_amount_hint' => 'Optional: Only allocate the proportional amount of this transaction to the project.',
                'btn' => ['submit' => 'Assign'],
            ],

            'append_funding' => [
                'heading' => 'Assign funding',
                'select_placeholder' => 'Select funding...',
                'allocated_amount' => 'Allocated amount',
                'allocated_amount_hint' => 'Optional: Only allocate the proportional amount of this transaction to the funding.',
                'booking_amount' => 'Transaction amount',
                'funding_remaining' => 'Still available in funding',
                'max_allocatable' => 'Max. allocatable',
                'btn' => ['submit' => 'Assign'],
                'error' => [
                    'exceeds_amount' => 'The proportional amount may not exceed the transaction amount (:amount).',
                ],
            ],
        ],
    ],
    'create' => [
        'page' => [
            'title' => 'Create transaction',
            'heading' => 'Create new transaction',
        ],
        'title' => 'New Transaction',
    ],
    'account-transfer-modal' => [
        'heading' => 'Rebooking (change account)',
        'content' => 'The rebooking cancels the selected transaction and creates a new transaction linked to the new account',
        'reason' => 'Reason for rebooking',
        'new_account' => 'New account',
        'account_placeholder' => 'Payment account e.g. cash, bank account, etc',
        'btn' => [
            'submit' => 'Rebook',
        ],
        'error' => [
            'transaction_id' => 'No transaction has been selected',
            'account_id' => 'No account has been selected',
            'identical' => 'The original account should not be selected',
            'reason' => 'A reason must be provided!',
        ],
    ],
    'account' => [
        'name' => 'Account',
        'number' => 'Number',
        'institute' => 'Institution',
        'type' => 'Type',
        'iban' => 'IBAN',
        'bic' => 'BIC',
        'starting_amount' => 'Opening balance',
    ],
    'mail' => [
        'receipt' => [
            'subject' => 'Receipt for contribution received',
            'title' => 'Receipt for contribution received',
            'greeting' => '',
            'header' => 'Overview',
            'body' => 'Thank you for your contribution! Please find attached the receipt for your records. If you have any questions, feel free to reply to this email.',
            'date' => 'Payment received on:',
            'amount' => 'Amount received',
            'label' => 'Reference/subject',
            'reference' => 'Reference',
        ],
        'send' => [
            'success' => 'Invoice was successfully sent to :email.',
            'success_heading' => 'Success',
            'error' => 'Error sending the invoice: :message',
            'error_heading' => 'Error',
            'no_email' => 'The invoice cannot be sent because the member has no email address. Please enter one or print and send by mail.',
            'no_email_heading' => 'Error',
        ],
    ],
    'event' => [
        'boxoffice' => [
            'heading' => 'Box office',
            'paymentsection' => 'Booking data',
            'visitorsection' => 'Visitor data',
            'visitorname' => 'Name',
            'visitoremail' => 'Email',
            'submit' => 'Record box office',
            'select_cash_desk' => 'Select cash desk',
            'select_account' => 'Select account',
        ],
    ],
    'status' => [
        'submitted' => 'submitted',
        'booked' => 'booked',
    ],
    'locked' => [
        'tooltip' => 'This transaction is locked (part of a closed fiscal year)',
        'cannot_modify' => 'This transaction cannot be edited because it is part of a closed fiscal year.',
    ],
    'type' => [
        'deposit' => 'Deposit',
        'withdrawal' => 'Withdrawal',
        'transfer' => 'Transfer',
        'reversal' => 'Reversal',
    ],
    'attach-project-success' => [
        'heading' => 'Project assigned',
        'text' => 'The transaction has been assigned to the project successfully.',
        'error' => [
            'exceeds_amount' => 'The proportional amount may not exceed the transaction amount (:amount).',
        ],
    ],
    'detach-project-success' => [
        'heading' => 'Project removed',
        'text' => 'The project assignment has been removed.',
    ],
    'attach-funding-success' => [
        'heading' => 'Funding assigned',
        'text' => 'The transaction has been assigned to the funding successfully.',
        'error' => [
            'exceeds_amount' => 'The proportional amount may not exceed the transaction amount (:amount).',
        ],
    ],
    'detach-funding-success' => [
        'heading' => 'Funding removed',
        'text' => 'The funding assignment has been removed.',
    ],

    'form' => [
        'type' => 'Booking',
        'status' => 'Status',
        'separator' => [
            'accounts' => 'Accounts',
            'amounts' => 'Amounts',
            'texts' => 'Texts',
        ],
        'account' => [
            'placeholder' => 'Payment account e.g. cash, bank account, etc',
            'new' => 'New payment account',
        ],
        'booking_account' => [
            'placeholder' => 'SKR42 account',
            'new' => 'New booking account',
        ],
        'area' => [
            'placeholder' => 'Tax sphere (KOST1)',
        ],
        'amount_gross' => 'Gross',
        'vat_percent' => 'VAT [%]',
        'vat_amount' => 'VAT [EUR]',
        'amount_net' => 'Net',
        'label' => 'Description',
        'reference' => 'Reference',
        'date' => 'Date',
        'description' => 'Description',
        'btn' => [
            'new' => 'Start new transaction',
            'save_event' => 'Save event transaction',
            'save_member' => 'Save member transaction',
            'save' => 'Save transaction',
        ],
        'validation' => [
            'label_required' => 'Please enter a description for the transaction.',
            'account_id_required' => 'Please select a payment account',
            'type_required' => 'The transaction type must be specified',
            'status_required' => 'The transaction status must be specified',
        ],
    ],

    'modal' => [
        'account' => [
            'heading' => 'Create payment account',
            'type_placeholder' => 'Account type',
            'name' => 'Name',
            'number' => 'Number',
            'starting_amount' => 'Opening balance',
            'institute' => 'Institution',
            'iban' => 'IBAN',
            'bic' => 'BIC',
            'btn' => [
                'save_and_continue' => 'Save and create another',
                'save_and_select' => 'Save and select',
            ],
        ],
        'booking' => [
            'heading' => 'Create booking account',
            'category_label' => 'Account category',
            'category_placeholder' => 'Choose category',
            'area_label' => 'Tax sphere',
            'area_placeholder' => 'Select area',
            'subtype_label' => 'Subtype',
            'subtype_placeholder' => 'No subtype',
            'label' => 'Description',
            'skr49' => 'SKR-49 number',
            'btn' => [
                'save_and_continue' => 'Save and create another',
                'save_and_select' => 'Save and select',
            ],
        ],
        'missing' => [
            'heading' => 'No transaction',
            'text' => 'No transaction has been recorded yet to which a receipt could be assigned',
        ],
    ],

    'booking' => [
        'heading' => 'Assign booking',
        'label' => 'Assign SKR account',
        'new_booking_account' => 'New SKR 49 booking account',
        'submit' => 'Complete booking',
    ],

    'booking-update-success' => [
        'text' => 'The booking has been updated',
        'heading' => 'Success',
    ],

    'cancel-success' => [
        'text' => 'The transaction :label has been cancelled',
        'heading' => 'Success',
    ],

    'change-success' => [
        'text' => 'The transaction :label has been changed',
        'heading' => 'Success',
    ],

    'event-create-success' => [
        'text' => 'The event transaction has been created',
        'heading' => 'Success',
    ],

    'member-create-success' => [
        'text' => 'The member fee transaction has been created',
        'heading' => 'Success',
    ],

    'create-success' => [
        'text' => 'The transaction :label has been created',
        'heading' => 'Success',
    ],

    'update-success' => [
        'text' => 'The transaction :label has been updated',
        'heading' => 'Success',
    ],

    'attach-success' => [
        'text' => 'The transaction has been successfully assigned',
    ],

    'area-reset-warning' => [
        'text' => 'Booking account was reset – it does not belong to the selected sphere.',
    ],

    'create-error' => [
        'text' => 'The transaction could not be saved: :message',
        'heading' => 'Error',
    ],

    'validation' => [
        'valid_amount' => 'Please enter a valid amount.',

        'event' => [
            'account_id' => [
                'required' => 'Please specify a payment account',
                'doesnt_start_with' => 'Please specify or create a payment account',
            ],
        ],

        'member' => [
            'account_id' => [
                'required' => 'Please select or create a payment account',
            ],
            'label' => [
                'required' => 'Please enter a description',
            ],
            'amount_gross' => [
                'required' => 'Please enter an amount',
            ],
        ],

        'append_event' => [
            'target_event' => [
                'required' => 'Please select an event',
            ],
            'transaction_id' => [
                'unique' => 'The transaction is already assigned to this event',
            ],
        ],

        'append_member' => [
            'target_member' => [
                'required' => 'Please select a member',
            ],
            'transaction_id' => [
                'unique' => 'The transaction is already assigned to a member',
            ],
            'fee_year' => [
                'integer' => 'Transactions cannot be older than 2010',
            ],
        ],

        'append_project' => [
            'target_project' => [
                'required' => 'Please select a project.',
            ],
            'transaction_id' => [
                'unique' => 'This transaction is already assigned to a project.',
            ],
        ],

        'append_funding' => [
            'target_funding' => [
                'required' => 'Please select a funding.',
            ],
            'transaction_id' => [
                'unique' => 'This transaction is already assigned to a funding.',
            ],
        ],

        'boxoffice' => [
            'amount_gross' => [
                'required' => 'Enter ticket price (0 for free entry)',
            ],
            'account_id' => [
                'required' => 'Please select a financial account',
            ],
        ],
    ],

    'member_transaction' => [
        'assign_event_label' => 'Assign event (optional)',
    ],
];
