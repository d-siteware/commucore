<?php

declare(strict_types=1);

return [

    // -------------------------------------------------------------------------
    // Allgemein (Index Page nutzt 'funding.page.title')
    // -------------------------------------------------------------------------
    'page' => [
        'title' => 'Förderungen',
    ],

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------
    'index' => [
        'search_placeholder' => 'Förderung oder Fördergeber suchen...',
        'ongoing' => 'laufend',
        'btn' => [
            'create' => 'Neue Förderung',
        ],
        'table' => [
            'title' => 'Titel',
            'funder' => 'Fördergeber',
            'status' => 'Status',
            'approved_amount' => 'Bewilligt',
            'period' => 'Laufzeit',
            'projects' => 'Projekte',
        ],
    ],

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------
    'create' => [
        'page' => [
            'title' => 'Neue Förderung',
        ],
        'btn' => [
            'submit' => 'Förderung anlegen',
        ],
        'success' => [
            'title' => 'Förderung angelegt',
            'content' => 'Die Förderung wurde erfolgreich gespeichert.',
        ],
    ],

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------
    'show' => [
        'title' => 'Förderung:',
        'page' => [
            'title' => 'Förderung',
        ],
        'toast' => [
            'updated' => 'Förderung gespeichert.',
        ],
    ],

    // -------------------------------------------------------------------------
    // Form (genutzt in Show + Create)
    // -------------------------------------------------------------------------
    'form' => [
        'title' => 'Titel',
        'funder' => 'Fördergeber',
        'reference' => 'Aktenzeichen / Referenz',
        'reference_hint' => 'Internes Aktenzeichen des Fördergebers',
        'status' => 'Status',
        'description' => 'Beschreibung / Notizen',
        'approved_amount' => 'Bewilligter Betrag',
        'period_start' => 'Förderbeginn',
        'period_end' => 'Förderende',
        'btn' => [
            'save' => 'Speichern',
            'delete' => 'Löschen',
        ],
        'confirm' => [
            'delete' => 'Förderung wirklich löschen? Dies kann nicht rückgängig gemacht werden.',
        ],
    ],

    // -------------------------------------------------------------------------
    // Tabs
    // -------------------------------------------------------------------------
    'tabs' => [
        'details' => 'Details',
        'receipts' => 'Zahlungseingänge',
        'projects' => 'Projekte',
    ],

    // -------------------------------------------------------------------------
    // Tab: Zahlungseingänge
    // -------------------------------------------------------------------------
    'receipts' => [
        'stat' => [
            'approved' => 'Bewilligt',
            'received' => 'Eingegangen',
            'remaining' => 'Ausstehend',
        ],
        'table' => [
            'date' => 'Datum',
            'label' => 'Bezeichnung',
            'allocated' => 'Anteil',
            'amount' => 'Betrag',
            'full_amount' => 'Vollbetrag',
        ],
        'empty' => 'Noch keine Zahlungseingänge erfasst.',
    ],

    // -------------------------------------------------------------------------
    // Tab: Projekte (Verwendungsnachweis)
    // -------------------------------------------------------------------------
    'projects' => [
        'stat' => [
            'approved' => 'Bewilligt',
            'allocated' => 'Auf Projekte verteilt',
            'unallocated' => 'Nicht verteilt',
        ],
        'table' => [
            'title' => 'Projekt',
            'status' => 'Status',
            'period' => 'Zeitraum',
            'allocated' => 'Zugeteilt',
        ],
        'empty' => 'Noch keine Projekte verknüpft.',
    ],

    // -------------------------------------------------------------------------
    // Status (genutzt von FundingStatus::label())
    // -------------------------------------------------------------------------
    'status' => [
        'applied' => 'Beantragt',
        'approved' => 'Bewilligt',
        'active' => 'Aktiv',
        'completed' => 'Abgeschlossen',
        'rejected' => 'Abgelehnt',
    ],

    // -------------------------------------------------------------------------
    // Verknüpfung von Projekten
    // -------------------------------------------------------------------------
    'link_project' => [
        'btn' => ['open' => 'Projekt verknüpfen'],
        'heading' => [
            'new' => 'Projekt verknüpfen',
            'edit' => 'Zuteilung bearbeiten',
        ],
        'form' => [
            'project' => 'Projekt',
            'project_placeholder' => 'Projekt auswählen...',
            'allocated_amount' => 'Zugeteilt (gemäß Bescheid)',
            'allocated_amount_hint' => 'Betrag laut Förderbescheid für dieses Projekt.',
            'editing_hint' => 'Betrag der Zuteilung ändern.',
            'remaining_hint' => 'Noch verfügbar aus dieser Förderung',
            'btn' => [
                'attach' => 'Verknüpfen',
                'update' => 'Aktualisieren',
            ],
        ],
        'menu' => [
            'edit' => 'Betrag bearbeiten',
            'detach' => 'Verknüpfung lösen',
            'detach_confirm' => 'Verknüpfung wirklich lösen? Der zugeteilte Betrag geht verloren.',
        ],
        'success' => [
            'attached' => 'Projekt erfolgreich verknüpft.',
            'updated' => 'Zuteilung aktualisiert.',
            'detached' => 'Verknüpfung wurde gelöst.',
        ],
        'error' => [
            'already_linked' => 'Dieses Projekt ist bereits verknüpft.',
            'invalid_amount' => 'Bitte einen gültigen Betrag größer als 0 eingeben.',
            'exceeds_remaining' => 'Betrag überschreitet den verfügbaren Rest (:remaining).',
        ],
    ],

];
