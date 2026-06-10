<?php

declare(strict_types=1);

return [
    'status' => [
        'pending' => 'Pending',
        'completed' => 'Approved',
        'rejected' => 'Rejected',
    ],

    'modal' => [
        'title' => 'Request change',
        'description' => 'Submit a change request to the board. You will be notified once it has been processed.',
        'submit' => 'Submit request',
    ],

    'field' => [
        'label' => 'Field to change',
        'placeholder' => 'Select field…',
    ],

    'requested_value' => [
        'label' => 'Desired value',
        'placeholder' => 'Enter desired value…',
    ],

    'reason' => [
        'label' => 'Reason',
        'placeholder' => 'Please briefly state why this change is necessary…',
    ],

    'table' => [
        'pending_heading' => 'Open requests',
        'history_heading' => 'History',
        'empty' => 'No change requests yet.',
        'col' => [
            'field' => 'Field',
            'requested_value' => 'Desired value',
            'reason' => 'Reason',
            'status' => 'Status',
            'date' => 'Submitted',
            'reviewed_by' => 'Reviewed by',
        ],
    ],

    'review' => [
        'empty' => 'No open change requests.',
        'modal' => [
            'title' => 'Process change request',
            'old_value' => 'Current value',
            'requested_value' => 'Desired value',
            'rejection_reason' => 'Reason for rejection',
            'rejection_reason_placeholder' => 'Please state the reason for rejection…',
            'rejection_reason_hint' => 'Only required for rejection.',
            'deduction_reason_placeholder' => 'Reason for fee reduction…',
            'deduction_reason_hint' => 'Will be saved as the reduction reason upon approval.',
        ],
    ],

    'btn' => [
        'review' => 'Review',
        'approve' => 'Approve',
        'reject' => 'Reject',
    ],

    'toast' => [
        'created' => [
            'heading' => 'Request submitted',
            'text' => 'Your change request has been forwarded to the board.',
        ],
        'duplicate' => [
            'heading' => 'Open request exists',
            'text' => 'There is already an open request for this field.',
        ],
        'approved' => [
            'heading' => 'Request approved',
            'text' => 'The change has been applied.',
        ],
        'rejected' => [
            'heading' => 'Request rejected',
            'text' => 'The change request has been rejected.',
        ],
    ],

    'notification' => [
        'subject' => 'New change request',
        'intro' => ':member has submitted a change request for the field ":field".',
        'old_value' => 'Current value: :value',
        'requested_value' => 'Desired value: :value',
        'reason' => 'Reason: :reason',
        'message' => ':member has requested a change for ":field": :value',
    ],

    'reviewed_notification' => [
        'subject' => 'Your change request has been processed',
        'intro' => 'Your change request for the field ":field" has been processed.',
        'approved' => 'Your request has been approved and the change has been applied.',
        'rejected' => 'Your request has been rejected.',
        'rejection_reason' => 'Reason: :reason',

    ],
];
