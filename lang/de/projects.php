<?php

declare(strict_types=1);

return [

    'page' => [
        'title' => 'Projekte',
    ],

    'index' => [
        'search_placeholder' => 'Projekt suchen...',
        'btn' => [
            'create' => 'Neues Projekt',
        ],
        'table' => [
            'title' => 'Titel',
            'status' => 'Status',
            'start_date' => 'Start',
            'end_date' => 'Ende',
            'fundings' => 'Förderungen',
            'transactions' => 'Buchungen',
        ],
    ],

    'create' => [
        'page' => [
            'title' => 'Neues Projekt',
        ],
        'btn' => [
            'submit' => 'Projekt anlegen',
        ],
        'success' => [
            'title' => 'Projekt angelegt',
            'content' => 'Das Projekt wurde erfolgreich gespeichert.',
        ],
    ],

    'show' => [
        'title' => 'Projekt:',
        'page' => [
            'title' => 'Projekt',
        ],
        'toast' => [
            'updated' => 'Projekt gespeichert.',
        ],
    ],

    'reports' => [
        'actions' => [
            'executive' => 'Executive Summary erstellen',
            'detailed' => 'Detailbericht erstellen',
        ],
        'toast' => [
            'created' => 'Der Projektbericht wurde erstellt und bei den Dokumenten abgelegt.',
        ],
    ],

    'form' => [
        'title' => 'Titel',
        'description' => 'Beschreibung / Notizen',
        'status' => 'Status',
        'start_date' => 'Startdatum',
        'end_date' => 'Enddatum',
        'btn' => [
            'save' => 'Speichern',
            'delete' => 'Löschen',
        ],
        'confirm' => [
            'delete' => 'Projekt wirklich löschen? Dies kann nicht rückgängig gemacht werden.',
        ],
    ],

    'tabs' => [
        'details' => 'Details',
        'financials' => 'Finanzen',
        'fundings' => 'Förderungen',
        'posts' => 'Blog',
        'documents' => 'Dokumente',
    ],

    'financials' => [
        'income' => 'Einnahmen',
        'expense' => 'Ausgaben',
        'balance' => 'Saldo',
        'empty' => 'Noch keine Buchungen erfasst.',
        'table' => [
            'date' => 'Datum',
            'label' => 'Bezeichnung',
            'type' => 'Art',
            'allocated' => 'Anteil',
            'amount' => 'Betrag',
            'full_amount' => 'Vollbetrag',
        ],
    ],

    'fundings' => [
        'stat' => [
            'allocated' => 'Fördermittel zugeteilt',
            'expense' => 'Projektausgaben',
            'coverage' => 'Deckungsgrad',
        ],
        'table' => [
            'title' => 'Förderung',
            'funder' => 'Fördergeber',
            'status' => 'Status',
            'allocated' => 'Zugeteilt',
        ],
        'empty' => 'Noch keine Förderungen verknüpft.',
    ],

    'posts' => [
        'btn' => ['create' => 'Neuer Beitrag'],
        'table' => [
            'title' => 'Titel',
            'author' => 'Autor',
            'status' => 'Status',
            'published_at' => 'Veröffentlicht',
        ],
        'empty' => 'Noch keine Beiträge vorhanden.',
    ],

    'link_funding' => [
        'btn' => ['open' => 'Förderung verknüpfen'],
        'heading' => [
            'new' => 'Förderung verknüpfen',
            'edit' => 'Zuteilung bearbeiten',
        ],
        'form' => [
            'funding' => 'Förderung',
            'funding_placeholder' => 'Förderung auswählen...',
            'allocated_amount' => 'Zugeteilt (gemäß Bescheid)',
            'allocated_amount_hint' => 'Betrag laut Förderbescheid für dieses Projekt.',
            'editing_hint' => 'Betrag der Zuteilung ändern.',
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
            'attached' => 'Förderung erfolgreich verknüpft.',
            'updated' => 'Zuteilung aktualisiert.',
            'detached' => 'Verknüpfung wurde gelöst.',
        ],
        'error' => [
            'already_linked' => 'Diese Förderung ist bereits verknüpft.',
            'invalid_amount' => 'Bitte einen gültigen Betrag größer als 0 eingeben.',
            'exceeds_remaining' => 'Betrag überschreitet den verfügbaren Rest (:remaining).',
        ],
    ],

    'status' => [
        'planned' => 'Geplant',
        'active' => 'Aktiv',
        'completed' => 'Abgeschlossen',
        'cancelled' => 'Abgebrochen',
    ],

    'documents' => [
        'category' => [
            'planning' => 'Planung / Konzept',
            'contract' => 'Vertrag',
            'report' => 'Sachbericht / Abschlussbericht',
            'invoice' => 'Rechnung / Kostenaufstellung',
            'correspondence' => 'Briefverkehr / E-Mails',
            'photo' => 'Fotos / Dokumentation',
            'other' => 'Sonstiges',
        ],
    ],

];
