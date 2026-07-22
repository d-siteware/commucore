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
        'btn' => [
            'upload' => 'Dokument hochladen',
        ],
        'modal_title' => 'Dokumente an Buchung anheften',
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
    'delete' => [
        'success' => [
            'heading' => 'Erfolg',
            'msg' => 'Die Buchung wurde erfolgreich gelöscht',
        ],

    ],
    'delete-transaction-confirmation-modal' => [
        'heading' => 'Buchung hat Belege',
        'has_documents' => 'Die Buchung hat einen verknüpften Beleg der ebenfalls gelöscht wird. Dieser Vorgang kann nicht rückgängig gemacht werden!|Mit der Buchung sind noch :count Belege verknüpft. Diese werden ebenfalls gelöscht. Der Vorgang kann nicht rückgängig gemacht werden!',
        'btn' => 'Endgültig löschen',
    ],
    'index' => [
        'title' => 'Übersicht der Buchungen',
        'menu-item' => [
            'book' => 'Buchen',
            'edit' => 'Bearbeiten',
            'delete' => 'Löschen',
            'cancel' => 'Storno',
            'edit_text' => 'Texte ändern',
            'rebook' => 'Umbuchen',
            'attach_document' => 'Beleg anhängen',
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
                'position' => 'Förderposition',
                'position_hint' => 'Optional: Diese Buchung einer Position der Förderung zuordnen (für den Statusbericht).',
                'position_placeholder' => 'Keine Position',
                'booking_amount' => 'Buchungsbetrag',
                'funding_remaining' => 'Noch verfügbar in Förderung',
                'max_allocatable' => 'Max. zurechenbar',
                'btn' => ['submit' => 'Zuordnen'],
                'error' => [
                    'exceeds_amount' => 'Der anteilige Betrag darf den Buchungsbetrag (:amount) nicht überschreiten.',
                    'allocated_required_multi' => 'Diese Buchung ist bereits einer Förderung zugeordnet. Bei mehreren Förderungen ist ein anteiliger Betrag erforderlich.',
                    'existing_unallocated' => 'Eine bestehende Förderungs-Zuordnung dieser Buchung hat keinen Teilbetrag. Bitte zuerst lösen und mit Teilbetrag neu zuordnen.',
                ],
            ],
        ],
    ],
    'create' => [
        'page' => [
            'title' => 'Erstellle Buchung',
            'heading' => 'Neue Buchung erfassen',
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
        'send' => [
            'success' => 'Rechnung wurde erfolgreich an :email gesendet.',
            'success_heading' => 'Erfolg',
            'error' => 'Fehler beim Senden der Rechnung: :message',
            'error_heading' => 'Fehler',
            'no_email' => 'Die Rechnung kann nicht versendet werden, da das Mitglied keine E-Mail-Adresse hat. Bitte diese einpflegen oder ausdrucken und per Post senden.',
            'no_email_heading' => 'Fehler',
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
            'select_cash_desk' => 'Kasse wählen',
            'select_account' => 'Konto wählen',
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

    'form' => [
        'type' => 'Buchung',
        'status' => 'Status',
        'separator' => [
            'accounts' => 'Konten',
            'amounts' => 'Beträge',
            'texts' => 'Texte',
        ],
        'account' => [
            'placeholder' => 'Zahlungskonto z.B. Barkasse, Bankkonto usw',
            'new' => 'Neues Zahlungskonto',
        ],
        'booking_account' => [
            'placeholder' => 'Buchungskonto',
            'new' => 'Neues Buchungskonto',
        ],
        'area' => [
            'placeholder' => 'Steuerliche Sphäre (KOST1)',
        ],
        'amount_gross' => 'Brutto',
        'vat_percent' => 'MWSt [%]',
        'vat_amount' => 'MWSt [EUR]',
        'amount_net' => 'Netto',
        'label' => 'Bezeichnung',
        'reference' => 'Referenz',
        'date' => 'Datum',
        'description' => 'Beschreibung',
        'btn' => [
            'new' => 'Neue Buchung anfangen',
            'save_event' => 'Event-Buchung speichern',
            'save_member' => 'Mitglied-Buchung speichern',
            'save' => 'Buchung speichern',
        ],
        'validation' => [
            'label_required' => 'Bitte eine Bezeichnung der Buchung eingeben.',
            'account_id_required' => 'Bitte ein Zahlungskonto angeben',
            'type_required' => 'Der Typ der Buchung muss angegeben werden',
            'status_required' => 'Der Buchungsstatus muss angegeben werden',
            'fiscal_year_closed' => 'Das gewählte Geschäftsjahr ist bereits abgeschlossen.',
            'fiscal_year_plausibility' => 'Das Geschäftsjahr muss dem Buchungsjahr oder ±1 Jahr entsprechen (10-Tage-Regel).',
        ],
    ],

    'modal' => [
        'account' => [
            'heading' => 'Zahlungskonto anlegen',
            'type_placeholder' => 'Kontotyp',
            'name' => 'Name',
            'number' => 'Nummer',
            'starting_amount' => 'Startguthaben',
            'institute' => 'Institut',
            'iban' => 'IBAN',
            'bic' => 'BIC',
            'btn' => [
                'save_and_continue' => 'Speichern und weiter anlegen',
                'save_and_select' => 'Anlegen und übernehmen',
            ],
        ],
        'booking' => [
            'heading' => 'Buchungskonto anlegen',
            'type_label' => 'Kontenrahmen',
            'category_label' => 'Kontenart',
            'category_placeholder' => 'Kategorie wählen',
            'area_label' => 'Steuerliche Sphäre',
            'area_placeholder' => 'Bereich wählen',
            'subtype_label' => 'Untertyp',
            'subtype_placeholder' => 'Kein Untertyp',
            'label' => 'Bezeichnung',
            'number' => 'Kontonummer',
            'btn' => [
                'save_and_continue' => 'Speichern und weiter anlegen',
                'save_and_select' => 'Anlegen und übernehmen',
            ],
        ],
        'missing' => [
            'heading' => 'Keine Buchung',
            'text' => 'Es wurde noch keine Buchung erfasst zu der ein Beleg zugeordnet werden könnte',
        ],
    ],

    'booking' => [
        'heading' => 'Buchung zuordnen',
        'label' => 'Buchungskonto zuordnen',
        'new_booking_account' => 'Neues Buchungskonto',
        'submit' => 'Buchung abschließen',
    ],

    'booking-update-success' => [
        'text' => 'Die Buchung wurde aktualisiert',
        'heading' => 'Erfolg',
    ],

    'cancel-success' => [
        'text' => 'Die Buchung :label wurde storniert',
        'heading' => 'Erfolg',
    ],

    'change-success' => [
        'text' => 'Die Buchung :label wurde geändert',
        'heading' => 'Erfolg',
    ],

    'event-create-success' => [
        'text' => 'Die Buchung für die Veranstaltung wurde erfasst',
        'heading' => 'Erfolg',
    ],

    'member-create-success' => [
        'text' => 'Die Buchung des Mitgliedsbeitrages wurde erfasst',
        'heading' => 'Erfolg',
    ],

    'create-success' => [
        'text' => 'Die Buchung :label wurde erfasst',
        'heading' => 'Erfolg',
    ],

    'update-success' => [
        'text' => 'Die Buchung :label wurde aktualisiert',
        'heading' => 'Erfolg',
    ],

    'attach-success' => [
        'text' => 'Die Buchung wurde erfolgreich zugeordnet',
    ],

    'area-reset-warning' => [
        'text' => 'Buchungskonto wurde zurückgesetzt – es gehört nicht zur gewählten Sphäre.',
    ],

    'create-error' => [
        'text' => 'Die Transaktion konnte nicht gespeichert werden: :message',
        'heading' => 'Fehler',
    ],

    'fiscal_year_locked' => 'Die Buchung „:label" liegt in einem abgeschlossenen Geschäftsjahr und kann nicht mehr geändert werden. Korrekturen bitte per Storno im offenen Jahr.',

    'validation' => [
        'valid_amount' => 'Bitte einen gültigen Betrag eingeben.',

        'event' => [
            'account_id' => [
                'required' => 'Bitte Zahlungskonto angeben',
                'doesnt_start_with' => 'Bitte Zahlungskonto angeben oder anlegen',
            ],
        ],

        'member' => [
            'account_id' => [
                'required' => 'Bitte ein Zahlungskonto auswählen oder anlegen',
            ],
            'label' => [
                'required' => 'Bitte eine Bezeichnung angeben',
            ],
            'amount_gross' => [
                'required' => 'Bitte einen Betrag angeben',
            ],
        ],

        'append_event' => [
            'target_event' => [
                'required' => 'Bitte eine Veranstaltung auswählen',
            ],
            'transaction_id' => [
                'unique' => 'Buchung ist bereits der Veranstaltung zugeordnet worden',
            ],
        ],

        'append_member' => [
            'target_member' => [
                'required' => 'Bitte ein Mitglied auswählen',
            ],
            'transaction_id' => [
                'unique' => 'Buchung ist bereits einem Mitglied zugeordnet worden',
            ],
            'fee_year' => [
                'integer' => 'Buchungen dürfen nicht älter als 2010 sein',
            ],
        ],

        'append_project' => [
            'target_project' => [
                'required' => 'Bitte ein Projekt auswählen.',
            ],
            'transaction_id' => [
                'unique' => 'Diese Buchung ist bereits einem Projekt zugeordnet.',
            ],
        ],

        'append_funding' => [
            'target_funding' => [
                'required' => 'Bitte eine Förderung auswählen.',
            ],
            'transaction_id' => [
                'unique' => 'Diese Buchung ist dieser Förderung bereits zugeordnet.',
            ],
        ],

        'boxoffice' => [
            'amount_gross' => [
                'required' => 'Eintrittspreis angeben (0 für freien Eintritt)',
            ],
            'account_id' => [
                'required' => 'Bitte ein Finanzkonto auswählen',
            ],
        ],
    ],

    'member_transaction' => [
        'assign_event_label' => 'Veranstaltung zuordnen (optional)',
    ],
];
