<?php

declare(strict_types=1);

return [
    'index' => [
        'page_title' => 'Jegyzőkönyvek',
        'heading' => 'Jegyzőkönyvek',
        'table' => [
            'header_title' => 'Cím',
            'header_date' => 'Dátum',
            'row' => [
                'view' => 'Betöltés',
                'edit' => 'Szerkesztés',
                'print' => 'Nyomtatás',
            ],
        ],
        'btn' => [
            'create' => 'Jegyzőkönyv létrehozása',
        ],
        'details' => [
            'heading' => 'Részletek',
        ],
    ],
    'pdf' => [
        'title' => 'Jegyzőkönyv',
        'error' => 'Hiba a jegyzőkönyv létrehozásakor.',
    ],
    'details' => [
        'date' => 'Dátum',
        'location' => 'Helyszín',
        'content' => 'Tartalom',
        'attendees' => 'Résztvevők',
        'no_attendees' => 'résztvevők nélkül',
        'topics' => 'Témák / Határozatok',
        'action_items' => 'Feladatok',
        'assigned_to' => 'Felelős',
        'due' => 'Határidő',
        'no_topics' => 'nincsenek témák',
        'select_meeting' => 'Jegyzőkönyv kiválasztása',
    ],
    'create' => [
        'page_title' => 'Jegyzőkönyv létrehozása',
        'heading' => 'Új jegyzőkönyv',
        'default_title' => 'Új találkozó',
        'title' => 'Cím',
        'meeting_date' => 'Találkozó dátuma',
        'meeting_date_placeholder' => 'Dátum kiválasztása',
        'location' => 'Helyszín',
        'content' => 'Tartalom',
        'save' => 'Jegyzőkönyv mentése',
        'success' => 'Jegyzőkönyv sikeresen elmentve!',
        'btn' => [
            'add_attendee' => 'Résztvevő hozzáadása',
        ],
        'attendees' => [
            'heading' => 'Résztvevők',
        ],
        'empty_attendee_list' => 'Nincs hozzáadva résztvevő.',
        'modal' => [
            'add_attendee' => [
                'header' => 'Résztvevő hozzáadása',
                'name' => 'Név',
                'email' => 'E-mail',
                'btn' => 'Hozzáadás',
                'select_member' => 'Tag kiválasztása',
                'no_member' => 'Nincs tag',
                'add_board' => 'Vezetőségi tagok',
            ],
            'add_action_item' => [
                'header' => 'Feladat hozzáadása',
                'description' => 'Leírás',
                'select_assignee' => 'Felelős kiválasztása',
                'no_assignee' => 'Nincs felelős',
                'due_date' => 'Határidő',
                'due_date_placeholder' => 'Határidő kiválasztása',
                'btn' => 'Hozzáadás',
            ],
        ],
        'topic' => [
            'heading' => 'Témák',
            'add' => 'Téma hozzáadása',
            'remove' => 'Eltávolítás',
            'placeholder' => 'Téma tartalmának megadása...',
            'empty_topics_list' => 'Nincs hozzáadva téma.',
        ],
        'actionitems' => [
            'heading' => 'Feladatok',
            'add' => 'hozzáadás',
            'remove' => 'Eltávolítás',
            'empty' => 'Nincs hozzáadva feladat',
            'no_assignee' => 'Nincs felelős',
        ],
        'validation_error' => [
            'title' => [
                'required' => 'A cím mező kitöltése kötelező.',
            ],
            'meeting_date' => [
                'required' => 'A dátum mező kitöltése kötelező.',
            ],
            'attendees' => [
                'required' => 'Legalább egy résztvevő megadása kötelező.',
                'min' => 'Legalább egy résztvevő megadása kötelező.',
                'duplicate' => 'A résztvevő már szerepel a listában.',
            ],
            'topics' => [
                'required' => 'Legalább egy téma megadása kötelező.',
                'min' => 'Legalább egy téma megadása kötelező.',
            ],
            'actionitems' => [
                'description' => [
                    'required' => 'A feladat leírásának megadása kötelező.',
                    'min' => 'A feladat leírásának legalább 3 karakter hosszúnak kell lennie.',
                ],
            ],
        ],
    ],
    'edit' => [
        'page_title' => 'Jegyzőkönyv szerkesztése',
    ],
];
