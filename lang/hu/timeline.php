<?php

declare(strict_types=1);


return [
    'start' => 'Kezdés',
    'duration' => 'Időtartam',
    'end' => 'Befejezés',
    'title' => 'Cím',
    'place' => 'Helyszín',
    'performer' => 'Előadó',
    'description' => 'Leírás',
    'notes' => 'Jegyzetek (belső)',
    'table' => [
        'header' => [
            'start' => 'Kezdés',
            'duration' => 'Időtartam',
            'end' => 'Befejezés',
            'title' => 'Cím',
            'member' => 'Moderátor',
            'place' => 'Helyszín',
            'performer' => 'Előadó',
        ],
    ],
    'modal' => [
        'heading' => [
            'new' => 'Új programpont létrehozása',
            'edit' => 'Programpont szerkesztése',
        ],
    ],
    'btn' => [
        'submit' => [
            'label' => 'Programpont mentése',
        ],
        'open-modal' => [
            'label' => 'Új programpont',
        ],
    ],
    'empty_list' => 'Nincsenek programpontok',
    'validation_error' => [
        'title' => [
            'required' => 'Kérlek, adj meg egy címet',
        ],
        'start' => [
            'required' => 'Kérlek, adj meg egy kezdési időpontot',
        ],
        'end' => [
            'required' => 'Kérlek, adj meg egy befejezési időpontot',
            'after' => 'A befejezésnek a kezdés után kell lennie ;)',
        ],
        'event_id' => [
            'required' => 'Nincs megadva esemény',
        ],
        'user_id' => [
            'required' => 'Mit keres ez itt????',
        ],
    ],
    'deletion_success' => [
        'header' => 'Siker!',
        'msg' => 'A programpont sikeresen törölve lett',
    ],
    'storing_success' => [
        'header' => 'Siker!',
        'msg' => 'A programpont sikeresen mentve lett',
    ],
    'title_extern_de' => 'Externer Cím de',
    'title_extern_hu' => 'Externer Cím hu',
];
