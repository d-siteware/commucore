<?php

return [
    'status' => [
        'pending' => 'Függőben',
        'confirmed' => 'Megerősítve',
        'rejected' => 'Elutasítva',
    ],

    'modal' => [
        'title' => 'Tagság lemondása',
        'description' => 'Lemondási kérelmet küldhet a vezetőségnek. A tagsága mindaddig aktív marad, amíg a kérelmet el nem fogadják.',
        'submit' => 'Lemondási kérelem beküldése',
        'warning' => [
            'heading' => 'Fontos tudnivaló',
            'text' => 'A lemondást a vezetőségnek jóvá kell hagynia, mielőtt hatályba lép. Az döntésről értesítést kap.',
        ],
    ],

    'leave_date' => [
        'label' => 'Kért kilépési dátum',
        'description' => 'Nem kötelező. Hagyja üresen, ha azonnali kilépést kér.',
    ],

    'reason' => [
        'label' => 'Indoklás',
        'placeholder' => 'Kérjük, röviden indokolja meg a lemondást…',
    ],

    'review' => [
        'empty' => 'Nincs függőben lévő lemondási kérelem.',
        'modal' => [
            'title' => 'Lemondási kérelem elbírálása',
            'member' => 'Tag',
            'leave_date_immediate' => 'Azonnali (nincs megadott dátum)',
            'rejection_reason_hint' => 'Csak elutasítás esetén szükséges.',
            'warning' => [
                'heading' => 'Figyelem',
                'text' => 'A kérelem jóváhagyásával beállítja a tag kilépési dátumát, és a tagság megszűnik.',
            ],
        ],
    ],

    'toast' => [
        'created' => [
            'heading' => 'Kérelem beküldve',
            'text' => 'A lemondási kérelmet továbbítottuk a vezetőségnek.',
        ],
        'duplicate' => [
            'heading' => 'Nyitott kérelem létezik',
            'text' => 'Ehhez a tagsághoz már van nyitott lemondási kérelem.',
        ],
        'approved' => [
            'heading' => 'Lemondás megerősítve',
            'text' => 'A tagság megszüntetésre került.',
        ],
        'rejected' => [
            'heading' => 'Kérelem elutasítva',
            'text' => 'A lemondási kérelmet elutasították.',
        ],
    ],

    'notification' => [
        'subject' => 'Új lemondási kérelem',
        'intro' => ':member lemondási kérelmet küldött be.',
        'reason' => 'Indoklás: :reason',
        'leave_date' => 'Kért kilépési dátum: :date',
        'message' => ':member lemondási kérelmet nyújtott be.',
    ],

    'reviewed_notification' => [
        'subject' => 'Lemondási kérelmét elbírálták',
        'confirmed' => 'Lemondási kérelmét megerősítették. A tagsága a kért időpontban megszűnik.',
        'leave_date' => 'Kilépési dátum: :date',
        'rejected' => 'Lemondási kérelmét elutasították.',
        'rejection_reason' => 'Indok: :reason',
    ],
];
