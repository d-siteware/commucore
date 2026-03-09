<?php

declare(strict_types=1);

return [

    // -------------------------------------------------------------------------
    // Buttons
    // -------------------------------------------------------------------------
    'btn' => [
        'upload' => 'Dokument hochladen',
        'save' => 'Speichern',
        'saving' => 'Wird gespeichert...',
        'cancel' => 'Abbrechen',
        'download' => 'Herunterladen',
    ],

    // -------------------------------------------------------------------------
    // Upload-Formular
    // -------------------------------------------------------------------------
    'upload' => [
        'title' => 'Dokumente hochladen',
        'file_label' => 'Dateien',
        'file_hint' => 'PDF, Word, Excel, Bilder oder .eml – max. 20 MB pro Datei',
        'label_field' => 'Bezeichnung',
        'label_placeholder' => 'z.B. Förderbescheid 2024',
        'notes_label' => 'Notiz',
        'loading' => 'Dateien werden vorbereitet...',
    ],

    // -------------------------------------------------------------------------
    // Kategorie
    // -------------------------------------------------------------------------
    'category' => [
        'label' => 'Kategorie',
        'placeholder' => 'Kategorie wählen (optional)',
    ],

    // -------------------------------------------------------------------------
    // Tabelle
    // -------------------------------------------------------------------------
    'table' => [
        'name' => 'Dateiname',
        'category' => 'Kategorie',
        'size' => 'Größe',
        'uploaded_by' => 'Hochgeladen von',
        'last_accessed' => 'Zuletzt geöffnet',
        'actions' => 'Aktionen',
    ],

    // -------------------------------------------------------------------------
    // Bestätigungen
    // -------------------------------------------------------------------------
    'confirm' => [
        'delete' => 'Dokument wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
    ],

    // -------------------------------------------------------------------------
    // Meldungen
    // -------------------------------------------------------------------------
    'empty' => 'Noch keine Dokumente vorhanden.',
    'upload_success' => ':count Dokument erfolgreich hochgeladen.|:count Dokumente erfolgreich hochgeladen.',
    'upload_partial_failure' => ':count Datei(en) konnten nicht gespeichert werden.',
    'delete_success' => 'Dokument wurde gelöscht.',

    // -------------------------------------------------------------------------
    // Fehler
    // -------------------------------------------------------------------------
    'errors' => [
        'unauthorized' => 'Keine Berechtigung für diese Aktion.',
        'file_not_found' => 'Datei nicht gefunden.',
        'no_files' => 'Bitte mindestens eine Datei auswählen.',
        'mime_not_allowed' => 'Dieser Dateityp ist für die gewählte Kategorie nicht erlaubt.',
        'upload_failed' => 'Beim Speichern ist ein Fehler aufgetreten.',
    ],
];
