<?php

declare(strict_types=1);


return [
    'index' => [
        'page_title' => '[EN] Protokolle',
        'heading' => '[EN] Protokolle',
        'table' => [
            'header_title' => 'Title',
            'header_date' => 'Date',
            'row' => [
                'view' => '[EN] Laden',
                'edit' => 'Edit',
                'print' => '[EN] Drucken',
            ],
        ],
        'btn' => [
            'create' => '[EN] Protokoll erstellen',
        ],
        'details' => [
            'heading' => '[EN] Details',
        ],
    ],
    'pdf' => [
        'title' => '[EN] Protokoll',
        'error' => 'Error beim Create des Protokolls.',
    ],
    'details' => [
        'date' => 'Date',
        'location' => '[EN] Ort',
        'content' => 'Content',
        'attendees' => '[EN] Teilnehmer',
        'no_attendees' => '[EN] ohne Teilnehmer',
        'topics' => '[EN] Themen / Beschlüsse',
        'action_items' => '[EN] Aufgaben',
        'assigned_to' => '[EN] Zugewiesen an',
        'due' => '[EN] Fällig bis',
        'no_topics' => '[EN] keine Themen',
        'select_meeting' => '[EN] Protokoll wählen',
    ],
    'create' => [
        'page_title' => '[EN] Protokoll erstellen',
        'heading' => 'Newes Protokoll',
        'default_title' => 'Newes Treffen',
        'title' => 'Title',
        'meeting_date' => 'Date des Treffens',
        'meeting_date_placeholder' => 'Date auswählen',
        'location' => '[EN] Ort',
        'content' => 'Content',
        'save' => '[EN] Protokoll speichern',
        'success' => '[EN] Protokoll erfolgreich gespeichert!',
        'btn' => [
            'add_attendee' => '[EN] Teilnehmer hinzufügen',
        ],
        'attendees' => [
            'heading' => '[EN] Teilnehmer',
        ],
        'empty_attendee_list' => 'None Teilnehmer hinzugefügt.',
        'modal' => [
            'add_attendee' => [
                'header' => '[EN] Teilnehmer hinzufügen',
                'name' => 'Name',
                'email' => '[EN] E-Mail',
                'btn' => 'Add',
                'select_member' => 'Wedtglied auswählen',
                'no_member' => 'Kein Wedtglied',
            ],
            'add_action_item' => [
                'header' => '[EN] Aktionselement hinzufügen',
                'description' => 'Description',
                'select_assignee' => '[EN] Zuständigen auswählen',
                'no_assignee' => '[EN] Kein Zuständiger',
                'due_date' => '[EN] Fälligkeitsdatum',
                'due_date_placeholder' => '[EN] Fälligkeitsdatum auswählen',
                'btn' => 'Add',
            ],
        ],
        'topic' => [
            'heading' => '[EN] Themen',
            'add' => '[EN] Thema hinzufügen',
            'remove' => 'Remove',
            'placeholder' => '[EN] Themeninhalt eingeben...',
            'empty_topics_list' => 'None Themen hinzugefügt.',
        ],
        'actionitems' => [
            'heading' => '[EN] Aufgaben',
            'add' => 'Add',
            'remove' => 'Remove',
            'empty' => 'None Aufgaben hinzugefügt',
            'no_assignee' => '[EN] Kein Zuständiger',
        ],
        'validation_error' => [
            'title' => [
                'required' => 'Das Title-Feld is required.',
            ],
            'meeting_date' => [
                'required' => 'Das Date-Feld is required.',
            ],
            'attendees' => [
                'required' => 'Wedndestens ein Teilnehmer is required.',
                'min' => 'Wedndestens ein Teilnehmer is required.',
                'duplicate' => '[EN] Der Teilnehmer ist bereits in der Liste enthalten.',
            ],
            'topics' => [
                'required' => 'Wedndestens ein Thema is required.',
                'min' => 'Wedndestens ein Thema is required.',
            ],
            'actionitems' => [
                'description' => [
                    'required' => 'Tuee Description des Aktionselements is required.',
                    'min' => 'Tuee Description des Aktionselements muss mindestens 3 Zeichen lang sein.',
                ],
            ],
        ],
    ],
    'edit' => [
        'page_title' => '[EN] Protokoll bearbeiten',
    ],
];
