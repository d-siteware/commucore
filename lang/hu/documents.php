<?php

declare(strict_types=1);

return [

    // -------------------------------------------------------------------------
    // Buttons
    // -------------------------------------------------------------------------
    'btn' => [
        'upload' => 'Dokumentum feltöltése',
        'save' => 'Mentés',
        'saving' => 'Mentés...',
        'cancel' => 'Mégse',
        'download' => 'Letöltés',
    ],

    // -------------------------------------------------------------------------
    // Upload-Formular
    // -------------------------------------------------------------------------
    'upload' => [
        'title' => 'Dokumentumok feltöltése',
        'file_label' => 'Fájlok',
        'file_hint' => 'PDF, Word, Excel, képek vagy .eml – max. 20 MB fájlonként',
        'label_field' => 'Megnevezés',
        'label_placeholder' => 'Pl. támogatási határozat 2024',
        'notes_label' => 'Megjegyzés',
        'loading' => 'Fájlok előkészítése...',
        'drag_hint' => 'Húzd ide a fájlokat a feltöltéshez, vagy kattints',
    ],

    // -------------------------------------------------------------------------
    // Kategorie
    // -------------------------------------------------------------------------
    'category' => [
        'label' => 'Kategória',
        'placeholder' => 'Válassz kategóriát (opcionális)',
    ],

    // -------------------------------------------------------------------------
    // Tabelle
    // -------------------------------------------------------------------------
    'table' => [
        'name' => 'Fájlnév',
        'category' => 'Kategória',
        'size' => 'Méret',
        'uploaded_by' => 'Feltöltötte',
        'last_accessed' => 'Utoljára megnyitva',
        'actions' => 'Műveletek',
    ],

    // -------------------------------------------------------------------------
    // Bestätigungen
    // -------------------------------------------------------------------------
    'confirm' => [
        'delete' => 'Valóban törlöd a dokumentumot? Ez a művelet nem vonható vissza.',
    ],

    // -------------------------------------------------------------------------
    // Meldungen
    // -------------------------------------------------------------------------
    'empty' => 'Még nincsenek dokumentumok.',
    'upload_success' => ':count dokumentum sikeresen feltöltve.|:count dokumentum sikeresen feltöltve.',
    'upload_partial_failure' => ':count fájlt nem sikerült elmenteni.',
    'delete_success' => 'Dokumentum törölve.',

    // -------------------------------------------------------------------------
    // Fehler
    // -------------------------------------------------------------------------
    'errors' => [
        'unauthorized' => 'Nincs jogosultságod ehhez a művelethez.',
        'file_not_found' => 'A fájl nem található.',
        'no_files' => 'Kérlek, válassz ki legalább egy fájlt.',
        'mime_not_allowed' => 'Ez a fájltípus nem engedélyezett a kiválasztott kategóriában.',
        'upload_failed' => 'Hiba történt a mentés során.',
    ],
];
