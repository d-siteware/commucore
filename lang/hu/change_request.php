<?php

return [
    'status' => [
        'pending' => 'Függőben',
        'completed' => 'Jóváhagyva',
        'rejected' => 'Elutasítva',
    ],

    'modal' => [
        'title' => 'Változtatás kérése',
        'description' => 'Küldj változtatási kérelmet az elnökségnek. Értesítünk, amint feldolgozták.',
        'submit' => 'Kérelem beküldése',
    ],

    'field' => [
        'label' => 'Módosítandó mező',
        'placeholder' => 'Mező kiválasztása…',
    ],

    'requested_value' => [
        'label' => 'Kívánt érték',
        'placeholder' => 'Kívánt érték megadása…',
    ],

    'reason' => [
        'label' => 'Indoklás',
        'placeholder' => 'Kérjük, indokold röviden, miért van szükség erre a változtatásra…',
    ],

    'table' => [
        'pending_heading' => 'Nyitott kérelmek',
        'history_heading' => 'Előzmények',
        'empty' => 'Még nincsenek változtatási kérelmek.',
        'col' => [
            'field' => 'Mező',
            'requested_value' => 'Kívánt érték',
            'reason' => 'Indoklás',
            'status' => 'Státusz',
            'date' => 'Beküldve',
            'reviewed_by' => 'Kezelte',
        ],
    ],

    'review' => [
        'empty' => 'Nincsenek nyitott változtatási kérelmek.',
        'modal' => [
            'title' => 'Változtatási kérelem kezelése',
            'old_value' => 'Jelenlegi érték',
            'requested_value' => 'Kívánt érték',
            'rejection_reason' => 'Elutasítás indoka',
            'rejection_reason_placeholder' => 'Kérjük, indokold az elutasítást…',
            'rejection_reason_hint' => 'Csak elutasítás esetén kötelező.',
            'deduction_reason_placeholder' => 'A kedvezmény indoklása…',
            'deduction_reason_hint' => 'Jóváhagyás esetén kedvezmény indokaként kerül mentésre.',
        ],
    ],

    'btn' => [
        'review' => 'Kezelés',
        'approve' => 'Jóváhagyás',
        'reject' => 'Elutasítás',
    ],

    'toast' => [
        'created' => [
            'heading' => 'Kérelem beküldve',
            'text' => 'A változtatási kérelmedet továbbítottuk az elnökségnek.',
        ],
        'duplicate' => [
            'heading' => 'Már van nyitott kérelem',
            'text' => 'Ehhez a mezőhöz már van egy nyitott kérelmed.',
        ],
        'approved' => [
            'heading' => 'Kérelem jóváhagyva',
            'text' => 'A változtatás érvénybe lépett.',
        ],
        'rejected' => [
            'heading' => 'Kérelem elutasítva',
            'text' => 'A változtatási kérelmet elutasították.',
        ],
    ],

    'notification' => [
        'subject' => 'Új változtatási kérelem',
        'intro' => ':member változtatási kérelmet nyújtott be a ":field" mezőhöz.',
        'old_value' => 'Jelenlegi érték: :value',
        'requested_value' => 'Kívánt érték: :value',
        'reason' => 'Indoklás: :reason',
        'message' => ':member változtatást kért a ":field" mezőhöz: :value',
    ],

    'reviewed_notification' => [
        'subject' => 'A változtatási kérelmedet feldolgozták',
        'intro' => 'A ":field" mezőhöz tartozó változtatási kérelmedet feldolgozták.',
        'approved' => 'A kérelmedet jóváhagyták és a változtatás érvénybe lépett.',
        'rejected' => 'A kérelmedet elutasították.',
        'rejection_reason' => 'Indoklás: :reason',

    ],
];
