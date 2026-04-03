<?php

return [
    'status' => [
        'pending' => 'Függőben',
        'completed' => 'Jóváhagyva',
        'rejected' => 'Elutasítva',
    ],

    'modal' => [
        'title' => 'Módosítás kérése',
        'description' => 'Módosítási kérelmet küldhet a vezetőségnek. Az elbírálásról értesítést kap.',
        'submit' => 'Kérelem beküldése',
    ],

    'field' => [
        'label' => 'Módosítandó mező',
        'placeholder' => 'Válasszon mezőt…',
    ],

    'requested_value' => [
        'label' => 'Kért érték',
        'placeholder' => 'Adja meg a kívánt értéket…',
    ],

    'reason' => [
        'label' => 'Indoklás',
        'placeholder' => 'Kérjük, röviden indokolja meg a módosítás szükségességét…',
    ],

    'table' => [
        'pending_heading' => 'Nyitott kérelmek',
        'history_heading' => 'Előzmények',
        'empty' => 'Még nincsenek módosítási kérelmek.',
        'col' => [
            'field' => 'Mező',
            'requested_value' => 'Kért érték',
            'reason' => 'Indoklás',
            'status' => 'Státusz',
            'date' => 'Beküldve',
            'reviewed_by' => 'Elbíráló',
        ],
    ],

    'review' => [
        'empty' => 'Nincs függőben lévő módosítási kérelem.',
        'modal' => [
            'title' => 'Módosítási kérelem elbírálása',
            'old_value' => 'Jelenlegi érték',
            'requested_value' => 'Kért érték',
            'rejection_reason' => 'Elutasítás indoka',
            'rejection_reason_placeholder' => 'Kérjük, indokolja meg az elutasítást…',
            'rejection_reason_hint' => 'Csak elutasítás esetén szükséges.',
            'deduction_reason_placeholder' => 'Az díjkedvezmény indoklása…',
            'deduction_reason_hint' => 'Jóváhagyáskor kedvezmény indokaként kerül mentésre.',
        ],
    ],

    'btn' => [
        'review' => 'Elbírálás',
        'approve' => 'Jóváhagyás',
        'reject' => 'Elutasítás',
    ],

    'toast' => [
        'created' => [
            'heading' => 'Kérelem beküldve',
            'text' => 'A módosítási kérelmet továbbítottuk a vezetőségnek.',
        ],
        'duplicate' => [
            'heading' => 'Nyitott kérelem létezik',
            'text' => 'Ehhez a mezőhöz már van nyitott kérelem.',
        ],
        'approved' => [
            'heading' => 'Kérelem jóváhagyva',
            'text' => 'A módosítás érvénybe lépett.',
        ],
        'rejected' => [
            'heading' => 'Kérelem elutasítva',
            'text' => 'A módosítási kérelmet elutasították.',
        ],
    ],

    'notification' => [
        'subject' => 'Új módosítási kérelem',
        'intro' => ':member módosítási kérelmet küldött be a(z) ":field" mezőhöz.',
        'old_value' => 'Jelenlegi érték: :value',
        'requested_value' => 'Kért érték: :value',
        'reason' => 'Indoklás: :reason',
        'message' => ':member módosítást kért a(z) ":field" mezőhöz: :value',
    ],

    'reviewed_notification' => [
        'subject' => 'Módosítási kérelmét elbírálták',
        'intro' => 'A(z) ":field" mezőre vonatkozó módosítási kérelmét elbírálták.',
        'approved' => 'Kérelmét jóváhagyták, a módosítás érvénybe lépett.',
        'rejected' => 'Kérelmét elutasították.',
        'rejection_reason' => 'Indok: :reason',
    ],
];
