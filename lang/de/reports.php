<?php

declare(strict_types=1);

return [
    'event.title' => 'Veranstalungsbericht',
    'event.subject' => 'Auswertung der Veranstaltung :name',
    'event.visitor.name' => 'Besucher',

    'account' => [
        'title' => 'Kassenbericht',
        'timespan' => 'Zeitraum',
        'heading' => 'Kopfdaten',
        'start' => 'Beginn',
        'end' => 'Ende',
        'starting_amount' => 'Startbetrag',
        'end_amount' => 'Endbetrag',
        'total_income' => 'Gesamteinnahmen',
        'total_expenditure' => 'Gesamtausgaben',
        'notes' => 'Notizen',
        'new' => [
            'header' => 'Neuen Bericht erstellen',
        ],
        'edit' => [
            'heading' => 'Bearbeiten',
        ],
        'btn' => [
            'get_transactions' => 'Hole Buchungen für Zeitraum',
            'store_data' => 'Daten speichern',
        ],
    ],

    'table.header.date' => 'Erstellt am',
    'table.header.name' => 'Finanzkonto',
    'table.header.status' => 'Status',
    'table.header.range' => 'Zeitraum',
    'table.header.audited' => 'Geprüft',

    'initiate-report-audit-modal.title' => 'Berichtsprüfung starten',
    'initiate-report-audit-modal.content' => 'Bitte die Mitglieder auswählen, welche die Prüfung vornehmen sollen.',
    'initiate-report-audit-modal.btn.submit' => 'Einladungen verschicken',
    'initiate-report-audit-modal.select_member_id' => 'Mitglied',

    'index' => [
        'title' => 'Monatsberichte',
        'actions' => [
            'datev_export' => 'DATEV CSV',
            'print' => 'drucken',
            'audit' => 'prüfen',
            'edit' => 'bearbeiten',
            'delete' => 'löschen',
        ],
        'export_warning' => [
            'title' => 'Bericht bereits exportiert',
            'body' => 'Dieser Bericht wurde bereits als DATEV-Export an den Steuerberater übermittelt. Eine erneute Prüfung kann den bestehenden Export ungültig machen.',
            'steuerberater_hint' => 'Wenn Sie fortfahren, informieren Sie bitte Ihren Steuerberater über den korrigierten Export.',
            'confirm' => 'Trotzdem fortfahren',
        ],

    ],

    'status' => [
        'eingereicht' => 'in Prüfung',
        'entwurf' => 'eingereicht',
        'geprueft' => 'geprüft',
        'draft' => 'entwurf',
        'submitted' => 'eingereicht',
        'audited' => 'geprüft',
        'rejected' => 'abgelehnt',
    ],
    'get_transactions_short' => 'Buchungen holen',
    'transactions_found' => ':count Buchungen gefunden',
    'no_transactions_in_period' => 'Keine Buchungen in dem Zeitraum gefunden!',
    'no_email_for_auditor' => 'Keine E-Mail für :email gefunden',
    'no_auditors_selected' => 'Es sollten noch Auditoren zur Prüfung ausgewählt werden!',
    'delete_error' => 'Der Bericht konnte nicht gelöscht werden: :message',
    'delete_success' => 'Der Bericht wurde erfolgreich gelöscht',
    'data_updated' => 'Berichtsdaten aktualisiert',
    'default_filename' => 'Bericht',
    'audits_found_heading' => 'Prüfungen gefunden',
    'audits_delete_warning' => 'Der zu löschende Bericht hat verknüpfte Prüfungen. Diese gehen mit der Löschung des Berichtes verloren.',
    'delete_all' => 'Alles löschen',
    'select_member_placeholder' => 'Mitglied wählen',
    'add_auditor' => 'hinzu',
    'nobody' => 'niemand',
    'create_report_btn' => 'Bericht erstellen',
    'auditor' => 'Prüfer',
];
