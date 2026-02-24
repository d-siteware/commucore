<?php

declare(strict_types=1);

return [
    'name' => [
        'required' => 'Please provide a name',
    ],
    'status' => 'Status',
    'event_date' => 'Date',
    'start_time' => 'Starts at',
    'end_time' => 'Ends at',
    'title' => [
        'de' => 'Title',
    ],
    'slug' => [
        'de' => 'slug',
    ],
    'description' => [
        'de' => 'Content',
    ],
    'excerpt' => [
        'de' => 'Excerpt',
    ],
    'image' => [
        'title' => 'Cover Image',
        'upload' => 'Cover image for the event',
    ],
    'entry_fee' => 'Admission',
    'entry_fee_discounted' => 'Reduced Admission',
    'venue_id' => 'Venue',
    'venue' => 'Location',
    'payment_link' => 'Payment Link',
    'more' => 'read more',
    'page' => [
        'title' => 'Overview of all Events',
    ],
    'date' => 'Date',
    'begins' => 'Begins',
    'ends' => 'Ends',
    'show' => [
        'label' => 'Details',
        'title' => 'Event',
        'page' => [
            'title' => 'Event',
        ],
        'timeline' => [
            'empty' => [
                'heading' => 'No program available yet',
                'message' => 'The program schedule has not been published yet. Feel free to sign up for our mailing list to stay updated.',
            ],
            'heading' => 'Program Schedule',
        ],
        'details' => [
            'heading' => 'Overview',
        ],
        'posts' => [
            'heading' => 'Articles',
            'poster' => [
                'heading' => 'Poster',
                'download' => 'Download PDF Poster',
            ],
            'content' => 'The following articles have been published for this event.',
        ],
        'btn' => [
            'link_to_post' => 'Read Article',
        ],
        'section' => [
            'published' => [
                'btn_publish_now' => 'Publish Event',
            ],
        ],
        'tab' => [
            'main' => [
                'published' => [
                    'confirmation_msg' => 'Please confirm the cancellation of the event',
                    'btn_reset' => 'Cancel Event',
                    'btn_sendMails' => 'Send Newsletter',
                    'btn_makeLetters' => 'Write Letter',
                    'header' => 'Event is published',
                    'status_msg' => 'This event has been published and is now visible.',
                    'sent_at' => 'sent :time',
                ],
            ],
        ],
    ],
    'make_ics' => 'Create Calendar Entry',
    'buy_tickets' => 'Buy Tickets Now',
    'upcoming' => [
        'title' => 'Upcoming Events',
    ],
    'recent' => [
        'title' => 'Past Events',
    ],
    'today' => [
        'title' => 'Today',
    ],
    'validation_error' => [
        'event_date' => [
            'required' => 'Please provide a date',
            'after' => 'The date must be in the future',
        ],
        'start_time' => [
            'required' => 'Please provide a start time',
        ],
        'end_time' => [
            'after' => 'The end should be after the start',
        ],
        'entry_fee' => '',
        'entry_fee_discounted' => '',
        'venue_id' => '',
        '' => '',
    ],
    'tabs' => [
        'nav' => [
            'dates' => 'Dates',
            'texts' => 'Texts',
            'payments' => 'Payments',
            'subscriptions' => 'Registrations',
            'visitors' => 'Visitors',
            'planing' => 'Planning',
        ],
    ],
    'visitor-table' => [
        'header' => [
            'name' => 'Name',
            'email' => 'Email',
            'gender' => 'Gender',
            'is_member' => 'Member',
            'is_subscriber' => 'Registrant',
        ],
    ],
    'subscribe' => 'Interested?',
    'tickets' => [
        'start' => [
            'label' => 'Reserve Tickets',
            'btn' => 'Reserve',
        ],
    ],
    'subscription' => [
        'text' => 'We are very pleased that you are interested in the event. For better planning, you can register using the form below. This gives us a better overview of the expected number of visitors.',
        'consent' => [
            'label' => 'Yes, please send me messages about this event.',
        ],
        'confirm_subscription_message' => 'Thank you! A confirmation email has been sent.',
        'submit-button' => [
            'label' => 'Follow Event',
        ],
        'subscribe-button' => [
            'label' => 'Announce Participation',
        ],
        'disclaimer' => [
            'header' => 'Important Notice',
            'body' => 'This data is used exclusively for planning the event and will be deleted after the event.',
        ],
        'mail' => [
            'text' => 'Please confirm your registration for the event by clicking on the following link:',
            'link' => [
                'label' => 'Confirm Now',
            ],
            'bring_a_guest' => 'We are glad that you want to bring :num guests.',
            'notification' => 'We will contact you if there are any changes',
            'ignore' => 'If you did not register, please ignore this email.',
        ],
        'title' => 'Participate in Event',
        'name' => 'Full Name',
        'email' => [
            'label' => 'Email Address',
            'confirmation' => [
                'heading' => 'Success',
                'text' => 'Thank you! Your participation is confirmed – we look forward to seeing you at the event soon.',
            ],
        ],
        'phone' => 'Phone or Mobile Number',
        'remarks' => 'Additional Remarks',
        'amountGuests' => 'Number of Additional Guests',
        'bringFriends' => 'I am bringing guests',
        'optional_section' => 'Additional Information',
    ],
    'backend' => [
        'subscription' => [
            'title' => 'Visitor Registration',
            'sendNotification' => [
                'label' => 'Send confirmation email to visitor',
            ],
            'consent' => [
                'label' => 'Add visitor to mailing list',
            ],
            'confirm_subscription_message' => 'A confirmation email was sent successfully.',
            'submit-button' => [
                'label' => 'Save Registration',
            ],
            'subscribe-button' => [
                'label' => 'Announce Participation',
            ],
        ],
        'text-nav' => [
            'btn-make-web-texts' => 'Create excerpt and slug for link',
            'btn-store' => 'Save Texts',
        ],
        'texts' => [
            'title_label' => 'Title for language',
            'title_description' => 'The title will be used for the page',
            'description_label' => 'Content/Description for language',
            'slug_label' => 'slug language',
            'slug_description' => 'The slug will be used as a link',
            'excerpt_label' => 'Text excerpt for language',
            'excerpt_description' => 'Used for preview. Please max 200 characters',
        ],
    ],
    'visitors' => [
        'empty_results_msg' => 'No visitors recorded yet',
        'search_placeholder' => 'Search visitors',
        'table' => [
            'paid' => 'Paid',
        ],
        'menu' => [
            'assign' => 'Assign',
            'assign_member' => 'Member',
            'assign_subscriber' => 'Registrant',
            'delete' => 'Delete',
        ],
    ],
    'visitor' => [
        'btn' => [
            'add' => [
                'label' => 'Add New Visitor',
            ],
        ],
    ],
    'visitor-modal' => [
        'heading' => 'Register Visitor',
        'select_member' => 'Link Member',
        'select_subscribers' => 'Link Registrant',
        'name' => 'Name, First Name',
        'email' => 'Email Address',
        'phone' => 'Phone',
        'gender' => 'Gender',
        'btn' => [
            'submit' => 'Save',
            'store' => 'Save + Create New',
        ],
        'separator' => [
            'values' => 'Information',
            'optional' => 'Optionally get data from',
            'or' => 'or',
        ],
        'toast' => [
            'msg' => 'Visitor successfully created',
            'heading' => 'Success',
        ],
    ],
    'email' => [
        'required' => 'We need your email address',
        'unique' => 'Check if you have already received an email from us.',
    ],
    'index' => [
        'title' => 'Title',
        'table' => [
            'header' => [
                'name' => 'Name (internal)',
                'title' => 'Title',
                'image' => 'Cover Image',
                'subscriptions' => 'Registrations',
            ],
        ],
        'btn' => [
            'start_new' => 'Create New',
            'generateList' => 'Generate Program',
        ],
    ],
    'create' => [
        'slug' => [
            'notice' => 'The slug is created as a link in two languages. This cannot be changed afterwards!',
        ],
        'page' => [
            'title' => 'Create New Event',
        ],
    ],
    'store' => [
        'success' => [
            'content' => 'The event was successfully created.',
            'title' => 'Success',
        ],
    ],
    'form' => [
        'name' => 'Name (internal)',
        'event_date' => 'Date',
        'start_time' => 'Starts at',
        'end_time' => 'Ends at',
        'title' => [
            'de' => 'Title',
        ],
        'slug' => [
            'de' => 'slug',
        ],
        'description' => [
            'de' => 'Content',
        ],
        'excerpt' => [
            'de' => 'Excerpt',
        ],
        'image' => [
            'title' => 'Cover Image',
            'upload' => 'Cover image for the event',
        ],
        'entry_fee' => 'Admission',
        'entry_fee_discounted' => 'Reduced Admission',
        'venue_id' => 'Venue',
        'venue' => [
            'select' => 'Choose venue',
            'new' => 'New',
        ],
        'status' => 'Status',
        'status_placeholder' => 'Status ...',
        'payment_link' => 'Payment Link',
        'btn' => [
            'save' => 'Save',
        ],
    ],
    'update' => [
        'success' => [
            'title' => 'Success',
            'content' => 'The event was successfully updated.',
        ],
    ],
    'delete_image' => [
        'success' => [
            'title' => 'Deletion successful',
            'content' => 'The cover image was successfully deleted.',
        ],
    ],
    'store_image' => [
        'success' => [
            'title' => 'Upload successful',
            'content' => 'The cover image was successfully saved and linked to the event.',
        ],
    ],
    'type' => [
        'label' => 'Status',
        'draft' => 'Draft',
        'pending' => 'Pending',
        'published' => 'Published',
        'rejected' => 'Rejected',
        'retracted' => 'Retracted',
    ],
    'assignments' => [
        'heading' => 'Tasks',
    ],
    'timeline' => [
        'heading' => 'Schedule',
        'title' => 'Item',
        'start' => 'Start',
        'end' => 'End',
        'place' => 'Location',
        'performer' => 'Performer',
        'type' => 'Review',
    ],
    'section' => [
        'published' => [
            'toast_success' => [
                'msg' => 'The event was successfully published.',
                'heading' => 'Success',
            ],
        ],
    ],
    'notification_mail' => [
        'subject' => 'New event on our website!',
        'header_subscriber' => 'New: An event for you',
        'header_member' => 'New: An event for you',
        'content_member' => 'Great news for you! A new event has been published on our website – we hope you\'ll check it out!',
        'content_subscriber' => 'Great news for you! A new event has been published on our website – take a look!',
        'btn_link_label' => 'Learn More',
        'btn_unsubscribe_link_label' => 'You are receiving this email because you subscribed to our updates. Want to change your settings or unsubscribe? Click here:',
        'content' => [
            'excerpt' => [
                'header' => 'Brief Description',
            ],
            'details' => [
                'header' => 'Appointment',
                'event_date' => 'Date',
                'start_time' => 'Start Time',
                'venue' => 'Venue',
            ],
        ],
    ],
    'poster' => [
        'separator' => [
            'text' => 'Create poster for event',
        ],
    ],
    'notification_letter' => [
        'title' => 'Invitation',
        'subject' => 'Invitation to our event',
        'greeting' => 'Dear :name,',
        'text' => 'We are pleased to inform you that an event will take place on :datum, to which we would like to invite you.',
        'overview' => 'Time and Place',
        'closing_text' => 'We hope you can attend and look forward to seeing you soon.',
        'signature' => 'With kind regards',
        'board' => 'The Board of Magyar Kolónia Berlin e. V.',
        'timelines' => [
            'header' => 'The following program is planned:',
            'empty' => 'No program items have been published yet.',
        ],
    ],
    'program_letter' => [
        'title' => 'Program Overview',
        'modal' => [
            'heading' => 'Filter Events',
            'text' => 'All published events are generated in a PDF list. The time filters determine which events are included in the document.',
            'radio' => [
                'year' => [
                    'label' => 'Current Year',
                    'desc' => 'All published events of the current year',
                ],
                'upcoming' => [
                    'label' => 'From Today',
                    'desc' => 'All future published events from today onwards',
                ],
                'all' => [
                    'label' => 'All',
                    'desc' => 'All past and future published events',
                ],
            ],
            'btn' => 'Create List',
        ],
    ],
    'boxoffice' => [
        'btn' => [
            'openmodal' => 'Box Office',
        ],
    ],
    'subscriptions' => [
        'btn' => [
            'add_new' => 'add new registration',
        ],
        'table' => [
            'name' => 'Name',
            'date' => 'Date',
            'email' => 'Email',
            'notifications' => 'Notifications',
            'phone' => 'Phone',
            'guests' => '# Guests',
            'confirmed_at' => 'Email confirmed at',
        ],
    ],
    'payments' => [
        'table' => [
            'text' => 'Text',
            'date' => 'Date',
            'visitor' => 'Visitor',
            'amount' => 'Amount',
        ],
        'btn' => [
            'add_new' => 'Record New Payment',
            'create_report' => 'Create Report',
        ],
    ],
    'modal' => [
        'resend_notification' => [
            'heading' => 'Please confirm',
            'text_1' => 'The notification was already sent on :date.',
            'text_2' => 'Should it be sent again?',
            'btn_cancel' => 'Not now',
            'btn_confirm' => 'Yes, please send again',
        ],
    ],
];