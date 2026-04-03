<?php

return [
    'status' => [
        'pending' => 'Ausstehend',
        'confirmed' => 'Bestätigt',
        'rejected' => 'Abgelehnt',
    ],

    'modal' => [
        'title' => 'Mitgliedschaft kündigen',
        'description' => 'Stellen Sie einen Kündigungsantrag an den Vorstand. Ihre Mitgliedschaft bleibt bis zur Bestätigung aktiv.',
        'submit' => 'Kündigungsantrag einreichen',
        'warning' => [
            'heading' => 'Bitte beachten',
            'text' => 'Die Kündigung muss vom Vorstand bestätigt werden, bevor sie wirksam wird. Sie werden über die Entscheidung informiert.',
        ],
    ],

    'leave_date' => [
        'label' => 'Gewünschtes Austrittsdatum',
        'description' => 'Optional. Leer lassen für sofortigen Austritt.',
    ],

    'reason' => [
        'label' => 'Begründung',
        'placeholder' => 'Bitte begründen Sie kurz Ihre Kündigung…',
    ],

    'review' => [
        'empty' => 'Keine offenen Kündigungsanträge.',
        'modal' => [
            'title' => 'Kündigungsantrag bearbeiten',
            'member' => 'Mitglied',
            'leave_date_immediate' => 'Sofort (kein Datum angegeben)',
            'rejection_reason_hint' => 'Nur bei Ablehnung erforderlich.',
            'warning' => [
                'heading' => 'Achtung',
                'text' => 'Mit der Genehmigung wird das Austrittsdatum gesetzt und die Mitgliedschaft beendet.',
            ],
        ],
    ],

    'toast' => [
        'created' => [
            'heading' => 'Antrag eingereicht',
            'text' => 'Ihr Kündigungsantrag wurde an den Vorstand weitergeleitet.',
        ],
        'duplicate' => [
            'heading' => 'Offener Antrag vorhanden',
            'text' => 'Für diese Mitgliedschaft gibt es bereits einen offenen Kündigungsantrag.',
        ],
        'approved' => [
            'heading' => 'Kündigung bestätigt',
            'text' => 'Die Mitgliedschaft wurde beendet.',
        ],
        'rejected' => [
            'heading' => 'Antrag abgelehnt',
            'text' => 'Der Kündigungsantrag wurde abgelehnt.',
        ],
    ],

    'notification' => [
        'subject' => 'Neuer Kündigungsantrag',
        'intro' => ':member hat einen Kündigungsantrag eingereicht.',
        'reason' => 'Begründung: :reason',
        'leave_date' => 'Gewünschtes Austrittsdatum: :date',
        'message' => ':member hat einen Kündigungsantrag eingereicht.',
    ],

    'reviewed_notification' => [
        'subject' => 'Ihr Kündigungsantrag wurde bearbeitet',
        'confirmed' => 'Ihr Kündigungsantrag wurde bestätigt. Ihre Mitgliedschaft wird zum gewünschten Zeitpunkt beendet.',
        'leave_date' => 'Austrittsdatum: :date',
        'rejected' => 'Ihr Kündigungsantrag wurde abgelehnt.',
        'rejection_reason' => 'Begründung: :reason',
    ],
];
