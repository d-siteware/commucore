<?php

declare(strict_types=1);


return [
    'index' => [
        'title' => '[EN] Übersicht Konten',
        'btn' => [
            'fetch_data' => '[EN] Hole Kontodaten',
            'create_report' => '[EN] Erstelle Bericht',
            'create_vcashcount' => '[EN] Erstelle Zählliste',
        ],
    ],
    'cashcount' => [
        'delete' => [
            'heading' => '[EN] Zählliste löschen',
            'label' => 'Please die Löschung der Zählliste :label bestätigen',
            'warning' => 'Tuee Löschung kann nicht rückgängig gemacht werden!',
            'btn' => [
                'cancel' => 'Cancel',
                'submit' => 'Delete',
            ],
            'confirmationtoast' => [
                'head' => 'Success',
                'txt' => '[EN] Zählliste wurde erfolgreich gelöscht!',
            ],
        ],
        'create' => [
            'heading' => 'Newe Zählliste erstellen',
            'btn' => [
                'submit' => '[EN] Erfassen',
            ],
        ],
        'edit' => [
            'heading' => '[EN] Zählliste bearbeiten',
            'btn' => [
                'submit' => 'Update',
            ],
        ],
    ],
];
