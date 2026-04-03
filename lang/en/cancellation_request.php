<?php

return [
    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'rejected' => 'Rejected',
    ],

    'modal' => [
        'title' => 'Cancel membership',
        'description' => 'Submit a cancellation request to the board. Your membership will remain active until the request has been confirmed.',
        'submit' => 'Submit cancellation request',
        'warning' => [
            'heading' => 'Please note',
            'text' => 'Cancellation must be confirmed by the board before it takes effect. You will be notified of the decision.',
        ],
    ],

    'leave_date' => [
        'label' => 'Requested leave date',
        'description' => 'Optional. Leave blank to request immediate cancellation.',
    ],

    'reason' => [
        'label' => 'Reason',
        'placeholder' => 'Please briefly explain your reason for cancelling…',
    ],

    'review' => [
        'empty' => 'No pending cancellation requests.',
        'modal' => [
            'title' => 'Review cancellation request',
            'member' => 'Member',
            'leave_date_immediate' => 'Immediate (no date specified)',
            'rejection_reason_hint' => 'Only required when rejecting.',
            'warning' => [
                'heading' => 'Attention',
                'text' => 'Approving this request will set the member\'s leave date and mark the membership as cancelled.',
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
            'text' => 'The membership has been cancelled.',
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
        'leave_date' => 'Requested leave date: :date',
        'message' => ':member submitted a cancellation request.',
    ],

    'reviewed_notification' => [
        'subject' => 'Your cancellation request has been reviewed',
        'confirmed' => 'Your cancellation request has been confirmed. Your membership will be ended as requested.',
        'leave_date' => 'Leave date: :date',
        'rejected' => 'Your cancellation request has been rejected.',
        'rejection_reason' => 'Reason: :reason',
    ],
];
