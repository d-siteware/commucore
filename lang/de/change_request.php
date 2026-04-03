<?php

return [
    'status' => [
        'pending' => 'Ausstehend',
        'completed' => 'Genehmigt',
        'rejected' => 'Abgelehnt',
    ],

    'modal' => [
        'title' => 'Änderung beantragen',
        'description' => 'Senden Sie einen Änderungsantrag an den Vorstand. Sie werden benachrichtigt, sobald dieser bearbeitet wurde.',
        'submit' => 'Antrag einreichen',
    ],

    'field' => [
        'label' => 'Zu änderndes Feld',
        'placeholder' => 'Feld auswählen…',
    ],

    'requested_value' => [
        'label' => 'Gewünschter Wert',
        'placeholder' => 'Gewünschten Wert eingeben…',
    ],

    'reason' => [
        'label' => 'Begründung',
        'placeholder' => 'Bitte begründen Sie kurz, warum diese Änderung notwendig ist…',
    ],

    'table' => [
        'pending_heading' => 'Offene Anträge',
        'history_heading' => 'Verlauf',
        'empty' => 'Noch keine Änderungsanträge vorhanden.',
        'col' => [
            'field' => 'Feld',
            'requested_value' => 'Gewünschter Wert',
            'reason' => 'Begründung',
            'status' => 'Status',
            'date' => 'Eingereicht',
            'reviewed_by' => 'Bearbeitet von',
        ],
    ],

    'review' => [
        'empty' => 'Keine offenen Änderungsanträge.',
        'modal' => [
            'title' => 'Änderungsantrag bearbeiten',
            'old_value' => 'Aktueller Wert',
            'requested_value' => 'Gewünschter Wert',
            'rejection_reason' => 'Ablehnungsgrund',
            'rejection_reason_placeholder' => 'Bitte begründen Sie die Ablehnung…',
            'rejection_reason_hint' => 'Nur bei Ablehnung erforderlich.',
            'deduction_reason_placeholder' => 'Begründung für die Beitragsermäßigung…',
            'deduction_reason_hint' => 'Wird bei Genehmigung als Ermäßigungsgrund gespeichert.',
        ],
    ],

    'btn' => [
        'review' => 'Bearbeiten',
        'approve' => 'Genehmigen',
        'reject' => 'Ablehnen',
    ],

    'toast' => [
        'created' => [
            'heading' => 'Antrag eingereicht',
            'text' => 'Ihr Änderungsantrag wurde an den Vorstand weitergeleitet.',
        ],
        'duplicate' => [
            'heading' => 'Offener Antrag vorhanden',
            'text' => 'Für dieses Feld gibt es bereits einen offenen Antrag.',
        ],
        'approved' => [
            'heading' => 'Antrag genehmigt',
            'text' => 'Die Änderung wurde übernommen.',
        ],
        'rejected' => [
            'heading' => 'Antrag abgelehnt',
            'text' => 'Der Änderungsantrag wurde abgelehnt.',
        ],
    ],

    'notification' => [
        'subject' => 'Neuer Änderungsantrag',
        'intro' => ':member hat einen Änderungsantrag für das Feld ":field" eingereicht.',
        'old_value' => 'Aktueller Wert: :value',
        'requested_value' => 'Gewünschter Wert: :value',
        'reason' => 'Begründung: :reason',
        'message' => ':member hat eine Änderung für ":field" beantragt: :value',
    ],

    'reviewed_notification' => [
        'subject' => 'Ihr Änderungsantrag wurde bearbeitet',
        'intro' => 'Ihr Änderungsantrag für das Feld ":field" wurde bearbeitet.',
        'approved' => 'Ihr Antrag wurde genehmigt und die Änderung wurde übernommen.',
        'rejected' => 'Ihr Antrag wurde abgelehnt.',
        'rejection_reason' => 'Begründung: :reason',

    ],
];
