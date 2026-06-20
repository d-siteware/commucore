<?php

declare(strict_types=1);

return [
    'name' => 'Name',
    'address' => 'Adresse',
    'postal_code' => 'Postleitzahl',
    'city' => 'Stadt',
    'country' => 'Land',
    'phone' => 'Telefon',
    'website' => 'Website',
    'geolocation' => 'Geolocation',

    'geolocation.more' => 'mehr',
    'geolocation.hint' => 'Die Eingabe des Codes in Google Maps führt einen Zeiger auf den Ort, um eine Navigation starten zu können.',

    'new.heading' => 'Neuer Veranstaltungsort',
    'new.btn.label' => 'Neu',
    'edit.heading' => 'Veranstaltungsort bearbeiten',
    'edit.btn.label' => 'Bearbeiten',

    'form.save_only' => 'Nur speichern',
    'form.save_and_apply' => 'Speichern + Übernehmen',

    'toast.created.heading' => 'Veranstaltungsort angelegt',
    'toast.created.text' => 'Der Veranstaltungsort wurde erfolgreich gespeichert.',
    'toast.updated.heading' => 'Veranstaltungsort aktualisiert',
    'toast.updated.text' => 'Die Änderungen wurden erfolgreich gespeichert.',
    'toast.deleted.heading' => 'Gelöscht',
    'toast.deleted.text' => 'Der Veranstaltungsort wurde entfernt.',

    'tool' => [
        'actions' => 'Aktionen',
        'heading' => 'Veranstaltungsorte verwalten',
        'create' => 'Neuer Veranstaltungsort',
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
        'search_placeholder' => 'Nach Name, Ort oder Adresse suchen…',
        'events_count' => 'Verwendet',
        'empty' => 'Noch keine Veranstaltungsorte angelegt.',

        'delete_confirm' => [
            'heading' => 'Veranstaltungsort löschen?',
            'text' => ':name wird unwiderruflich gelöscht.',
            'in_use_heading' => 'Achtung: Wird noch verwendet',
            'in_use_text' => ':name wird noch in :count Veranstaltung verwendet. Beim Löschen bleibt der Eintrag in bestehenden Veranstaltungen erhalten, kann aber nicht mehr neu zugewiesen werden.|:name wird noch in :count Veranstaltungen verwendet. Beim Löschen bleibt der Eintrag in bestehenden Veranstaltungen erhalten, kann aber nicht mehr neu zugewiesen werden.',
            'cancel' => 'Abbrechen',
            'confirm' => 'Endgültig löschen',
        ],
    ],
];
