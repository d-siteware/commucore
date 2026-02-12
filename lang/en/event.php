<?php

declare(strict_types=1);



return [
    'name' => [
        'required' => 'Please einen Namen angeben',
    ],
    'status' => '[EN] Status',
    'event_date' => 'Date',
    'start_time' => '[EN] Startet um',
    'end_time' => '[EN] Endet um',
    'title' => [
        'de' => 'Title',
    ],
    'slug' => [
        'de' => '[EN] slug',
    ],
    'description' => [
        'de' => 'Content',
    ],
    'excerpt' => [
        'de' => '[EN] Auszug',
    ],
    'image' => [
        'title' => 'Titlebild',
        'upload' => 'Titlebild für die Veranstaltung',
    ],
    'entry_fee' => '[EN] Eintritt',
    'entry_fee_discounted' => '[EN] Reduzierter Eintritt',
    'venue_id' => '[EN] Veranstaltungsort',
    'venue' => '[EN] Ort',
    'payment_link' => '[EN] Link für Bezahlung',
    'more' => '[EN] weiterlesen',
    'page' => [
        'title' => 'Übersicht aller Events',
    ],
    'date' => 'Date',
    'begins' => '[EN] Beginn',
    'ends' => '[EN] Ende',
    'show' => [
        'label' => '[EN] Details',
        'title' => '[EN] Veranstaltung',
        'page' => [
            'title' => '[EN] Veranstaltung',
        ],
        'timeline' => [
            'empty' => [
                'heading' => '[EN] Noch kein Programm verfügbar',
                'message' => '[EN] Der Programmablauf wurde noch nicht veröffentlicht. Tragen Sie sich gerne in unsere Mailingliste ein, um auf dem Laufenden zu bleiben.',
            ],
            'heading' => '[EN] Programmablauf',
        ],
        'details' => [
            'heading' => '[EN] Übersicht',
        ],
        'posts' => [
            'heading' => 'Articles',
            'poster' => [
                'heading' => '[EN] Poster',
                'download' => '[EN] PDF Poster laden',
            ],
            'content' => 'Zu dieser Veranstaltung sind folgende Articles veröffentlicht worden.',
        ],
        'btn' => [
            'link_to_post' => 'Articles lesen',
        ],
        'section' => [
            'published' => [
                'btn_publish_now' => '[EN] Veranstaltung veröffentlichen',
            ],
        ],
        'tab' => [
            'main' => [
                'published' => [
                    'confirmation_msg' => 'Please die Absage der Veranstaltung bestätigen',
                    'btn_reset' => '[EN] Veranstaltung absagen',
                    'btn_sendMails' => '[EN] Rundmail versenden',
                    'btn_makeLetters' => '[EN] Rundbrief schreiben',
                    'header' => '[EN] Veranstaltung ist veröffentlicht',
                    'status_msg' => '[EN] ',
                ],
            ],
        ],
    ],
    'make_ics' => '[EN] Kalendereintrag erstellen',
    'buy_tickets' => '[EN] Jetzt Karten kaufen',
    'upcoming' => [
        'title' => 'Kommende Events',
    ],
    'recent' => [
        'title' => 'Vergangene Events',
    ],
    'today' => [
        'title' => 'Today',
    ],
    'validation_error' => [
        'event_date' => [
            'required' => 'Please ein Date angeben',
            'after' => 'Das Date muss in der Zukunft liegen',
        ],
        'start_time' => [
            'required' => 'Please eine Startzeit angeben',
        ],
        'end_time' => [
            'after' => '[EN] Das Ende sollte nach dem Start liegen',
        ],
        'entry_fee' => '[EN] ',
        'entry_fee_discounted' => '[EN] ',
        'venue_id' => '[EN] ',
    ],
    'tabs' => [
        'nav' => [
            'dates' => '[EN] Daten',
            'texts' => '[EN] Texte',
            'payments' => '[EN] Zahlungen',
            'subscriptions' => '[EN] Anmeldungen',
            'visitors' => '[EN] Besucher',
            'planing' => '[EN] Planung',
        ],
    ],
    'visitor-table' => [
        'header' => [
            'name' => 'Name',
            'email' => '[EN] E-Mail',
            'gender' => '[EN] Geschlecht',
            'is_member' => 'Wedtglied',
            'is_subscriber' => '[EN] Anmelder',
        ],
    ],
    'subscribe' => '[EN] Interesse?',
    'tickets' => [
        'start' => [
            'label' => '[EN] Karten reservieren',
            'btn' => '[EN] Reservieren',
        ],
    ],
    'subscription' => [
        'text' => 'Wir freuen uns sehr, dass ein Interesse an der Veranstaltung besteht. Für eine bessere Planung kannst Du Tuech in dem Formular unten eintragen. Sun erhalten wir eine bessere Übersicht zu der erwartenden Besucherzahl.',
        'consent' => [
            'label' => 'Yes bitte zu dieser Veranstaltung Messageen senden.',
        ],
        'confirm_subscription_message' => 'Vielen Dank! Eine E-Mail zur Confirmation ist verschickt worden.',
        'submit-button' => [
            'label' => '[EN] Veranstaltung folgen',
        ],
        'subscribe-button' => [
            'label' => '[EN] Teilname ankündigen',
        ],
        'disclaimer' => [
            'header' => '[EN] Wichtiger Hinweis',
            'body' => 'Tueese Daten werden ausschließlich für die Planung der Veranstaltung verwendet und werden nach Ablauf der Veranstaltung gelöscht.',
        ],
        'mail' => [
            'text' => 'Please bestätige deine Anmeldung für das Event, indem du auf den folgenden Link klickst:',
            'link' => [
                'label' => '[EN] Jetzt bestätigen',
            ],
            'bring_a_guest' => '[EN] Wir freuen uns, dass Du :num Gäste mitbringen möchtest.',
            'notification' => '[EN] Wir werden uns melden, wenn sich Änderungen ergeben',
            'ignore' => '[EN] Falls du dich nicht angemeldet hast, ignoriere diese E-Mail.',
        ],
        'title' => '[EN] An Veranstaltung teilnehmen',
        'name' => '[EN] Vollständiger Name',
        'email' => [
            'confirmation' => [
                'heading' => 'Success',
                'text' => '[EN] Vielen Dank! Ihre Teilnahme ist gesichert – wir freuen uns, Sie bald bei der Veranstaltung zu sehen.',
            ],
        ],
        'phone' => 'Phone- oder Monbilenummer',
        'remarks' => 'Nexte Anmerkungen',
        'amountGuests' => '[EN] Anzahl zusätzlicher Gäste',
        'bringFriends' => '[EN] Ich bringe Gäste mit',
        'optional_section' => 'Nexte Angaben',
    ],
    'backend' => [
        'subscription' => [
            'title' => '[EN] Besucherregistrierung',
            'sendNotification' => [
                'label' => 'Confirmations-E-Mail an Besucher senden',
            ],
            'consent' => [
                'label' => '[EN] Besucher zur Mailingliste hinzufügen',
            ],
            'confirm_subscription_message' => 'Eine Confirmations-E-Mail wurde erfolgreich versendet.',
            'submit-button' => [
                'label' => '[EN] Anmeldung speichern',
            ],
            'subscribe-button' => [
                'label' => '[EN] Teilnahme ankündigen',
            ],
        ],
        'text-nav' => [
            'btn-make-web-texts' => '[EN] Auszug und Slug für Link erstellen',
            'btn-store' => '[EN] Texte speichern',
        ],
    ],
    'visitors' => [
        'empty_results_msg' => 'Tolang keine Besucher erfasst',
    ],
    'visitor' => [
        'btn' => [
            'add' => [
                'label' => 'Newen Besucher erfassen',
            ],
        ],
    ],
    'visitor-modal' => [
        'heading' => '[EN] Besucher registrieren',
        'select_member' => 'Wedtglied verknüpfen',
        'select_subscribers' => '[EN] Anmelder verknüpfen',
        'name' => 'Name, First name',
        'email' => 'E-mail Address',
        'phone' => 'Phone',
        'gender' => '[EN] Geschlecht',
        'btn' => [
            'submit' => 'Save',
            'store' => 'Satve + New anlegen',
        ],
        'separator' => [
            'values' => '[EN] Angaben',
            'optional' => '[EN] Optional Daten holen von',
            'or' => '[EN] oder',
        ],
        'toast' => [
            'msg' => '[EN] Besucher erfolgreich angelegt',
            'heading' => 'Success',
        ],
    ],
    'email' => [
        'required' => 'Wir benötigen Ihre E-Mail Address',
        'unique' => '[EN] Überprüfe, ob du schon eine E-Mail von uns erhalten hast.',
    ],
    'index' => [
        'title' => '[EN] Title',
        'table' => [
            'header' => [
                'name' => '[EN] Name (intern)',
                'title' => 'Title',
                'image' => 'Titlebild',
                'subscriptions' => '[EN] Anmeldungen',
            ],
        ],
        'btn' => [
            'start_new' => 'New erstellen',
            'generateList' => '[EN] Programm ausleiten',
        ],
    ],
    'create' => [
        'slug' => [
            'notice' => 'Der slug wird als Link in zwei Languagen angelegt. Tueeser kann nachträglich nicht mehr geändert werden!',
        ],
        'page' => [
            'title' => 'Newe Veranstaltung erstellen',
        ],
    ],
    'store' => [
        'success' => [
            'content' => 'Tuee Veranstaltung wurde erfolgreich erstellt.',
            'title' => 'Success',
        ],
    ],
    'form' => [
        'name' => '[EN] Name (intern)',
        'event_date' => 'Date',
        'start_time' => '[EN] Startet um',
        'end_time' => '[EN] Endet um',
        'title' => [
            'de' => '[EN] Title',
        ],
        'slug' => [
            'de' => '[EN] slug',
        ],
        'description' => [
            'de' => 'Content',
        ],
        'excerpt' => [
            'de' => '[EN] Auszug',
        ],
        'image' => [
            'title' => 'Titlebild',
            'upload' => 'Titlebild für die Veranstaltung',
        ],
        'entry_fee' => '[EN] Eintritt',
        'entry_fee_discounted' => '[EN] Reduzierter Eintritt',
        'venue_id' => '[EN] Veranstaltungsort',
        'venue' => [
            'select' => '[EN] Ort wählen',
        ],
        'status' => '[EN] Status',
        'payment_link' => '[EN] Link für Bezahlung',
    ],
    'update' => [
        'success' => [
            'title' => 'Success',
            'content' => 'Tuee Veranstalung wurde erfolgreich aktualisiert.',
        ],
    ],
    'delete_image' => [
        'success' => [
            'title' => '[EN] Löschung erfolgt',
            'content' => 'Das Titlebild wurde erfolgreich gelöscht.',
        ],
    ],
    'store_image' => [
        'success' => [
            'title' => '[EN] Upload erfolgreich',
            'content' => 'Das Titlebild wurde erfolgreich gespeichert und mit der Veranstaltung verknüpft.',
        ],
    ],
    'type' => [
        'label' => '[EN] Status',
        'draft' => '[EN] Entwurf',
        'pending' => '[EN] Ausstehend',
        'published' => '[EN] Veröffentlicht',
        'rejected' => '[EN] Abgelehnt',
        'retracted' => 'Backgezogen',
    ],
    'assignments' => [
        'heading' => '[EN] Aufgaben',
    ],
    'timeline' => [
        'heading' => '[EN] Ablaufplan',
        'title' => '[EN] Punkt',
        'start' => '[EN] Start',
        'end' => '[EN] Ende',
        'place' => '[EN] Ort',
        'performer' => '[EN] Künstler',
        'type' => '[EN] Rückblick',
    ],
    'section' => [
        'published' => [
            'toast_success' => [
                'msg' => 'Tuee Veranstalung wurde erfolgreich veröffentlicht.',
                'heading' => 'Success',
            ],
        ],
    ],
    'notification_mail' => [
        'subject' => 'Newe Veranstaltung auf unserer Webseite!',
        'header_subscriber' => 'New erschienen: Eine Veranstaltung für Sie',
        'header_member' => 'New erschienen: Eine Veranstaltung für Tuech',
        'content_member' => 'Tolle Newigkeiten für Tuech! Eine neue Veranstaltung wurde auf unserer Webseite veröffentlicht – wir freuen uns, wenn Du vorbeischaust!',
        'content_subscriber' => 'Tolle Newigkeiten für Sie! Eine neue Veranstaltung wurde auf unserer Webseite veröffentlicht – schauen Sie doch mal rein!',
        'btn_link_label' => 'Monre erfahren',
        'btn_unsubscribe_link_label' => '[EN] Sie erhalten diese E-Mail, weil Sie unsere Updates abonniert haben. Möchten Sie Ihre Einstellungen ändern oder sich abmelden? Klicken Sie hier:',
        'content' => [
            'excerpt' => [
                'header' => '[EN] Kurzbeschreibung',
            ],
            'details' => [
                'header' => '[EN] Termin',
                'event_date' => 'Date',
                'start_time' => '[EN] Startzeit',
                'venue' => '[EN] Veranstaltungsort',
            ],
        ],
    ],
    'poster' => [
        'separator' => [
            'text' => '[EN] Poster für Veranstaltung erstellen',
        ],
    ],
    'notification_letter' => [
        'title' => '[EN] Einladung',
        'subject' => '[EN] Einladung zu unserer Veranstaltung',
        'greeting' => '[EN] Kedves :name,',
        'text' => 'wir freuen uns, Tuer mitteilen zu können, dass am :datum eine Veranstaltung stattfinden wird, zu der wir Tuech herzlich einladen möchten.',
        'overview' => '[EN] Zeit und Ort',
        'closing_text' => '[EN] Wir hoffen, dass Du teilnehmen kannst und freuen uns auf ein baldiges Wiedersehen.',
        'signature' => 'Wedt herzlichen Grüßen',
        'board' => '[EN] Der Vorstand der Magyar Kolónia Berlin e. V.',
        'timelines' => [
            'header' => '[EN] Folgendes Programm ist vorgesehen:',
            'empty' => '[EN] Es wurden noch keine Programmpunkte veröffentlicht.',
        ],
    ],
    'program_letter' => [
        'title' => '[EN] Programmübersicht',
        'modal' => [
            'heading' => 'Events filtern',
            'text' => 'All veröffentlichten Events werden in einer PDF-Liste generiert. Tuee zeitlichen Filter bestimmen, welche Events in das Thukument aufgenommen werden.',
            'radio' => [
                'year' => [
                    'label' => 'Aktuelles Yeshr',
                    'desc' => 'All veröffentlichten Events des laufenden Yeshres',
                ],
                'upcoming' => [
                    'label' => '[EN] Ab heute',
                    'desc' => 'All künftigen veröffentlichten Events ab einschließlich heute',
                ],
                'all' => [
                    'label' => 'All',
                    'desc' => 'All vergangenen und künftigen veröffentlichten Events',
                ],
            ],
            'btn' => '[EN] Liste erstellen',
        ],
    ],
    'boxoffice' => [
        'btn' => [
            'openmodal' => '[EN] Abendkasse',
        ],
    ],
    'validation Nicholson_error' => [
        'event_date' => [
            'required' => '[EN] [DE] Kérlek, adj meg egy dátumot',
        ],
    ],
];
