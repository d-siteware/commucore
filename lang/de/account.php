<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Übersicht Konten',
        'btn' => [
            'fetch_data' => 'Hole Kontodaten',
            'create_report' => 'Erstelle Bericht',
            'create_vcashcount' => 'Erstelle Zählliste',
        ],
    ],
    'cashcount' => [
        'delete' => [
            'heading' => 'Zählliste löschen',
            'label' => 'Bitte die Löschung der Zählliste :label bestätigen',
            'warning' => 'Die Löschung kann nicht rückgängig gemacht werden!',
            'btn' => [
                'cancel' => 'Abbrechen',
                'submit' => 'Löschen',
            ],
            'confirmationtoast' => [
                'head' => 'Erfolg',
                'txt' => 'Zählliste wurde erfolgreich gelöscht!',
            ],
        ],
        'create' => [
            'heading' => 'Neue Zählliste erstellen',
            'btn' => [
                'submit' => 'Erfassen',
            ],
        ],
        'edit' => [
            'heading' => 'Zählliste bearbeiten',
            'btn' => [
                'submit' => 'Aktualisieren',
            ],
        ],
    ],
];
