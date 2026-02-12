<?php

declare(strict_types=1);


return [
    'name' => [
        'required' => 'Bitte einen Namen angeben',
    ],
    'status' => 'Status',
    'event_date' => 'Datum',
    'start_time' => 'Startet um',
    'end_time' => 'Endet um',
    'title' => [
        'de' => 'Titel',
    ],
    'slug' => [
        'de' => 'slug',
    ],
    'description' => [
        'de' => 'Inhalt',
    ],
    'excerpt' => [
        'de' => 'Auszug',
    ],
    'image' => [
        'title' => 'Titelbild',
        'upload' => 'Titelbild für die Veranstaltung',
    ],
    'entry_fee' => 'Eintritt',
    'entry_fee_discounted' => 'Reduzierter Eintritt',
    'venue_id' => 'Veranstaltungsort',
    'venue' => 'Ort',
    'payment_link' => 'Link für Bezahlung',
    'more' => 'weiterlesen',
    'page' => [
        'title' => 'Übersicht aller Veranstaltungen',
    ],
    'date' => 'Datum',
    'begins' => 'Beginn',
    'ends' => 'Ende',
    'show' => [
        'label' => 'Details',
        'title' => 'Veranstaltung',
        'page' => [
            'title' => 'Veranstaltung',
        ],
        'timeline' => [
            'empty' => [
                'heading' => 'Noch kein Programm verfügbar',
                'message' => 'Der Programmablauf wurde noch nicht veröffentlicht. Tragen Sie sich gerne in unsere Mailingliste ein, um auf dem Laufenden zu bleiben.',
            ],
            'heading' => 'Programmablauf',
        ],
        'details' => [
            'heading' => 'Übersicht',
        ],
        'posts' => [
            'heading' => 'Artikel',
            'poster' => [
                'heading' => 'Poster',
                'download' => 'PDF Poster laden',
            ],
            'content' => 'Zu dieser Veranstaltung sind folgende Artikel veröffentlicht worden.',
        ],
        'btn' => [
            'link_to_post' => 'Artikel lesen',
        ],
        'section' => [
            'published' => [
                'btn_publish_now' => 'Veranstaltung veröffentlichen',
            ],
        ],
        'tab' => [
            'main' => [
                'published' => [
                    'confirmation_msg' => 'Bitte die Absage der Veranstaltung bestätigen',
                    'btn_reset' => 'Veranstaltung absagen',
                    'btn_sendMails' => 'Rundmail versenden',
                    'btn_makeLetters' => 'Rundbrief schreiben',
                    'header' => 'Veranstaltung ist veröffentlicht',
                    'status_msg' => '',
                ],
            ],
        ],
    ],
    'make_ics' => 'Kalendereintrag erstellen',
    'buy_tickets' => 'Jetzt Karten kaufen',
    'upcoming' => [
        'title' => 'Kommende Veranstaltungen',
    ],
    'recent' => [
        'title' => 'Vergangene Veranstaltungen',
    ],
    'today' => [
        'title' => 'Heute',
    ],
    'validation_error' => [
        'event_date' => [
            'required' => 'Bitte ein Datum angeben',
            'after' => 'Das Datum muss in der Zukunft liegen',
        ],
        'start_time' => [
            'required' => 'Bitte eine Startzeit angeben',
        ],
        'end_time' => [
            'after' => 'Das Ende sollte nach dem Start liegen',
        ],
        'entry_fee' => '',
        'entry_fee_discounted' => '',
        'venue_id' => '',
        '' => '',
    ],
    'tabs' => [
        'nav' => [
            'dates' => 'Daten',
            'texts' => 'Texte',
            'payments' => 'Zahlungen',
            'subscriptions' => 'Anmeldungen',
            'visitors' => 'Besucher',
            'planing' => 'Planung',
        ],
    ],
    'visitor-table' => [
        'header' => [
            'name' => 'Name',
            'email' => 'E-Mail',
            'gender' => 'Geschlecht',
            'is_member' => 'Mitglied',
            'is_subscriber' => 'Anmelder',
        ],
    ],
    'subscribe' => 'Interesse?',
    'tickets' => [
        'start' => [
            'label' => 'Karten reservieren',
            'btn' => 'Reservieren',
        ],
    ],
    'subscription' => [
        'text' => 'Wir freuen uns sehr, dass ein Interesse an der Veranstaltung besteht. Für eine bessere Planung kannst Du Dich in dem Formular unten eintragen. So erhalten wir eine bessere Übersicht zu der erwartenden Besucherzahl.',
        'consent' => [
            'label' => 'Ja bitte zu dieser Veranstaltung Nachrichten senden.',
        ],
        'confirm_subscription_message' => 'Vielen Dank! Eine E-Mail zur Bestätigung ist verschickt worden.',
        'submit-button' => [
            'label' => 'Veranstaltung folgen',
        ],
        'subscribe-button' => [
            'label' => 'Teilname ankündigen',
        ],
        'disclaimer' => [
            'header' => 'Wichtiger Hinweis',
            'body' => 'Diese Daten werden ausschließlich für die Planung der Veranstaltung verwendet und werden nach Ablauf der Veranstaltung gelöscht.',
        ],
        'mail' => [
            'text' => 'Bitte bestätige deine Anmeldung für das Event, indem du auf den folgenden Link klickst:',
            'link' => [
                'label' => 'Jetzt bestätigen',
            ],
            'bring_a_guest' => 'Wir freuen uns, dass Du :num Gäste mitbringen möchtest.',
            'notification' => 'Wir werden uns melden, wenn sich Änderungen ergeben',
            'ignore' => 'Falls du dich nicht angemeldet hast, ignoriere diese E-Mail.',
        ],
        'title' => 'An Veranstaltung teilnehmen',
        'name' => 'Vollständiger Name',
        'email' => [
            'confirmation' => [
                'heading' => 'Erfolg',
                'text' => 'Vielen Dank! Ihre Teilnahme ist gesichert – wir freuen uns, Sie bald bei der Veranstaltung zu sehen.',
            ],
        ],
        'phone' => 'Telefon- oder Mobilnummer',
        'remarks' => 'Weitere Anmerkungen',
        'amountGuests' => 'Anzahl zusätzlicher Gäste',
        'bringFriends' => 'Ich bringe Gäste mit',
        'optional_section' => 'Weitere Angaben',
    ],
    'backend' => [
        'subscription' => [
            'title' => 'Besucherregistrierung',
            'sendNotification' => [
                'label' => 'Bestätigungs-E-Mail an Besucher senden',
            ],
            'consent' => [
                'label' => 'Besucher zur Mailingliste hinzufügen',
            ],
            'confirm_subscription_message' => 'Eine Bestätigungs-E-Mail wurde erfolgreich versendet.',
            'submit-button' => [
                'label' => 'Anmeldung speichern',
            ],
            'subscribe-button' => [
                'label' => 'Teilnahme ankündigen',
            ],
        ],
        'text-nav' => [
            'btn-make-web-texts' => 'Auszug und Slug für Link erstellen',
            'btn-store' => 'Texte speichern',
        ],
    ],
    'visitors' => [
        'empty_results_msg' => 'Bislang keine Besucher erfasst',
    ],
    'visitor' => [
        'btn' => [
            'add' => [
                'label' => 'Neuen Besucher erfassen',
            ],
        ],
    ],
    'visitor-modal' => [
        'heading' => 'Besucher registrieren',
        'select_member' => 'Mitglied verknüpfen',
        'select_subscribers' => 'Anmelder verknüpfen',
        'name' => 'Name, Vorname',
        'email' => 'E-mail Adresse',
        'phone' => 'Telefon',
        'gender' => 'Geschlecht',
        'btn' => [
            'submit' => 'Speichern',
            'store' => 'Speichern + Neu anlegen',
        ],
        'separator' => [
            'values' => 'Angaben',
            'optional' => 'Optional Daten holen von',
            'or' => 'oder',
        ],
        'toast' => [
            'msg' => 'Besucher erfolgreich angelegt',
            'heading' => 'Erfolg',
        ],
    ],
    'email' => [
        'required' => 'Wir benötigen Ihre E-Mail Adresse',
        'unique' => 'Überprüfe, ob du schon eine E-Mail von uns erhalten hast.',
    ],
    'index' => [
        'title' => 'Title',
        'table' => [
            'header' => [
                'name' => 'Name (intern)',
                'title' => 'Titel',
                'image' => 'Titelbild',
                'subscriptions' => 'Anmeldungen',
            ],
        ],
        'btn' => [
            'start_new' => 'Neu erstellen',
            'generateList' => 'Programm ausleiten',
        ],
    ],
    'create' => [
        'slug' => [
            'notice' => 'Der slug wird als Link in zwei Sprachen angelegt. Dieser kann nachträglich nicht mehr geändert werden!',
        ],
        'page' => [
            'title' => 'Neue Veranstaltung erstellen',
        ],
    ],
    'store' => [
        'success' => [
            'content' => 'Die Veranstaltung wurde erfolgreich erstellt.',
            'title' => 'Erfolg',
        ],
    ],
    'form' => [
        'name' => 'Name (intern)',
        'event_date' => 'Datum',
        'start_time' => 'Startet um',
        'end_time' => 'Endet um',
        'title' => [
            'de' => 'Title',
        ],
        'slug' => [
            'de' => 'slug',
        ],
        'description' => [
            'de' => 'Inhalt',
        ],
        'excerpt' => [
            'de' => 'Auszug',
        ],
        'image' => [
            'title' => 'Titelbild',
            'upload' => 'Titelbild für die Veranstaltung',
        ],
        'entry_fee' => 'Eintritt',
        'entry_fee_discounted' => 'Reduzierter Eintritt',
        'venue_id' => 'Veranstaltungsort',
        'venue' => [
            'select' => 'Ort wählen',
        ],
        'status' => 'Status',
        'payment_link' => 'Link für Bezahlung',
    ],
    'update' => [
        'success' => [
            'title' => 'Erfolg',
            'content' => 'Die Veranstalung wurde erfolgreich aktualisiert.',
        ],
    ],
    'delete_image' => [
        'success' => [
            'title' => 'Löschung erfolgt',
            'content' => 'Das Titelbild wurde erfolgreich gelöscht.',
        ],
    ],
    'store_image' => [
        'success' => [
            'title' => 'Upload erfolgreich',
            'content' => 'Das Titelbild wurde erfolgreich gespeichert und mit der Veranstaltung verknüpft.',
        ],
    ],
    'type' => [
        'label' => 'Status',
        'draft' => 'Entwurf',
        'pending' => 'Ausstehend',
        'published' => 'Veröffentlicht',
        'rejected' => 'Abgelehnt',
        'retracted' => 'Zurückgezogen',
    ],
    'assignments' => [
        'heading' => 'Aufgaben',
    ],
    'timeline' => [
        'heading' => 'Ablaufplan',
        'title' => 'Punkt',
        'start' => 'Start',
        'end' => 'Ende',
        'place' => 'Ort',
        'performer' => 'Künstler',
        'type' => 'Rückblick',
    ],
    'section' => [
        'published' => [
            'toast_success' => [
                'msg' => 'Die Veranstalung wurde erfolgreich veröffentlicht.',
                'heading' => 'Erfolg',
            ],
        ],
    ],
    'notification_mail' => [
        'subject' => 'Neue Veranstaltung auf unserer Webseite!',
        'header_subscriber' => 'Neu erschienen: Eine Veranstaltung für Sie',
        'header_member' => 'Neu erschienen: Eine Veranstaltung für Dich',
        'content_member' => 'Tolle Neuigkeiten für Dich! Eine neue Veranstaltung wurde auf unserer Webseite veröffentlicht – wir freuen uns, wenn Du vorbeischaust!',
        'content_subscriber' => 'Tolle Neuigkeiten für Sie! Eine neue Veranstaltung wurde auf unserer Webseite veröffentlicht – schauen Sie doch mal rein!',
        'btn_link_label' => 'Mehr erfahren',
        'btn_unsubscribe_link_label' => 'Sie erhalten diese E-Mail, weil Sie unsere Updates abonniert haben. Möchten Sie Ihre Einstellungen ändern oder sich abmelden? Klicken Sie hier:',
        'content' => [
            'excerpt' => [
                'header' => 'Kurzbeschreibung',
            ],
            'details' => [
                'header' => 'Termin',
                'event_date' => 'Datum',
                'start_time' => 'Startzeit',
                'venue' => 'Veranstaltungsort',
            ],
        ],
    ],
    'poster' => [
        'separator' => [
            'text' => 'Poster für Veranstaltung erstellen',
        ],
    ],
    'notification_letter' => [
        'title' => 'Einladung',
        'subject' => 'Einladung zu unserer Veranstaltung',
        'greeting' => 'Kedves :name,',
        'text' => 'wir freuen uns, Dir mitteilen zu können, dass am :datum eine Veranstaltung stattfinden wird, zu der wir Dich herzlich einladen möchten.',
        'overview' => 'Zeit und Ort',
        'closing_text' => 'Wir hoffen, dass Du teilnehmen kannst und freuen uns auf ein baldiges Wiedersehen.',
        'signature' => 'Mit herzlichen Grüßen',
        'board' => 'Der Vorstand der Magyar Kolónia Berlin e. V.',
        'timelines' => [
            'header' => 'Folgendes Programm ist vorgesehen:',
            'empty' => 'Es wurden noch keine Programmpunkte veröffentlicht.',
        ],
    ],
    'program_letter' => [
        'title' => 'Programmübersicht',
        'modal' => [
            'heading' => 'Veranstaltungen filtern',
            'text' => 'Alle veröffentlichten Veranstaltungen werden in einer PDF-Liste generiert. Die zeitlichen Filter bestimmen, welche Veranstaltungen in das Dokument aufgenommen werden.',
            'radio' => [
                'year' => [
                    'label' => 'Aktuelles Jahr',
                    'desc' => 'Alle veröffentlichten Veranstaltungen des laufenden Jahres',
                ],
                'upcoming' => [
                    'label' => 'Ab heute',
                    'desc' => 'Alle künftigen veröffentlichten Veranstaltungen ab einschließlich heute',
                ],
                'all' => [
                    'label' => 'Alle',
                    'desc' => 'Alle vergangenen und künftigen veröffentlichten Veranstaltungen',
                ],
            ],
            'btn' => 'Liste erstellen',
        ],
    ],
    'boxoffice' => [
        'btn' => [
            'openmodal' => 'Abendkasse',
        ],
    ],
    'validation Nicholson_error' => [
        'event_date' => [
            'required' => '[DE] Kérlek, adj meg egy dátumot',
        ],
    ],
];
