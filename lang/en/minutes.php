<?php

declare(strict_types=1);

return [
    'index' => [
        'page_title' => 'Minutes',
        'heading' => 'Minutes',
        'table' => [
            'header_title' => 'Title',
            'header_date' => 'Date',
            'row' => [
                'view' => 'View',
                'edit' => 'Edit',
                'print' => 'Print',
            ],
        ],
        'btn' => [
            'create' => 'Create minutes',
        ],
        'details' => [
            'heading' => 'Details',
        ],
    ],
    'pdf' => [
        'title' => 'Minutes',
        'error' => 'Error creating the minutes.',
    ],
    'details' => [
        'date' => 'Date',
        'location' => 'Location',
        'content' => 'Content',
        'attendees' => 'Attendees',
        'no_attendees' => 'without attendees',
        'topics' => 'Topics / Resolutions',
        'action_items' => 'Action Items',
        'assigned_to' => 'Assigned to',
        'due' => 'Due by',
        'no_topics' => 'no topics',
        'select_meeting' => 'Select minutes',
    ],
    'create' => [
        'page_title' => 'Create minutes',
        'heading' => 'New minutes',
        'default_title' => 'New meeting',
        'title' => 'Title',
        'meeting_date' => 'Date of meeting',
        'meeting_date_placeholder' => 'Select date',
        'location' => 'Location',
        'content' => 'Content',
        'save' => 'Save minutes',
        'success' => 'Minutes saved successfully!',
        'btn' => [
            'add_attendee' => 'Add attendee',
        ],
        'attendees' => [
            'heading' => 'Attendees',
        ],
        'empty_attendee_list' => 'No attendees added.',
        'modal' => [
            'add_attendee' => [
                'header' => 'Add attendee',
                'name' => 'Name',
                'email' => 'Email',
                'btn' => 'Add',
                'select_member' => 'Select member',
                'no_member' => 'No member',
                'add_board' => 'Board members',
            ],
            'add_action_item' => [
                'header' => 'Add action item',
                'description' => 'Description',
                'select_assignee' => 'Select assignee',
                'no_assignee' => 'No assignee',
                'due_date' => 'Due date',
                'due_date_placeholder' => 'Select due date',
                'btn' => 'Add',
            ],
        ],
        'topic' => [
            'heading' => 'Topics',
            'add' => 'Add topic',
            'remove' => 'Remove',
            'placeholder' => 'Enter topic content...',
            'empty_topics_list' => 'No topics added.',
        ],
        'actionitems' => [
            'heading' => 'Action items',
            'add' => 'add',
            'remove' => 'Remove',
            'empty' => 'No action items added',
            'no_assignee' => 'No assignee',
        ],
        'validation_error' => [
            'title' => [
                'required' => 'The title field is required.',
            ],
            'meeting_date' => [
                'required' => 'The date field is required.',
            ],
            'attendees' => [
                'required' => 'At least one attendee is required.',
                'min' => 'At least one attendee is required.',
                'duplicate' => 'This attendee is already in the list.',
            ],
            'topics' => [
                'required' => 'At least one topic is required.',
                'min' => 'At least one topic is required.',
            ],
            'actionitems' => [
                'description' => [
                    'required' => 'The action item description is required.',
                    'min' => 'The action item description must be at least 3 characters long.',
                ],
            ],
        ],
    ],
    'edit' => [
        'page_title' => 'Edit minutes',
    ],
];
