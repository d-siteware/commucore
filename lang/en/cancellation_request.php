<?php

declare(strict_types=1);

return [
    'trigger_btn' => 'Cancel membership',

    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'rejected' => 'Rejected',
    ],

    'modal' => [
        'title' => 'Cancel membership',
        'description' => 'Submit a cancellation request to the board. Your membership remains active until confirmation.',
        'submit' => 'Submit cancellation request',
        'warning' => [
            'heading' => 'Please note',
            'text' => 'The cancellation must be confirmed by the board before it takes effect. You will be informed of the decision.',
        ],
    ],

    'leave_date' => [
        'label' => 'Desired leave date',
        'description' => 'Optional. Leave blank for immediate cancellation.',
    ],

    'reason' => [
        'label' => 'Reason',
        'placeholder' => 'Please briefly state the reason for your cancellation…',
    ],

    'review' => [
        'empty' => 'No open cancellation requests.',
        'modal' => [
            'title' => 'Process cancellation request',
            'member' => 'Member',
            'leave_date_immediate' => 'Immediate (no date specified)',
            'rejection_reason_hint' => 'Only required for rejection.',
            'warning' => [
                'heading' => 'Caution',
                'text' => 'Upon approval, the leave date will be set and the membership will be terminated.',
            ],
        ],
    ],

    'toast' => [
        'created' => [
            'heading' => 'Request submitted',
            'text' => 'Your cancellation request has been forwarded to the board.',
        ],
        'duplicate' => [
            'heading' => 'Open request exists',
            'text' => 'There is already an open cancellation request for this membership.',
        ],
        'approved' => [
            'heading' => 'Cancellation confirmed',
            'text' => 'The membership has been terminated.',
        ],
        'rejected' => [
            'heading' => 'Request rejected',
            'text' => 'The cancellation request has been rejected.',
        ],
    ],

    'notification' => [
        'subject' => 'New cancellation request',
        'intro' => ':member has submitted a cancellation request.',
        'reason' => 'Reason: :reason',
        'leave_date' => 'Desired leave date: :date',
        'message' => ':member has submitted a cancellation request.',
    ],

    'reviewed_notification' => [
        'subject' => 'Your cancellation request has been processed',
        'confirmed' => 'Your cancellation request has been confirmed. Your membership will be terminated at the desired date.',
        'leave_date' => 'Leave date: :date',
        'rejected' => 'Your cancellation request has been rejected.',
        'rejection_reason' => 'Reason: :reason',
    ],
];
