<?php

declare(strict_types=1);

return [
    'index' => [
        'page_title' => 'Protokolle',
        'heading' => 'Protokolle',
        'table' => [
            'header_title' => 'Titel',
            'header_date' => 'Datum',
            'row' => [
                'view' => 'Laden',
                'edit' => 'Bearbeiten',
                'print' => 'Drucken',
            ],
        ],
        'btn' => [
            'create' => 'Protokoll erstellen',
        ],
        'details' => [
            'heading' => 'Details',
        ],
    ],
    'pdf' => [
        'title' => 'Protokoll',
        'error' => 'Fehler beim Erstellen des Protokolls.',
    ],
    'details' => [
        'date' => 'Datum',
        'location' => 'Ort',
        'content' => 'Inhalt',
        'attendees' => 'Teilnehmer',
        'no_attendees' => 'ohne Teilnehmer',
        'topics' => 'Themen / Beschlüsse',
        'action_items' => 'Aufgaben',
        'assigned_to' => 'Zugewiesen an',
        'due' => 'Fällig bis',
        'no_topics' => 'keine Themen',
        'select_meeting' => 'Protokoll wählen',
    ],
    'create' => [
        'page_title' => 'Protokoll erstellen',
        'heading' => 'Neues Protokoll',
        'default_title' => 'Neues Treffen',
        'title' => 'Titel',
        'meeting_date' => 'Datum des Treffens',
        'meeting_date_placeholder' => 'Datum auswählen',
        'location' => 'Ort',
        'content' => 'Inhalt',
        'save' => 'Protokoll speichern',
        'success' => 'Protokoll erfolgreich gespeichert!',
        'btn' => [
            'add_attendee' => 'Teilnehmer hinzufügen',
        ],
        'attendees' => [
            'heading' => 'Teilnehmer',
        ],
        'empty_attendee_list' => 'Keine Teilnehmer hinzugefügt.',
        'modal' => [
            'add_attendee' => [
                'header' => 'Teilnehmer hinzufügen',
                'name' => 'Name',
                'email' => 'E-Mail',
                'btn' => 'Hinzufügen',
                'select_member' => 'Mitglied auswählen',
                'no_member' => 'Kein Mitglied',
                'add_board' => 'Vorstandsmitglieder',
            ],
            'add_action_item' => [
                'header' => 'Aktionselement hinzufügen',
                'description' => 'Beschreibung',
                'select_assignee' => 'Zuständigen auswählen',
                'no_assignee' => 'Kein Zuständiger',
                'due_date' => 'Fälligkeitsdatum',
                'due_date_placeholder' => 'Fälligkeitsdatum auswählen',
                'btn' => 'Hinzufügen',
            ],
        ],
        'topic' => [
            'heading' => 'Themen',
            'add' => 'Thema hinzufügen',
            'remove' => 'Entfernen',
            'placeholder' => 'Themeninhalt eingeben...',
            'empty_topics_list' => 'Keine Themen hinzugefügt.',
        ],
        'actionitems' => [
            'heading' => 'Aufgaben',
            'add' => 'hinzufügen',
            'remove' => 'Entfernen',
            'empty' => 'Keine Aufgaben hinzugefügt',
            'no_assignee' => 'Kein Zuständiger',
        ],
        'validation_error' => [
            'title' => [
                'required' => 'Das Titel-Feld ist erforderlich.',
            ],
            'meeting_date' => [
                'required' => 'Das Datum-Feld ist erforderlich.',
            ],
            'attendees' => [
                'required' => 'Mindestens ein Teilnehmer ist erforderlich.',
                'min' => 'Mindestens ein Teilnehmer ist erforderlich.',
                'duplicate' => 'Der Teilnehmer ist bereits in der Liste enthalten.',
            ],
            'topics' => [
                'required' => 'Mindestens ein Thema ist erforderlich.',
                'min' => 'Mindestens ein Thema ist erforderlich.',
            ],
            'actionitems' => [
                'description' => [
                    'required' => 'Die Beschreibung des Aktionselements ist erforderlich.',
                    'min' => 'Die Beschreibung des Aktionselements muss mindestens 3 Zeichen lang sein.',
                ],
            ],
        ],
    ],
    'edit' => [
        'page_title' => 'Protokoll bearbeiten',
    ],
];
