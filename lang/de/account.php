<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Übersicht Konten',
        'title_no_state' => 'Konto auswählen',
        'btn' => [
            'fetch_data' => 'Hole Kontodaten',
            'create_report' => 'Erstelle Bericht',
            'create_vcashcount' => 'Erstelle Zählliste',
            'create_account' => 'Neues Konto erstellen',
        ],
    ],
    'area' => [

        'ideal' => [
            'label' => 'Ideeller Bereich',
            'description' => 'Vereinsarbeit',
        ],

        'asset_management' => [
            'label' => 'Vermögensverwaltung',
            'description' => 'Zinsen, Vermietung',
        ],
        'purpose_operation' => [
            'label' => 'Zweckbetrieb',
            'description' => 'Vereinsveranstaltungen',
        ],
        'economic_business' => [
            'label' => 'Wirtschaftsbetrieb',
            'description' => 'Verkauf, Gastronomie',
        ],
    ],
    'dashboard' => [
        'heading' => 'Kassenjahr :year',
        'transactions' => [
            'title' => 'Buchungen',
            'columns' => [
                'label' => 'Bezeichnung',
                'amount' => 'Betrag',
            ],
            'btn' => [
                'overview' => 'Übersicht',
                'create' => 'Buchung Einreichen',
                'create_short' => 'Einreichen',
            ],
        ],
        'sections' => [
            'balance_sheet' => 'Kontenübersicht',
            'cash_counts' => 'Kassenzählungen',
        ],
        'reports' => [
            'title' => 'Berichte',
            'columns' => [
                'period' => 'Zeitraum',
                'status' => 'Status',
            ],
            'btn' => [
                'print' => 'drucken',
            ],
        ],
    ],
    'cashcount' => [
        'heading' => 'Übersicht',
        'dated' => 'vom',
        'empty_state' => 'Keine Zählungen erfasst',
        'btn' => [
            'delete' => 'löschen',
            'edit' => 'bearbeiten',
        ],
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
    'balance_sheet' => [
        'total' => 'Gesamter Kontostand',
        'dated' => 'Stand',
    ],

];
