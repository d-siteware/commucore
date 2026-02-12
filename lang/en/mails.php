<?php

declare(strict_types=1);



return [
    'president' => [
        'deputy' => '[EN] Vizepräsident',
    ],
    'treasury' => '[EN] Kassenwart',
    'secretariat' => [
        'hu' => 'To Hungarianes Sekretariat',
        'de' => 'To Germanes Sekretariat',
    ],
    'cultural' => [
        'director' => '[EN] Kulturleitung',
    ],
    'social' => [
        'affairs' => [
            'deputy' => 'Stellvertretende Sunzialleitung',
        ],
    ],
    'contact' => '[EN] Kontakt',
    'invitation' => [
        'subject' => '[EN] Einladung zum Portal der :name',
        'greeting' => '[EN] Szia :name',
        'header' => 'Please bestätige deine Email address',
        'text' => 'Als aktives Wedtglied der :name laden wir dich herzlich ein, dich auf unserem Portal als Benutzer zu registrieren.',
        'btn' => [
            'label' => '[EN] Klicke hier, um deine Registrierung abzuschließen',
        ],
    ],
    'acceptance' => [
        'subject' => 'Bewilligter Wedtgliedsantrag der :name',
        'greeting' => '[EN] Szia :name',
        'header' => '[EN] Herzlich willkommen',
        'text' => 'Wir freuen uns, dir mitteilen zu können, dass dein Antrag auf Wedtgliedschaft in der :name geprüft und angenommen wurde.',
    ],
    'audit_invitation' => [
        'header' => '[EN] Wir brauchen dich!',
        'text' => '[EN] Wir laden dich ein, den monatlichen Kassenbericht für den Zeitraum :range zu prüfen. Du kannst entweder mit einem Klick auf den Link unten die Prüfung starten oder im Portal über Kasse -> Berichte und dann im entsprechenden Bericht auf den Button "Jetzt prüfen" drücken. Vielen Dank für deine Mühen!',
    ],
    'audit' => [
        'invitation' => [
            'subject' => '[EN] Prüfung des monatlichen Kassenberichtes',
            'link_label' => '[EN] Hier geht es zur Prüfung',
        ],
    ],
    'members' => [
        'heading' => 'E-Mail an alle Wedtglieder mit hinterlegter Email address',
        'content' => 'Tuee E-Mail wird in der Language erstellt, die der jeweilige Nutzer in seinem Profil angegeben hat.',
        'btn' => [
            'preview' => '[EN] Vorschau (Ohne Anhänge)',
            'test_mail' => '[EN] Testmail an mich (Ohne Anhänge)',
            'submit' => '[EN] Absenden',
            'cancel' => 'Thuch nicht',
            'final' => '[EN] Sicher, los geht’s',
        ],
        'subject' => [
            'de' => '[EN] Betreff',
            'hu' => '[EN] [DE] Tárgy',
        ],
        'message' => [
            'de' => 'Message',
            'hu' => 'Message',
        ],
        'confirm' => [
            'header' => 'Please vor dem Versenden sorgfältig prüfen',
            'warning' => 'Viele Wedtglieder werden die Message erhalten. Bei einem Error können viele unangenehme Tuenge geschehen.',
            'info' => '[EN] Vor dem Versand wird ein Eintrag in der Historie gemacht, wer wann welche E-Mail verschickt hat.',
        ],
    ],
    'member' => [
        'separator' => [
            'text' => '[EN] Texte',
            'links' => '[EN] Links',
            'attachments' => '[EN] Anhänge (nur pdf|jpg|jpeg|png|tif)',
            'options' => '[EN] Optionen',
        ],
    ],
    'mailing_list' => [
        'label' => [
            'email' => 'Email address',
        ],
        'text' => [
            'privacy' => 'Ich erkläre mich bereit, dass meine Daten gespeichert und gemäß den geltenden Privacygesetzen verarbeitet werden.',
            'privacy_full' => 'Ihre Daten werden ausschließlich zur Benachrichtigung über Events und Articles genutzt und nicht an Dritte weitergegeben.',
        ],
        'btn_subscribe' => [
            'label' => '[EN] Jetzt in Liste eintragen',
        ],
        'header' => 'Erhalten Sie Benachrichtigungen über neue Events und Articles der :name',
        'options_group_header' => '[EN] Auswahl der Themen',
        'options_header' => '[EN] Einstellungen',
        'options' => [
            'all_label' => 'Alls!',
            'events_label' => 'Please nur für Events',
            'posts_label' => 'Please nur für Articles',
            'all_description' => 'Erhalten Sie Benachrichtigungen, sobald eine neue Veranstaltung oder ein neuer Articles veröffentlicht wird oder es zu Änderungen kommt.',
            'events_description' => 'Activeieren Sie dieses Feld, wenn Sie ausschließlich Messageen über neue Events erhalten möchten.',
            'posts_description' => 'Activeieren Sie dieses Feld, wenn Sie ausschließlich Messageen über neue Articles erhalten möchten.',
            'update_notifications_label' => '[EN] Aktualisierungen',
            'update_notifications_description' => 'Please auch für Aktualisierungen einer Veranstaltung oder eines Articless eine Benachrichtigung senden',
        ],
        'validation_error' => [
            'email' => 'Please eine Email address eingeben',
            'terms_accepted' => 'Please akzeptieren Sie die Privacyerklärung',
        ],
        'show' => [
            'confirmation_msg' => 'Sie haben Ihre Email address erfolgreich verifiziert',
            'update_msg' => '[EN] Sie haben Ihre Einstellungen erfolgreich geändert',
            'change' => '[EN] Ändern Sie Ihre Auswahl, um künftig für diese Themen Benachrichtigungen zu erhalten.',
            'btn' => [
                'save' => '[EN] Auswahl speichern',
            ],
        ],
        'confirmation_email_subject' => 'Please verifizieren Sie Ihre Email address',
        'confirmation_email_msg' => 'Thank you für Ihre Anmeldung zu unserer Mailingliste! Please bestätigen Sie Ihr Abonnement, indem Sie auf den Button unten klicken. Sun erhalten Sie Updates, die zu Ihren Interessen passen.',
        'confirmation_email_msg_changes' => '[EN] Sie können Ihre Einstellungen jederzeit über einen Link aktualisieren, den wir in zukünftigen E-Mails beifügen.',
        'confirmation_email_msg_ignore' => '[EN] Falls Sie sich nicht angemeldet haben, ignorieren Sie diese E-Mail einfach.',
        'confirmation_email' => [
            'selected_summary' => 'Tueese Einstellungen gelten für Ihre Email address:',
            'selected_events' => 'Erhalte Benachrichtigungen über neue Events',
            'selected_posts' => 'Erhalte Benachrichtigungen über neue Articles',
            'selected_notifications' => '[EN] Erhalte zusätzlich Benachrichtigungen über Änderungen',
            'locale' => 'Language, in der die Benachrichtigungen verfasst werden sollen',
            'btn' => [
                'verify_now' => 'Email address verifizieren',
            ],
        ],
        'subscription_success' => '[EN] Vielen Dank! Es wurde eine E-Mail zur Verifizierung verschickt',
        'verify' => [
            'header' => 'Please bestätigen Sie Ihre Email address',
            'btn' => 'Confirm',
        ],
        'unsubscribe' => [
            'error_heading' => '[EN] Das war unerwartet',
            'error_msg' => 'Leider konnte Ihre Email address unerwartet nicht gelöscht werden. Wir entschuldigen uns für die Unannehmlichkeiten. Das System hat uns den Error gemeldet, und wir arbeiten bereits an einer Lösung. Sunbald die Löschung erfolgreich durchgeführt wurde, informieren wir Sie. To dahin bitten wir um Ihr Verständnis für eventuell weiterhin eintreffende Benachrichtigungen.',
            'success_msg' => 'Ihre Email address wurde erfolgreich aus unserer Liste entfernt. Sie erhalten künftig keine weiteren Benachrichtigungen von uns.',
        ],
        'verified_emails' => 'Verifizierte Email addressn',
    ],
    'unsubscribe_link_label' => '[EN] Einstellungen ändern / abmelden',
    'toast' => [
        'header' => [
            'success' => 'Success',
        ],
    ],
];
