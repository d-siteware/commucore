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
];
