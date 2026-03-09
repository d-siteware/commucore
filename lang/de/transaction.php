<?php

declare(strict_types=1);

return [
    'documents' => [
        'heading' => 'Dokumente',
        'category' => [
            'label' => 'Kategorie',
            'invoice' => 'Rechnung',
            'receipt' => 'Quittung',
            'bank_statement' => 'Bankauszug',
            'contract' => 'Vertrag',
            'other' => 'Sonstiges',
        ],
        'drag_hint' => 'Dateien hierher ziehen oder klicken zum Auswählen',
    ],
    'edit-text-modal' => [
        'heading' => 'Buchungstexte ändern',
        'label' => 'Label',
        'reference' => 'Referenz',
        'description' => 'Beschreibung',
        'btn' => [
            'label' => 'Speichern',
        ],
        'update-success' => [
            'text' => 'Die Texte wurden erfolgreich aktualisiert',
            'heading' => 'Erfolg!',
        ],
    ],
    'detach-member-success' => [
        'text' => 'Die Verknüpfung der Buchung mit dem Mitglied wurde erfolgreich gelöscht',
        'heading' => 'Erfolg',
    ],
    'attach-member-success' => [
        'text' => 'Die Verknüpfung der Buchung mit dem Mitglied wurde erfolgreich erstellt',
        'heading' => 'Erfolg',
    ],
    'attach-event-success' => [
        'heading' => 'Erfolg',
        'text' => 'Die Verknüpfung der Buchung mit der Veranstaltung wurde erfolgreich erstellt',
    ],
    'detach-event-success' => [
        'text' => 'Die Verknüpfung der Buchung mit der Veranstaltung wurde erfolgreich gelöscht',
        'heading' => 'Erfolg',
    ],
    'access' => [
        'denied' => 'Sie haben keine Berechtigungen Buchungen zu verwalten: ',
    ],
    'cancel-transaction-modal' => [
        'reason' => [
            'label' => 'Grund für Stornierung angeben',
            'error' => 'Es muss eine Begründung für die Stornierung angegegen werden!',
        ],
        'heading' => 'Buchung stornieren',
        'btn' => [
            'submit' => [
                'label' => 'Stornieren',
            ],
        ],
    ],
    'index' => [
        'title' => 'Übersicht der Buchungen',
        'menu-item' => [
            'book' => 'Buchen',
            'edit' => 'Bearbeiten',
            'cancel' => 'Storno',
            'edit_text' => 'Texte ändern',
            'rebook' => 'Umbuchen',
            'attach_event' => 'Veranstaltung',
            'attach_member' => 'Mitglied',
            'detach_event' => 'Veranstaltung',
            'detach_member' => 'Mitglied',
            'send_invoice' => 'E-Mail senden',
            'print_invoice' => 'Ausdrucken',
            'attach_project' => 'Projekt zuordnen',
            'detach_project' => 'Projekt entfernen',
            'attach_funding' => 'Förderung zuordnen',
            'detach_funding' => 'Förderung entfernen',
        ],
        'menu-group' => [
            'booking' => 'Buchung',
            'receipt' => 'Quittung',
        ],
        'menu-submenu' => [
            'assign' => 'Zuweisen',
            'detach' => 'Lösen',
        ],
        'table' => [
            'empty-results' => 'Keine Buchungen gefunden',
            'columns' => [
                'booking' => 'Buchung',
                'date' => 'Erfolgt am',
                'created' => 'Eingereicht',
                'status' => 'Status',
                'account' => 'Konto',
                'amount' => 'Betrag [EUR]',
                'type' => 'Art',
                'receipt' => 'Beleg',
                'linked' => 'Verknüpft',
            ],
            'tooltip' => [
                'reference' => 'Referenz',
                'description' => 'Beschreibung',
                'event_assigned' => 'Veranstalung zugeordnet',
                'member_assigned' => 'Mitglied zugeordnet',
                'receipt_sent' => 'Quittung versendet am',
                'project_assigned' => 'Projekt',
                'funding_assigned' => 'Förderung',
            ],
        ],
        'search' => [
            'placeholder' => 'Suche ...',
        ],
        'filter' => [
            'date_range' => [
                'placeholder' => 'nach Zeitraum filtern',
            ],
            'type' => [
                'placeholder' => 'nach Typ filtern',
                'suffix' => 'Buchungstyp',
            ],
            'status' => [
                'placeholder' => 'nach Status filtern',
                'suffix' => 'Buchungstatus',
            ],
        ],
        'btn' => [
            'create' => 'Neue Buchung anlegen',
        ],
        'confirm' => [
            'resend_invoice' => 'Die E-Mail wurde bereits verschickt. Erneut verschicken?',
            'detach_project' => 'Projektzuordnung wirklich aufheben?',
            'detach_funding' => 'Förderzuordnung wirklich aufheben?',
        ],
        'modal' => [
            'max' => 'Max',
            'edit' => [
                'heading' => 'Buchung bearbeiten',
            ],
            'append_event' => [
                'heading' => 'Veranstaltung zuordnen',
                'select_placeholder' => 'Veranstaltung wählen',
                'optional' => 'Optional',
                'btn' => [
                    'submit' => 'zuordnen',
                ],
            ],
            'append_member' => [
                'heading' => 'Mitglied zuordnen',
                'select_placeholder' => 'Mitglied wählen',
                'membership_fees' => 'Mitgliedsbeiträge',
                'is_membership_fee' => 'Ist Mitgliedszahlung',
                'fee_year' => 'Erfassen für Kassenjahr',
                'btn' => [
                    'submit' => 'Mitglied zuordnen',
                ],
            ],
            'append_project' => [
                'heading' => 'Projekt zuordnen',
                'select_placeholder' => 'Projekt auswählen...',
                'allocated_amount' => 'Anteiliger Betrag',
                'allocated_amount_hint' => 'Optional: Nur den anteiligen Betrag dieser Buchung dem Projekt zurechnen.',
                'btn' => ['submit' => 'Zuordnen'],
            ],

            'append_funding' => [
                'heading' => 'Förderung zuordnen',
                'select_placeholder' => 'Förderung auswählen...',
                'allocated_amount' => 'Anteiliger Betrag',
                'allocated_amount_hint' => 'Optional: Nur den anteiligen Betrag dieser Buchung der Förderung zurechnen.',
                'booking_amount' => 'Buchungsbetrag',
                'funding_remaining' => 'Noch verfügbar in Förderung',
                'max_allocatable' => 'Max. zurechenbar',
                'btn' => ['submit' => 'Zuordnen'],
                'error' => [
                    'exceeds_amount' => 'Der anteilige Betrag darf den Buchungsbetrag (:amount) nicht überschreiten.',
                ],
            ],
        ],
    ],
    'create' => [
        'page' => [
            'title' => 'Erstellle Buchung',
        ],
        'title' => '[DE] Uj Tranzakció',
    ],
    'account-transfer-modal' => [
        'heading' => 'Umbuchung (Finanzkonto ändern)',
        'content' => 'Die Umbuchung storniert die ausgewählte Buchung und erstellt eine neue Buchung mit einem Bezug zum neuen Finanzkonto',
        'reason' => 'Grund der Umbuchung',
        'new_account' => 'Neues Finanzkonto',
        'account_placeholder' => 'Zahlungskonto z.B. Barkasse, Bankkonto usw',
        'btn' => [
            'submit' => 'Umbuchen',
        ],
        'error' => [
            'transaction_id' => 'Es ist keine Buchung ausgewählt worden',
            'account_id' => 'Es ist kein Finanzkonto ausgewählt worden',
            'identical' => 'Es sollte nicht das ursprüngliche Konto ausgewählt werden',
            'reason' => 'Eine Begründung ist zwingend anzugeben!',
        ],
    ],
    'account' => [
        'name' => 'Finanzkonto',
        'number' => 'Nummer',
        'institute' => 'Institut',
        'type' => 'Art',
        'iban' => 'IBAN',
        'bic' => 'BIC',
        'starting_amount' => 'Startguthaben',
    ],
    'mail' => [
        'receipt' => [
            'subject' => 'Quittung über erhaltenen Beitrag',
            'title' => 'Quittung über erhaltenen Beitrag',
            'greeting' => '',
            'header' => 'Übersicht',
            'body' => 'Vielen Dank für Ihren Beitrag! Im Anhang finden Sie den Quittungsbeleg für Ihre Unterlagen. Bei Fragen gerne auf diese E-Mail antworten.',
            'date' => 'Zahlung erhalten am:',
            'amount' => 'Erhaltener Betrag',
            'label' => 'Verwendungszwecks/Betreff',
            'reference' => 'Referenz',
        ],
    ],
    'event' => [
        'boxoffice' => [
            'heading' => 'Abendkasse',
            'paymentsection' => 'Buchungsdaten',
            'visitorsection' => 'Besucherdaten',
            'visitorname' => 'Name',
            'visitoremail' => 'E-Mail',
            'submit' => 'Abendkasse erfassen',
        ],
    ],
    'status' => [
        'submitted' => 'eingereicht',
        'booked' => 'gebucht',
    ],
    'locked' => [
        'tooltip' => 'Diese Transaktion ist gesperrt (Teil eines abgeschlossenen Geschäftsjahres)',
        'cannot_modify' => 'Diese Transaktion kann nicht bearbeitet werden, da sie Teil eines abgeschlossenen Geschäftsjahres ist.',
    ],
    'type' => [
        'deposit' => 'Einzahlung',
        'withdrawal' => 'Auszahlung',
        'transfer' => 'Umbuchung',
        'reversal' => 'Stornierung',
    ],
    'attach-project-success' => [
        'heading' => 'Projekt zugeordnet',
        'text' => 'Die Buchung wurde dem Projekt erfolgreich zugeordnet.',
        'error' => [
            'exceeds_amount' => 'Der anteilige Betrag darf den Buchungsbetrag (:amount) nicht überschreiten.',
        ],
    ],
    'detach-project-success' => [
        'heading' => 'Projekt entfernt',
        'text' => 'Die Projektzuordnung wurde aufgehoben.',
    ],
    'attach-funding-success' => [
        'heading' => 'Förderung zugeordnet',
        'text' => 'Die Buchung wurde der Förderung erfolgreich zugeordnet.',
        'error' => [
            'exceeds_amount' => 'Der anteilige Betrag darf den Buchungsbetrag (:amount) nicht überschreiten.',
        ],
    ],
    'detach-funding-success' => [
        'heading' => 'Förderung entfernt',
        'text' => 'Die Förderzuordnung wurde aufgehoben.',
    ],
];
