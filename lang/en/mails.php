<?php

declare(strict_types=1);

return [
    'page_title' => 'Email Dispatch',

    'president' => [
        'deputy' => 'Vice President',
    ],
    'treasury' => 'Treasurer',
    'secretariat' => [
        'hu' => 'Hungarian Secretariat',
        'de' => 'German Secretariat',
    ],
    'cultural' => [
        'director' => 'Cultural Director',
    ],
    'social' => [
        'affairs' => [
            'deputy' => 'Deputy Social Affairs Director',
        ],
    ],
    'contact' => 'Contact',
    'invitation' => [
        'subject' => 'Invitation to the portal of :name',
        'greeting' => 'Hello :name',
        'header' => 'Please confirm your email address',
        'text' => 'As an active member of :name, we cordially invite you to register as a user on our portal.',
        'btn' => [
            'label' => 'Click here to complete your registration',
        ],
    ],
    'acceptance' => [
        'subject' => 'Approved membership application for :name',
        'greeting' => 'Hello :name',
        'header' => 'Welcome',
        'text' => 'We are pleased to inform you that your application for membership in :name has been reviewed and accepted.',
    ],
    'audit_invitation' => [
        'header' => 'We need you!',
        'text' => 'We invite you to review the monthly cash report for the period :range. You can either start the review by clicking on the link below or go to Cash -> Reports in the portal and click the "Review now" button on the corresponding report. Thank you for your efforts!',
    ],
    'audit' => [
        'invitation' => [
            'subject' => 'Review of the monthly cash report',
            'link_label' => 'Go to review',
        ],
    ],
    'members' => [
        'heading' => 'Email to all members with a registered email address',
        'content' => 'The email will be created in the language specified by each user in their profile.',
        'btn' => [
            'preview' => 'Preview (Without attachments)',
            'test_mail' => 'Test email to myself (Without attachments)',
            'submit' => 'Send',
            'cancel' => 'Cancel',
            'final' => 'Yes, send it',
        ],
        'subject' => 'Subject',
        'message' => 'Message',
        'label' => 'Link label',
        'url' => 'Link URL',
        'confirm' => [
            'header' => 'Please review carefully before sending',
            'warning' => 'Many members will receive this message. A mistake could have unpleasant consequences.',
            'info' => 'Before sending, a history entry will be made recording who sent which email and when.',
        ],
    ],
    'member' => [
        'separator' => [
            'text' => 'Content',
            'links' => 'Links',
            'attachments' => 'Attachments (only pdf|jpg|jpeg|png|tif)',
            'options' => 'Options',
        ],
    ],
    'mailing_list' => [
        'label' => [
            'email' => 'Email address',
        ],
        'text' => [
            'privacy' => 'I agree that my data will be stored and processed in accordance with applicable data protection laws.',
            'privacy_full' => 'Your data will be used exclusively for notifications about events and articles and will not be shared with third parties.',
        ],
        'btn_subscribe' => [
            'label' => 'Subscribe to list',
        ],
        'header' => 'Receive notifications about new events and articles from :name',
        'options_group_header' => 'Topic selection',
        'options_header' => 'Settings',
        'options' => [
            'all_label' => 'Everything!',
            'events_label' => 'Events only',
            'posts_label' => 'Articles only',
            'all_description' => 'Receive notifications as soon as a new event or article is published or changed.',
            'events_description' => 'Enable this if you only want to receive messages about new events.',
            'posts_description' => 'Enable this if you only want to receive messages about new articles.',
            'update_notifications_label' => 'Updates',
            'update_notifications_description' => 'Also send a notification for updates to an event or article',
        ],
        'validation_error' => [
            'email' => 'Please enter an email address',
            'terms_accepted' => 'Please accept the privacy policy',
        ],
        'show' => [
            'confirmation_msg' => 'Your email address has been successfully verified',
            'update_msg' => 'Your settings have been changed successfully',
            'change' => 'Change your selection to receive notifications for these topics in the future.',
            'btn' => [
                'save' => 'Save selection',
            ],
        ],
        'confirmation_email_subject' => 'Please verify your email address',
        'confirmation_email_msg' => 'Thank you for signing up for our mailing list! Please confirm your subscription by clicking the button below. This way you will receive updates that match your interests.',
        'confirmation_email_msg_changes' => 'You can update your settings at any time via a link included in future emails.',
        'confirmation_email_msg_ignore' => 'If you did not sign up, simply ignore this email.',
        'confirmation_email' => [
            'selected_summary' => 'These settings apply to your email address:',
            'selected_events' => 'Receive notifications about new events',
            'selected_posts' => 'Receive notifications about new articles',
            'selected_notifications' => 'Also receive notifications about changes',
            'locale' => 'Language in which notifications should be written',
            'btn' => [
                'verify_now' => 'Verify email address',
            ],
        ],
        'subscription_success' => 'Thank you! A verification email has been sent',
        'verify' => [
            'header' => 'Please confirm your email address',
            'btn' => 'Confirm',
        ],
        'unsubscribe' => [
            'label' => 'Unsubscribe',
            'error_heading' => 'Unexpected error',
            'error_msg' => 'Unfortunately, your email address could not be deleted due to an unexpected error. We apologize for the inconvenience. The system has reported the error and we are already working on a solution. We will notify you once the deletion has been successfully completed. Until then, we ask for your understanding regarding any further notifications you may receive.',
            'success_msg' => 'Your email address has been successfully removed from our list. You will no longer receive notifications from us.',
        ],
        'verified_emails' => 'Verified email addresses',
    ],
    'unsubscribe_link_label' => 'Change settings / unsubscribe',
    'toast' => [
        'header' => [
            'success' => 'Success',
        ],
        'text' => [
            'sent' => 'The email has been sent to :count recipients!',
        ],
    ],
    'tab' => [
        'create' => 'Create',
        'history' => 'History',
        'external_list' => 'External list',
    ],
    'tool' => [
        'options_heading' => 'Email dispatch options',
        'reason' => 'Reason for writing',
        'new_event' => 'New event',
        'new_article' => 'New article',
        'change' => 'Change to article/event',
        'include_external_list' => 'Include external mailing list',
        'include_external_list_desc' => 'If enabled, a link will be added at the end of the email leading to the respective page.',
        'create_link' => 'Create link',
        'create_link_desc' => 'If enabled, a link will be added at the end of the email leading to the respective page.',
        'personal_greeting' => 'Personal greeting',
        'personal_greeting_desc' => 'If enabled, the recipient will be addressed by name. If disabled, no greeting will be created!',
        'attachments' => 'Email attachments',
        'attachments_desc' => 'Should files be attached to the email?',
    ],
    'attached_file' => 'Attached file',
    'empty_mailing_list' => 'No verified entries in the mailing list',
    'mailing_list_subscriptions' => [
        'new_in_month' => 'new registrations in :month',
        'one_in_month' => 'One registration in :month',
        'none_in_month' => 'No new registrations in :month',
        'new_in_year' => 'new registrations in :year',
        'one_in_year' => 'One new registration in :year',
        'none_in_year' => 'No new registrations in :year',
    ],
    'mailing_list_unsubscribe_greeting' => 'Best regards / Viszlát',
    'history_heading' => 'Sent mailings',
    'history_description' => 'Documentation of all mass emails sent so far.',
    'history_empty' => 'No mailings sent yet.',
    'history_recipients_total' => 'Total recipients',
    'history_members' => 'Members',
    'history_mailing_list' => 'Mailing list',
    'history_attachments' => 'Attachments',
    'history_attachments_label' => 'Attached files (only filenames stored)',
    'history_sender' => 'Sender',
    'history_included_mailing_list' => 'Mailing list included',
    'history_personal_greeting_enabled' => 'Personal greeting',
    'history_attachments_enabled' => 'Attachments enabled',
    'footer_greeting' => 'Best regards,',
    'subscription_footer_greeting' => 'Best regards,',
];
