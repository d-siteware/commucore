<?php

return [
    'trigger_btn' => 'Tagság lemondása',

    'status' => [
        'pending' => 'Függőben',
        'confirmed' => 'Jóváhagyva',
        'rejected' => 'Elutasítva',
    ],

    'modal' => [
        'title' => 'Tagság lemondása',
        'description' => 'Nyújts be felmondási kérelmet az elnökségnek. A tagságod a jóváhagyásig aktív marad.',
        'submit' => 'Felmondási kérelem benyújtása',
        'warning' => [
            'heading' => 'Kérjük, vedd figyelembe',
            'text' => 'A felmondást az elnökségnek jóvá kell hagynia, mielőtt hatályba lépne. Értesítünk a döntésről.',
        ],
    ],

    'leave_date' => [
        'label' => 'Kívánt kilépési dátum',
        'description' => 'Opcionális. Hagyd üresen az azonnali kilépéshez.',
    ],

    'reason' => [
        'label' => 'Indoklás',
        'placeholder' => 'Kérjük, röviden indokold meg a felmondásod…',
    ],

    'review' => [
        'empty' => 'Nincsenek nyitott felmondási kérelmek.',
        'modal' => [
            'title' => 'Felmondási kérelem szerkesztése',
            'member' => 'Tag',
            'leave_date_immediate' => 'Azonnal (nincs dátum megadva)',
            'rejection_reason_hint' => 'Csak elutasítás esetén kötelező.',
            'warning' => [
                'heading' => 'Figyelem',
                'text' => 'A jóváhagyással a kilépési dátum beállításra kerül és a tagság megszűnik.',
            ],
        ],
    ],

    'toast' => [
        'created' => [
            'heading' => 'Kérelem benyújtva',
            'text' => 'A felmondási kérelmedet továbbítottuk az elnökségnek.',
        ],
        'duplicate' => [
            'heading' => 'Nyitott kérelem van',
            'text' => 'Ehhez a tagsághoz már van egy nyitott felmondási kérelem.',
        ],
        'approved' => [
            'heading' => 'Felmondás jóváhagyva',
            'text' => 'A tagság megszűnt.',
        ],
        'rejected' => [
            'heading' => 'Kérelem elutasítva',
            'text' => 'A felmondási kérelmet elutasították.',
        ],
    ],

    'notification' => [
        'subject' => 'Új felmondási kérelem',
        'intro' => ':member felmondási kérelmet nyújtott be.',
        'reason' => 'Indoklás: :reason',
        'leave_date' => 'Kívánt kilépési dátum: :date',
        'message' => ':member felmondási kérelmet nyújtott be.',
    ],

    'reviewed_notification' => [
        'subject' => 'A felmondási kérelmedet feldolgozták',
        'confirmed' => 'A felmondási kérelmedet jóváhagyták. A tagságod a kívánt időpontban megszűnik.',
        'leave_date' => 'Kilépési dátum: :date',
        'rejected' => 'A felmondási kérelmedet elutasították.',
        'rejection_reason' => 'Indoklás: :reason',
    ],
];
