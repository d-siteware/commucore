<?php

declare(strict_types=1);

return [
    'step' => [
        '01' => 'Organization',
        '02' => 'Settings',
        '03' => 'Invite team',
        '04' => 'Done',
    ],
    'org' => [
        'heading' => 'Organization',
        'subheading' => 'Basic information about your organization.',
        'org_name' => 'Organization name',
        'email' => 'Email',
        'website' => 'Website',
        'website_placeholder' => 'https://',
        'address' => 'Address',
        'zip' => 'ZIP code',
        'city' => 'City',
        'legal_heading' => 'Legal',
        'legal_subheading' => 'This information is used for receipts and reports.',
        'register_id' => 'Register number',
        'register_id_placeholder' => 'VR 12345',
        'registered_date' => 'Registered on',
        'court' => 'Local court',
        'tax_id' => 'Tax ID',
        'vat_id' => 'VAT ID',
        'vat_id_placeholder' => 'DE123456789',
    ],
    'settings' => [
        'fy_heading' => 'Fiscal year',
        'fy_subheading' => 'The starting year for accounting.',
        'fy_label' => 'Starting year',
        'locales_heading' => 'Languages',
        'locales_subheading' => 'Which languages should be active in your instance?',
        'locales_available' => 'available languages',
    ],
    'team' => [
        'profile_heading' => 'Your profile',
        'profile_subheading' => 'Complete your own information.',
        'surname' => 'Last name',
        'firstname' => 'First name',
        'username' => 'Username',
        'invite_heading' => 'Invite team',
        'invite_subheading' => 'Invite more people. Each invited person will automatically be created as a member — not every member automatically has a login.',
        'invite_name_placeholder' => 'Last name',
        'invite_firstname_placeholder' => 'First name',
        'invite_email_placeholder' => 'email@example.com',
        'add_more_btn' => 'Add more',
        'smtp_warning_heading' => 'Note',
        'smtp_warning_text' => 'Currently, all outgoing emails in this instance are written to the log and not sent. Please contact our helpdesk if you would like to use the email dispatch feature. Thank you!',
    ],
    'finish' => [
        'heading' => 'All set!',
        'subheading' => 'Your organization is set up. You can get started now.',
        'fiscal_year' => 'Fiscal year :year',
        'selected_locales' => 'Selected languages',
        'selected_locale' => 'Selected language',
        'invites_sent' => ':count invitation(s) will be sent',
        'btn_dashboard' => 'Go to dashboard',
    ],
    'btn' => [
        'next' => 'Next',
        'back' => 'Back',
    ],

    'validation' => [
        'active_locales' => [
            'required' => 'At least one language must be selected.',
            'min' => 'At least one language must be selected.',
        ],
    ],
];
