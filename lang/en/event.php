<?php

declare(strict_types=1);

return [
    'name' => [
        'required' => 'Please enter a name',
    ],
    'status' => [
        'label' => 'Status',
        'draft' => 'draft',
        'pending' => 'pending',
        'published' => 'published',
        'rejected' => 'rejected',
        'retracted' => 'retracted',

    ],
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
        'title' => 'Cover image',
        'upload' => 'Cover image for the event',
    ],
    'entry_fee' => 'Entry fee',
    'entry_fee_discounted' => 'Reduced entry fee',
    'venue_id' => 'Venue',
    'venue' => 'Venue',
    'payment_link' => 'Payment link',
    'more' => 'read more',
    'page' => [
        'title' => 'Overview of all events',
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
                'heading' => 'No programme available yet',
                'message' => 'The schedule has not been published yet. Feel free to sign up for our mailing list to stay up to date.',
            ],
            'heading' => 'Schedule',
        ],
        'details' => [
            'heading' => 'Overview',
        ],
        'posts' => [
            'heading' => 'Articles',
            'poster' => [
                'heading' => 'Poster',
                'download' => 'Download PDF poster',
            ],
            'content' => 'The following articles have been published for this event.',
        ],
        'btn' => [
            'link_to_post' => 'Read article',
        ],
        'section' => [
            'published' => [
                'btn_publish_now' => 'Publish event',
            ],
        ],
        'tab' => [
            'main' => [
                'published' => [
                    'confirmation_msg' => 'Please confirm the cancellation of the event',
                    'btn_reset' => 'Cancel event',
                    'btn_sendMails' => 'Send mass mail',
                    'btn_makeLetters' => 'Write circular letter',
                    'header' => 'Event is published',
                    'status_msg' => 'This event has been published and is now visible.',
                    'sent_at' => 'sent :time',
                ],
            ],
        ],
    ],
    'make_ics' => 'Create calendar entry',
    'buy_tickets' => 'Buy tickets now',
    'upcoming' => [
        'title' => 'Upcoming events',
    ],
    'recent' => [
        'title' => 'Past events',
    ],
    'today' => [
        'title' => 'Today',
    ],
    'validation_error' => [
        'event_date' => [
            'required' => 'Please enter a date',
            'after' => 'The date must be in the future',
        ],
        'start_time' => [
            'required' => 'Please enter a start time',
        ],
        'end_time' => [
            'after' => 'The end time must be after the start time',
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
            'poster' => 'Poster',
            'payments' => 'Payments',
            'subscriptions' => 'Subscriptions',
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
            'is_subscriber' => 'Subscriber',
        ],
    ],
    'subscribe' => 'Interested?',
    'tickets' => [
        'start' => [
            'label' => 'Reserve tickets',
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
            'label' => 'Follow event',
        ],
        'subscribe-button' => [
            'label' => 'Announce participation',
        ],
        'disclaimer' => [
            'header' => 'Important notice',
            'body' => 'This data is used exclusively for planning the event and will be deleted after the event has concluded.',
        ],
        'mail' => [
            'text' => 'Please confirm your registration for the event by clicking on the following link:',
            'link' => [
                'label' => 'Confirm now',
            ],
            'bring_a_guest' => 'We are pleased that you would like to bring :num guests.',
            'notification' => 'We will get in touch if there are any changes',
            'ignore' => 'If you did not register, please ignore this email.',
        ],
        'title' => 'Attend event',
        'name' => 'Full name',
        'email' => [
            'label' => 'Email address',
            'confirmation' => [
                'heading' => 'Success',
                'text' => 'Thank you! Your participation is confirmed – we look forward to seeing you at the event.',
            ],
        ],
        'phone' => 'Phone or mobile number',
        'remarks' => 'Additional remarks',
        'amountGuests' => 'Number of additional guests',
        'bringFriends' => 'I am bringing guests',
        'optional_section' => 'Additional information',
    ],
    'backend' => [
        'subscription' => [
            'title' => 'Visitor registration',
            'sendNotification' => [
                'label' => 'Send confirmation email to visitor',
            ],
            'consent' => [
                'label' => 'Add visitor to mailing list',
            ],
            'confirm_subscription_message' => 'A confirmation email has been sent successfully.',
            'submit-button' => [
                'label' => 'Save registration',
            ],
            'subscribe-button' => [
                'label' => 'Announce participation',
            ],
        ],
        'text-nav' => [
            'btn-make-web-texts' => 'Create excerpt and slug for link',
            'btn-store' => 'Save texts',
        ],
        'texts' => [
            'title_label' => 'Title for language',
            'title_description' => 'The title is used for the page',
            'description_label' => 'Content/Description for language',
            'slug_label' => 'slug language',
            'slug_description' => 'The slug is used as a link',
            'excerpt_label' => 'Text excerpt for language',
            'excerpt_description' => 'Used for the preview. Please max 200 characters',
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
            'assign_subscriber' => 'Subscriber',
            'delete' => 'Delete',
        ],
    ],
    'visitor' => [
        'label' => 'Visitor',
        'name' => 'Visitor name',
        'btn' => [
            'add' => [
                'label' => 'Record new visitor',
            ],
        ],
    ],
    'visitor-modal' => [
        'heading' => 'Register visitor',
        'select_member' => 'Link member',
        'select_subscribers' => 'Link subscriber',
        'name' => 'Surname, First name',
        'email' => 'Email address',
        'phone' => 'Phone',
        'gender' => 'Gender',
        'btn' => [
            'submit' => 'Save',
            'store' => 'Save + Create new',
        ],
        'separator' => [
            'values' => 'Details',
            'optional' => 'Optional: get data from',
            'or' => 'or',
        ],
        'toast' => [
            'msg' => 'Visitor created successfully',
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
                'image' => 'Cover image',
                'subscriptions' => 'Subscriptions',
            ],
        ],
        'btn' => [
            'start_new' => 'Create new',
            'generateList' => 'Generate programme',
        ],
    ],
    'create' => [
        'slug' => [
            'notice' => 'The slug will be created as a link in two languages. It cannot be changed afterwards!',
        ],
        'page' => [
            'title' => 'Create new event',
        ],
    ],
    'store' => [
        'success' => [
            'content' => 'The event has been created successfully.',
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
            'title' => 'Cover image',
            'upload' => 'Cover image for the event',
        ],
        'entry_fee' => 'Entry fee',
        'entry_fee_discounted' => 'Reduced entry fee',
        'venue_id' => 'Venue',
        'venue' => [
            'select' => 'Select venue',
            'new' => 'New',
        ],
        'status' => 'Status',
        'status_placeholder' => 'Status ...',
        'payment_link' => 'Payment link',
        'content' => 'Content/Description',
        'btn' => [
            'save' => 'Save',
        ],
    ],
    'update' => [
        'success' => [
            'title' => 'Success',
            'content' => 'The event has been updated successfully.',
        ],
    ],
    'delete_image' => [
        'success' => [
            'title' => 'Deletion successful',
            'content' => 'The cover image has been deleted successfully.',
        ],
    ],
    'store_image' => [
        'success' => [
            'title' => 'Upload successful',
            'content' => 'The cover image has been saved and linked to the event successfully.',
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
        'place' => 'Place',
        'performer' => 'Performer',
        'type' => 'Review',
    ],
    'section' => [
        'published' => [
            'toast_success' => [
                'msg' => 'The event has been published successfully.',
                'heading' => 'Success',
            ],
        ],
    ],
    'notification_mail' => [
        'subject' => 'New event on our website!',
        'header_subscriber' => 'Newly published: An event for you',
        'header_member' => 'Newly published: An event for you',
        'content_member' => 'Great news for you! A new event has been published on our website – we look forward to seeing you there!',
        'content_subscriber' => 'Great news for you! A new event has been published on our website – come check it out!',
        'btn_link_label' => 'Learn more',
        'btn_unsubscribe_link_label' => 'You are receiving this email because you subscribed to our updates. Would you like to change your settings or unsubscribe? Click here:',
        'content' => [
            'excerpt' => [
                'header' => 'Short description',
            ],
            'details' => [
                'header' => 'Date',
                'event_date' => 'Date',
                'start_time' => 'Start time',
                'venue' => 'Venue',
            ],
        ],
    ],
    'poster' => [
        'separator' => [
            'text' => 'Create poster for event',
        ],
        'option' => [
            'image' => 'Show cover image',
            'text' => 'Text',
            'text_excerpt' => 'Short text',
            'text_full' => 'Long text',
            'preview_locale' => 'Preview language',
        ],
        'generate' => 'Generate poster',
        'generate_jpeg' => 'Generate JPEG',
        'generate_pdf' => 'Generate PDF',
        'preview' => 'Preview',
        'jpeg_files' => 'JPEG posters',
        'pdf_files' => 'PDF posters',
        'confirm_delete' => 'Really delete poster?',
    ],
    'notification_letter' => [
        'title' => 'Invitation',
        'subject' => 'Invitation to our event',
        'greeting' => 'Dear :name,',
        'text' => 'we are pleased to inform you that an event will take place on :datum, to which we cordially invite you.',
        'overview' => 'Time and venue',
        'closing_text' => 'We hope you can attend and look forward to seeing you soon.',
        'signature' => 'With kind regards',
        'board' => 'The board of the Magyar Kolónia Berlin e. V.',
        'timelines' => [
            'header' => 'The following programme is planned:',
            'empty' => 'No programme items have been published yet.',
        ],
    ],
    'program_letter' => [
        'title' => 'Programme overview',
        'modal' => [
            'heading' => 'Filter events',
            'text' => 'All published events will be generated in a PDF list. The time filters determine which events are included in the document.',
            'radio' => [
                'year' => [
                    'label' => 'Current year',
                    'desc' => 'All published events of the current year',
                ],
                'upcoming' => [
                    'label' => 'From today',
                    'desc' => 'All future published events from today onwards',
                ],
                'all' => [
                    'label' => 'All',
                    'desc' => 'All past and future published events',
                ],
            ],
            'btn' => 'Create list',
        ],
    ],
    'boxoffice' => [
        'btn' => [
            'openmodal' => 'Box office',
        ],
    ],
    'subscriptions' => [
        'btn' => [
            'add_new' => 'add new subscription',
        ],
        'table' => [
            'name' => 'Name',
            'date' => 'Date',
            'email' => 'Email',
            'notifications' => 'Notifications',
            'phone' => 'Phone',
            'guests' => '# Guests',
            'confirmed_at' => 'Email confirmed on',
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
            'add_new' => 'Record new payment',
            'create_report' => 'Create report',
        ],
    ],
    'modal' => [
        'resend_notification' => [
            'heading' => 'Please confirm',
            'text_1' => 'The notification was already sent on :date.',
            'text_2' => 'Should it be sent again?',
            'btn_cancel' => 'Cancel',
            'btn_confirm' => 'Yes, please resend',
        ],
    ],

    'report' => [
        'title' => 'Event Report',
        'summary' => 'Summary',
        'finances' => 'Finances',
        'income' => 'Income',
        'expenses' => 'Expenses',
        'total' => 'Total',
        'visitors' => 'Visitors',
        'visitors_total' => 'Total number of recorded visitors',
        'visitors_male' => 'Total male',
        'visitors_female' => 'Total female',
        'members' => 'Members',
        'subscribed_online' => 'Subscribed via website',
        'details' => 'Details',
        'details_income' => 'Income',
        'details_expenses' => 'Expenses',
        'details_visitors' => 'Visitors',
        'text' => 'Text',
        'reference' => 'Reference',
        'status' => 'Status',
        'account' => 'Account',
        'amount' => 'Amount',
        'name' => 'Name',
        'email' => 'Email',
        'legend_member' => 'M: Visitor is a member',
        'legend_subscribed' => 'S: Visitor has subscribed',
        'legend_male' => 'M: Visitor is male',
        'legend_female' => 'F: Visitor is female',
    ],
    'boxoffice' => [
        'ticket_count' => 'Number of tickets purchased',
        'select_account' => 'Select cash register',
        'select_booking_account' => 'Select account',
    ],
    'payment' => [
        'date' => 'Date',
        'type' => 'Booking',
        'account_placeholder' => 'Payment account e.g. cash, bank account, etc.',
        'booking_account_placeholder' => 'SKR account',
        'label' => 'Text / Purpose',
        'entry_fee' => 'Entry fee',
        'entry_fee_discounted' => 'Discounted entry fee',
        'member_list_placeholder' => 'Member list',
        'external' => 'External',
        'btn_store' => 'Record payment',
    ],
];
