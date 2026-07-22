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

    'reports' => [
        'actions' => [
            'executive' => 'Executive Summary erstellen',
            'detailed' => 'Detailbericht erstellen',
            'statusbericht' => 'Statusbericht erstellen',
        ],
        'toast' => [
            'created' => 'Der Förderbericht wurde erstellt und bei den Dokumenten abgelegt.',
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
        'positions' => 'Positionen',
        'documents' => 'Dokumente',
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
    // -------------------------------------------------------------------------
    // Tab: Positionen (Plan/Ist je Förderposition)
    // -------------------------------------------------------------------------
    'positions' => [
        'btn' => [
            'create' => 'Position anlegen',
        ],
        'table' => [
            'title' => 'Position',
            'category' => 'Kategorie',
            'budget' => 'Plan',
            'actual' => 'Ist',
            'remaining' => 'Rest',
            'due_date' => 'Fällig',
            'responsible' => 'Verantwortlich',
        ],
        'empty' => 'Noch keine Positionen angelegt. Lege Positionen an, um Plan-Budgets gegen tatsächliche Ausgaben zu verfolgen.',
        'menu' => [
            'edit' => 'Bearbeiten',
            'delete' => 'Löschen',
            'delete_confirm' => 'Position wirklich löschen? Die Zuordnung der Buchungen geht verloren.',
        ],
        'modal' => [
            'heading_create' => 'Position anlegen',
            'heading_edit' => 'Position bearbeiten',
        ],
        'form' => [
            'title' => 'Titel',
            'budget' => 'Plan-Budget (brutto)',
            'budget_hint' => 'Geplantes Budget laut Zuwendungsbescheid für diese Position.',
            'category' => 'Kategorie',
            'category_placeholder' => 'Keine Kategorie',
            'responsible' => 'Verantwortliche Person',
            'responsible_placeholder' => 'Niemand zugeordnet',
            'due_date' => 'Fälligkeitsdatum',
            'description' => 'Beschreibung / Notizen',
            'btn' => [
                'save' => 'Speichern',
            ],
        ],
        'toast' => [
            'saved' => 'Position gespeichert.',
            'deleted' => 'Position gelöscht.',
        ],
        'warning' => [
            'budget_exceeded' => [
                'heading' => 'Positions-Budgets übersteigen die Bewilligung',
                'text' => 'Die Summe der Positions-Budgets (:sum) liegt über dem bewilligten Betrag (:approved). Bitte prüfe die Planung gegen den Bescheid.',
            ],
        ],
        'categories' => [
            'heading' => 'Kategorien verwalten',
            'system_badge' => 'System',
            'new_label' => 'Eigene Kategorie',
            'new_placeholder' => 'Name der Kategorie...',
            'btn' => [
                'add' => 'Hinzufügen',
            ],
            'delete_confirm' => 'Eigene Kategorie wirklich löschen? Positionen behalten ihre Daten, verlieren aber die Kategorie.',
            'toast' => [
                'created' => 'Kategorie angelegt.',
                'deleted' => 'Kategorie gelöscht.',
            ],
            'error' => [
                'duplicate' => 'Eine Kategorie mit diesem Namen existiert bereits.',
                'system_readonly' => 'System-Kategorien können nicht geändert oder gelöscht werden.',
            ],
        ],
    ],

    'documents' => [
        'category' => [
            'approval_notice' => 'Förderbescheid',
            'usage_proof' => 'Verwendungsnachweis',
            'correspondence' => 'Briefverkehr / E-Mails',
            'contract' => 'Vertrag / Vereinbarung',
            'report' => 'Sachbericht',
            'other' => 'Sonstiges',
        ],
    ],

];
