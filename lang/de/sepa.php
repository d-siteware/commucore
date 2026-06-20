<?php

declare(strict_types=1);

return [
    'mandate' => [
        'heading' => 'SEPA-Lastschriftmandat',
        'status' => [
            'pending' => 'Ausstehend',
            'active' => 'Aktiv',
            'cancelled' => 'Widerrufen',
            'expired' => 'Abgelaufen',
        ],
        'type' => [
            'core' => 'Basis-Lastschrift (CORE)',
            'b2b' => 'Firmen-Lastschrift (B2B)',
        ],
        'fields' => [
            'mandate_reference' => 'Mandatsreferenz',
            'iban' => 'IBAN',
            'bic' => 'BIC',
            'account_holder' => 'Kontoinhaber',
            'mandate_date' => 'Mandatsdatum',
            'mandate_type' => 'Mandatsart',
            'status' => 'Status',
            'signed_document' => 'Unterzeichnetes Mandat (PDF)',
            'sepa_documents' => 'Mandatsvollmacht (PDF)',
            'sepa_documents_dropzone_heading' => 'Datei ablegen oder auswählen',
            'sepa_documents_dropzone_text' => 'Unterzeichnetes SEPA-Mandat als PDF hochladen',
            'notes' => 'Notizen',
        ],
        'actions' => [
            'create' => 'SEPA-Mandat anlegen',
            'cancel' => 'Mandat widerrufen',
            'view' => 'Mandat anzeigen',
        ],
        'messages' => [
            'created' => 'SEPA-Mandat wurde erfolgreich angelegt.',
            'cancelled' => 'SEPA-Mandat wurde widerrufen.',
            'active' => 'Aktives SEPA-Mandat vorhanden.',
            'no_mandate' => 'Dieses Mitglied hat kein aktives SEPA-Mandat.',
            'has_active_mandate' => 'Dieses Mitglied hat bereits ein aktives SEPA-Mandat.',
            'updated' => 'SEPA-Mandat wurde erfolgreich aktualisiert.',
            'pending_fees_warning' => 'Achtung: Es sind noch offene Lastschriften vorhanden. Bereits eingereichte Buchungen werden dennoch ausgeführt.',
            'replaced' => 'Das vorherige SEPA-Mandat wurde widerrufen.',
        ],
    ],
    'settings' => [
        'tab' => 'SEPA',
        'iban_warnning' => 'Das Konto hat keine IBAN hinterlegt!',
        'creditor' => [
            'heading' => 'Gläubiger & Zahlungsformat',
            'subheading' => 'SEPA-Gläubiger-ID und Standardkonto für Lastschriften.',
            'creditor_id' => 'Gläubiger-ID (Creditor ID)',
            'account' => 'Gläubigerkonto',
            'account_placeholder' => 'Bankkonto auswählen…',
            'due_date_offset' => 'Fälligkeit in Tagen',
            'pain_format' => 'Pain-Format',
        ],
        'info' => [
            'heading' => 'Hinweise',
            'creditor_id_label' => 'Wo erhalte ich eine Gläubiger-ID?',
            'creditor_id_text' => 'Die Gläubiger-ID (Creditor Identifier) wird von der Deutschen Bundesbank ausgestellt. Beantragung online unter www.bundesbank.de – Suche nach "Gläubiger-ID beantragen". Die ID ist deutschlandweit gültig und lautet z. B. DE00ZZZ00000000000.',
            'pain_formats_label' => 'Was bedeuten die PAIN-Formate?',
            'pain_02' => 'Älteres Format, von den meisten deutschen Banken unterstützt.',
            'pain_09' => 'Aktuelles Standard-Format (ISO 20022). Empfohlen für Neukunden. Verwendet in Deutschland und Österreich.',
            'pain_at' => 'Österreich-Format (ISO 20022), identisch zu pain.008.001.09.',
            'pain_301' => 'Schweizer Format (ISO 20022), für Konten in der Schweiz.',
            'pain_recommendation' => 'Empfehlung: pain.008.001.09 verwenden, es sei denn, deine Bank fordert explizit ein anderes Format.',
        ],
        'transfer' => [
            'mode' => 'Übertragungsmodus',
            'mode_manual' => 'Manuell (XML-Download)',
            'mode_ebics' => 'Automatisch via EBICS',
        ],
        'ebics' => [
            'heading' => 'EBICS-Konfiguration',
            'subheading' => 'Zugangsdaten für automatische SEPA-Übermittlung an die Bank.',
            'host' => 'EBICS Host-URL',
            'host_id' => 'EBICS Host-ID',
            'partner_id' => 'EBICS Partner-ID',
            'user_id' => 'EBICS User-ID',
            'passphrase' => 'EBICS Passphrase',
        ],
        'btn' => [
            'save' => 'SEPA-Einstellungen speichern',
        ],
        'toast' => [
            'save_success_heading' => 'SEPA-Einstellungen gespeichert',
            'save_success_text' => 'Die SEPA-Konfiguration wurde erfolgreich aktualisiert.',
        ],
    ],
    'direct_debit' => [
        'heading' => 'SEPA-Lastschriften',
        'actions' => [
            'generate_xml' => 'SEPA-XML erzeugen',
            'run_collection' => 'Lastschrift einziehen',
        ],
        'messages' => [
            'xml_generated' => 'SEPA-XML wurde erfolgreich erzeugt.',
            'collection_initiated' => 'Lastschrifteinzug wurde gestartet.',
        ],
        'errors' => [
            'no_account' => 'Kein SEPA-Gläubigerkonto konfiguriert.',
        ],
    ],
    'return_debit' => [
        'heading' => 'Rücklastschriften',
        'actions' => [
            'mark_returned' => 'Als Rücklastschrift markieren',
            'recollect' => 'Wiedereinzug durchführen',
            'record_return' => 'Rückläufer erfassen',
        ],
        'messages' => [
            'marked_returned' => 'Transaktion wurde als Rücklastschrift markiert.',
            'recollected' => 'Wiedereinzug wurde eingeleitet.',
            'return_recorded' => 'Rückläufer wurde erfasst.',
        ],
        'errors' => [
            'no_active_mandate' => 'Kein aktives SEPA-Mandat für Wiedereinzug vorhanden.',
            'no_transaction' => 'Transaktion nicht gefunden.',
        ],
        'columns' => [
            'date' => 'Datum',
            'member' => 'Mitglied',
            'amount' => 'Betrag',
            'reason' => 'Grund',
            'actions' => 'Aktionen',
        ],
        'reasons' => [
            'unknown' => 'Unbekannter Grund',
        ],
        'no_returns' => 'Keine Rücklastschriften vorhanden.',
        'form' => [
            'heading' => 'Rückläufer erfassen',
            'reason_code' => 'Grund',
            'reason_text' => 'Grund (Freitext)',
            'reference' => 'Referenz der Bank (optional)',
        ],
    ],
    'notifications' => [
        'return_debit' => [
            'subject' => 'Rücklastschrift über :amount',
            'intro' => 'Hallo :name, eine Lastschrift über :amount wurde von deiner Bank zurückgegeben.',
            'reason' => 'Grund: :reason',
            'action' => 'Bitte überprüfe deine Zahlungsdaten und sorge für ausreichende Deckung. Bei Fragen wende dich an deinen Verein.',
        ],
    ],
    'collection' => [
        'no_batch' => 'ohne Batch',
        'heading' => 'SEPA-Übersicht',
        'subheading' => 'Übersicht aller ausstehenden und vergangenen Lastschrift-Einzüge.',
        'total_pending' => 'Ausstehende Lastschriften: :sum',
        'total_sum' => 'Gesamtbetrag: :sum',
        'tabs' => [
            'pending' => 'Ausstehend',
            'attempts' => 'Einreichungen',
            'history' => 'Historie',
            'returns' => 'Rücklastschriften',
        ],
        'pending_none' => 'Keine ausstehenden Lastschriften.',
        'no_attempts' => 'Keine ausstehenden Einreichungen.',
        'no_history' => 'Bisher keine SEPA-Einzüge durchgeführt.',
        'batch' => 'Batch',
        'create_none' => 'Keine neuen Beitrags-Transaktionen nötig – alle berechtigten Mitglieder haben bereits Transaktionen.',
        'transactions_created' => ':count Beitrags-Transaktion(en) wurden angelegt.',
        'actions' => [
            'create_attempts' => 'Einreichungen anlegen',
            'generate_xml' => 'XML generieren',
            'download_xml' => 'XML herunterladen',
            'upload_ebics' => 'via EBICS einreichen',
            'confirm' => 'Bestätigen',
            'confirm_batch' => 'Batch bestätigen',
        ],
        'columns' => [
            'member' => 'Mitglied',
            'mandate' => 'Mandatsreferenz',
            'amount' => 'Betrag',
            'fee_year' => 'Beitragsjahr',
            'status' => 'Status',
            'actions' => 'Aktionen',
        ],
        'errors' => [
            'ebics_not_configured' => 'EBICS ist nicht konfiguriert. Bitte zuerst das EBICS-Setup abschließen.',
        ],
        'messages' => [
            'ebics_upload_success' => 'SEPA-XML wurde erfolgreich via EBICS übermittelt.',
            'batch_confirmed' => ':count Buchungen bestätigt',
        ],
    ],
    'validation' => [
        'passed' => '✅ XML-Validierung bestanden: Die SEPA-Datei ist formal korrekt.',
        'failed' => '⚠️ XML-Validierung fehlgeschlagen (:count Fehler):',
        'failed_generic' => 'Validierung fehlgeschlagen',
        'error_line' => '  Zeile :line: :message',
        'step_validate' => 'XML validieren…',
    ],
];
