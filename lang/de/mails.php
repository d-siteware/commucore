<?php

declare(strict_types=1);


return [
    'president' => [
        'deputy' => 'Vizepräsident',
    ],
    'treasury' => 'Kassenwart',
    'secretariat' => [
        'hu' => 'Ungarisches Sekretariat',
        'de' => 'Deutsches Sekretariat',
    ],
    'cultural' => [
        'director' => 'Kulturleitung',
    ],
    'social' => [
        'affairs' => [
            'deputy' => 'Stellvertretende Sozialleitung',
        ],
    ],
    'contact' => 'Kontakt',
    'invitation' => [
        'subject' => 'Einladung zum Portal der :name',
        'greeting' => 'Szia :name',
        'header' => 'Bitte bestätige deine E-Mail-Adresse',
        'text' => 'Als aktives Mitglied der :name laden wir dich herzlich ein, dich auf unserem Portal als Benutzer zu registrieren.',
        'btn' => [
            'label' => 'Klicke hier, um deine Registrierung abzuschließen',
        ],
    ],
    'acceptance' => [
        'subject' => 'Bewilligter Mitgliedsantrag der :name',
        'greeting' => 'Szia :name',
        'header' => 'Herzlich willkommen',
        'text' => 'Wir freuen uns, dir mitteilen zu können, dass dein Antrag auf Mitgliedschaft in der :name geprüft und angenommen wurde.',
    ],
    'audit_invitation' => [
        'header' => 'Wir brauchen dich!',
        'text' => 'Wir laden dich ein, den monatlichen Kassenbericht für den Zeitraum :range zu prüfen. Du kannst entweder mit einem Klick auf den Link unten die Prüfung starten oder im Portal über Kasse -> Berichte und dann im entsprechenden Bericht auf den Button "Jetzt prüfen" drücken. Vielen Dank für deine Mühen!',
    ],
    'audit' => [
        'invitation' => [
            'subject' => 'Prüfung des monatlichen Kassenberichtes',
            'link_label' => 'Hier geht es zur Prüfung',
        ],
    ],
    'members' => [
        'heading' => 'E-Mail an alle Mitglieder mit hinterlegter E-Mail-Adresse',
        'content' => 'Die E-Mail wird in der Sprache erstellt, die der jeweilige Nutzer in seinem Profil angegeben hat.',
        'btn' => [
            'preview' => 'Vorschau (Ohne Anhänge)',
            'test_mail' => 'Testmail an mich (Ohne Anhänge)',
            'submit' => 'Absenden',
            'cancel' => 'Doch nicht',
            'final' => 'Sicher, los geht’s',
        ],
        'subject' => [
            'de' => 'Betreff',
            'hu' => '[DE] Tárgy',
        ],
        'message' => [
            'de' => 'Nachricht',
            'hu' => 'Nachricht',
        ],
        'confirm' => [
            'header' => 'Bitte vor dem Versenden sorgfältig prüfen',
            'warning' => 'Viele Mitglieder werden die Nachricht erhalten. Bei einem Fehler können viele unangenehme Dinge geschehen.',
            'info' => 'Vor dem Versand wird ein Eintrag in der Historie gemacht, wer wann welche E-Mail verschickt hat.',
        ],
    ],
    'member' => [
        'separator' => [
            'text' => 'Texte',
            'links' => 'Links',
            'attachments' => 'Anhänge (nur pdf|jpg|jpeg|png|tif)',
            'options' => 'Optionen',
        ],
    ],
    'mailing_list' => [
        'label' => [
            'email' => 'E-Mail-Adresse',
        ],
        'text' => [
            'privacy' => 'Ich erkläre mich bereit, dass meine Daten gespeichert und gemäß den geltenden Datenschutzgesetzen verarbeitet werden.',
            'privacy_full' => 'Ihre Daten werden ausschließlich zur Benachrichtigung über Veranstaltungen und Artikel genutzt und nicht an Dritte weitergegeben.',
        ],
        'btn_subscribe' => [
            'label' => 'Jetzt in Liste eintragen',
        ],
        'header' => 'Erhalten Sie Benachrichtigungen über neue Veranstaltungen und Artikel der :name',
        'options_group_header' => 'Auswahl der Themen',
        'options_header' => 'Einstellungen',
        'options' => [
            'all_label' => 'Alles!',
            'events_label' => 'Bitte nur für Veranstaltungen',
            'posts_label' => 'Bitte nur für Artikel',
            'all_description' => 'Erhalten Sie Benachrichtigungen, sobald eine neue Veranstaltung oder ein neuer Artikel veröffentlicht wird oder es zu Änderungen kommt.',
            'events_description' => 'Aktivieren Sie dieses Feld, wenn Sie ausschließlich Nachrichten über neue Veranstaltungen erhalten möchten.',
            'posts_description' => 'Aktivieren Sie dieses Feld, wenn Sie ausschließlich Nachrichten über neue Artikel erhalten möchten.',
            'update_notifications_label' => 'Aktualisierungen',
            'update_notifications_description' => 'Bitte auch für Aktualisierungen einer Veranstaltung oder eines Artikels eine Benachrichtigung senden',
        ],
        'validation_error' => [
            'email' => 'Bitte eine E-Mail-Adresse eingeben',
            'terms_accepted' => 'Bitte akzeptieren Sie die Datenschutzerklärung',
        ],
        'show' => [
            'confirmation_msg' => 'Sie haben Ihre E-Mail-Adresse erfolgreich verifiziert',
            'update_msg' => 'Sie haben Ihre Einstellungen erfolgreich geändert',
            'change' => 'Ändern Sie Ihre Auswahl, um künftig für diese Themen Benachrichtigungen zu erhalten.',
            'btn' => [
                'save' => 'Auswahl speichern',
            ],
        ],
        'confirmation_email_subject' => 'Bitte verifizieren Sie Ihre E-Mail-Adresse',
        'confirmation_email_msg' => 'Danke für Ihre Anmeldung zu unserer Mailingliste! Bitte bestätigen Sie Ihr Abonnement, indem Sie auf den Button unten klicken. So erhalten Sie Updates, die zu Ihren Interessen passen.',
        'confirmation_email_msg_changes' => 'Sie können Ihre Einstellungen jederzeit über einen Link aktualisieren, den wir in zukünftigen E-Mails beifügen.',
        'confirmation_email_msg_ignore' => 'Falls Sie sich nicht angemeldet haben, ignorieren Sie diese E-Mail einfach.',
        'confirmation_email' => [
            'selected_summary' => 'Diese Einstellungen gelten für Ihre E-Mail-Adresse:',
            'selected_events' => 'Erhalte Benachrichtigungen über neue Veranstaltungen',
            'selected_posts' => 'Erhalte Benachrichtigungen über neue Artikel',
            'selected_notifications' => 'Erhalte zusätzlich Benachrichtigungen über Änderungen',
            'locale' => 'Sprache, in der die Benachrichtigungen verfasst werden sollen',
            'btn' => [
                'verify_now' => 'E-Mail-Adresse verifizieren',
            ],
        ],
        'subscription_success' => 'Vielen Dank! Es wurde eine E-Mail zur Verifizierung verschickt',
        'verify' => [
            'header' => 'Bitte bestätigen Sie Ihre E-Mail-Adresse',
            'btn' => 'Bestätigen',
        ],
        'unsubscribe' => [
            'error_heading' => 'Das war unerwartet',
            'error_msg' => 'Leider konnte Ihre E-Mail-Adresse unerwartet nicht gelöscht werden. Wir entschuldigen uns für die Unannehmlichkeiten. Das System hat uns den Fehler gemeldet, und wir arbeiten bereits an einer Lösung. Sobald die Löschung erfolgreich durchgeführt wurde, informieren wir Sie. Bis dahin bitten wir um Ihr Verständnis für eventuell weiterhin eintreffende Benachrichtigungen.',
            'success_msg' => 'Ihre E-Mail-Adresse wurde erfolgreich aus unserer Liste entfernt. Sie erhalten künftig keine weiteren Benachrichtigungen von uns.',
        ],
        'verified_emails' => 'Verifizierte E-Mail-Adressen',
    ],
    'unsubscribe_link_label' => 'Einstellungen ändern / abmelden',
    'toast' => [
        'header' => [
            'success' => 'Erfolg',
        ],
    ],
];
