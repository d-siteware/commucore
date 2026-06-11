<?php

declare(strict_types=1);

return [
    'name' => [
        'required' => 'Bitte einen Namen angeben',
    ],
    'status' => [
        'label' => 'Status',
        'draft' => 'entwurf',
        'pending' => 'ausstehend',
        'published' => 'veröffentlicht',
        'rejected' => 'abgelehnt',
        'retracted' => 'zurückgezogen',

    ],
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
                    'status_msg' => 'Diese Veranstaltung wurde veröffentlicht und ist nun sichtbar.',
                    'sent_at' => 'verschickt :time',
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
            'poster' => 'Poster',
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
            'label' => 'E-Mail Adresse',
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
        'texts' => [
            'title_label' => 'Titel für Sprache',
            'title_description' => 'Der Titel wird für die Seite verwendet',
            'description_label' => 'Inhalt/Beschreibung für Sprache',
            'slug_label' => 'slug Sprache',
            'slug_description' => 'Der slug wird als Link verwendet',
            'excerpt_label' => 'Text Auszug für Sprache',
            'excerpt_description' => 'Wird für die Vorschau verwendet. Bitte max 200 Zeichen',
        ],
    ],
    'visitors' => [
        'empty_results_msg' => 'Bislang keine Besucher erfasst',
        'search_placeholder' => 'Suche Besucher',
        'table' => [
            'paid' => 'Bezahlt',
        ],
        'menu' => [
            'assign' => 'Zuordnen',
            'assign_member' => 'Mitglied',
            'assign_subscriber' => 'Anmelder',
            'delete' => 'Löschen',
        ],
    ],
    'visitor' => [
        'label' => 'Besucher',
        'name' => 'Besuchername',
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
        'step' => [
            'core_data' => 'Kerndaten',
            'texts' => 'Texte',
            'cover_image' => 'Titelbild',
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
            'new' => 'Neu',
        ],
        'status' => 'Status',
        'status_placeholder' => 'Status ...',
        'payment_link' => 'Link für Bezahlung',
        'content' => 'Inhalt/Beschreibung',
        'btn' => [
            'save' => 'Speichern',
        ],
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
        'greeting' => [
            'member_male' => 'Lieber :name,',
            'member_female' => 'Liebe :name,',
            'subscriber' => 'Hallo,',
        ],
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
        'option' => [
            'image' => 'Titelbild einblenden',
            'text' => 'Text',
            'text_excerpt' => 'Kurztext',
            'text_full' => 'Langtext',
            'preview_locale' => 'Vorschau-Sprache',
        ],
        'generate' => 'Poster generieren',
        'generate_jpeg' => 'JPEG generieren',
        'generate_pdf' => 'PDF generieren',
        'preview' => 'Vorschau',
        'jpeg_files' => 'JPEG-Poster',
        'pdf_files' => 'PDF-Poster',
        'confirm_delete' => 'Poster wirklich löschen?',
    ],
    'notification_letter' => [
        'title' => 'Einladung',
        'subject' => 'Einladung zu unserer Veranstaltung',
        'greeting' => 'Kedves :name,',
        'text' => 'wir freuen uns, Dir mitteilen zu können, dass am :datum eine Veranstaltung stattfinden wird, zu der wir Dich herzlich einladen möchten.',
        'overview' => 'Zeit und Ort',
        'closing_text' => 'Wir hoffen, dass Du teilnehmen kannst und freuen uns auf ein baldiges Wiedersehen.',
        'signature' => 'Mit herzlichen Grüßen',
        'board' => 'Der Vorstand von :org',
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
    'subscriptions' => [
        'btn' => [
            'add_new' => 'neue Anmeldung hinzufügen',
        ],
        'table' => [
            'name' => 'Name',
            'date' => 'Datum',
            'email' => 'E-Mail',
            'notifications' => 'Benachrichtigungen',
            'phone' => 'Telefon',
            'guests' => '# Gäste',
            'confirmed_at' => 'E-Mail bestätigt am',
        ],
    ],
    'payments' => [
        'table' => [
            'text' => 'Text',
            'date' => 'Datum',
            'visitor' => 'Besucher',
            'amount' => 'Betrag',
        ],
        'btn' => [
            'add_new' => 'Neue Zahlung erfassen',
            'create_report' => 'Bericht erstellen',
        ],
    ],
    'modal' => [
        'resend_notification' => [
            'heading' => 'Bitte bestätigen',
            'text_1' => 'Die Benachrichtigung wurde bereits am :date verschickt.',
            'text_2' => 'Soll diese erneut verschickt werden?',
            'btn_cancel' => 'Doch nicht',
            'btn_confirm' => 'Ja, bitte erneut versenden',
        ],
    ],

    'report' => [
        'title' => 'Event-Report',
        'summary' => 'Zusammenfassung',
        'finances' => 'Finanzen',
        'income' => 'Einnahmen',
        'expenses' => 'Ausgaben',
        'total' => 'Gesamt',
        'visitors' => 'Besucher',
        'visitors_total' => 'Gesamtzahl der erfassten Besucher',
        'visitors_male' => 'Gesamt männlich',
        'visitors_female' => 'Gesamt weiblich',
        'members' => 'Mitglieder',
        'subscribed_online' => 'Über die Webseite angemeldet',
        'details' => 'Details',
        'details_income' => 'Einnahmen',
        'details_expenses' => 'Ausgaben',
        'details_visitors' => 'Besucher',
        'text' => 'Text',
        'reference' => 'Referenz',
        'status' => 'Status',
        'account' => 'Konto',
        'amount' => 'Betrag',
        'name' => 'Name',
        'email' => 'E-Mail',
        'legend_member' => 'M: Besucher ist Mitglied',
        'legend_subscribed' => 'A: Besucher hat sich angemeldet',
        'legend_male' => 'M: Besucher ist männlich',
        'legend_female' => 'W: Besucher ist weiblich',
    ],
    'boxoffice' => [
        'ticket_count' => 'Anzahl gekaufter Karten',
        'select_account' => 'Kasse wählen',
        'select_booking_account' => 'Konto wählen',
    ],
    'payment' => [
        'date' => 'Datum',
        'type' => 'Buchung',
        'account_placeholder' => 'Zahlungskonto z.B. Barkasse, Bankkonto usw',
        'booking_account_placeholder' => 'SKR Konto',
        'label' => 'Text / Zweck',
        'entry_fee' => 'Eintritt',
        'entry_fee_discounted' => 'Eintritt rabattiert',
        'member_list_placeholder' => 'Mitgliedsliste',
        'external' => 'Extern',
        'btn_store' => 'Zahlung erfassen',
    ],
    'file' => [
        'delete_error' => 'Die Datei konnte nicht gelöscht werden: :message',
    ],
    'error' => [
        'heading' => 'Fehler',
    ],
    'demo' => [
        'description_de' => '
<h2>Einladung zum Sommerfest 2026</h2>
<p>Der Modellbauverein lädt alle Mitglieder, Familien und Interessierten herzlich zum diesjährigen Sommerfest ein.</p>

<p>Freuen Sie sich auf eine vielfältige Ausstellung beeindruckender Modelle aus den Bereichen Flugzeug-, Schiffs- und Fahrzeugbau. Erfahrene Vereinsmitglieder präsentieren ihre neuesten Projekte und stehen für Fragen und fachlichen Austausch zur Verfügung.</p>

<h3>Highlights</h3>
<ul>
<li>Live-Vorführungen von RC-Modellen</li>
<li>Mitmachbereich für Kinder und Jugendliche</li>
<li>Fachgespräche und Tipps rund um den Modellbau</li>
<li>Grillstand und Getränke</li>
</ul>

<p>Das Sommerfest bietet eine ideale Gelegenheit, den Verein kennenzulernen und gemeinsam einen entspannten Tag zu verbringen.</p>
',
        'description_hu' => '
<h2>Meghívó a 2026-os nyári rendezvényre</h2>
<p>A Modellépítő Egyesület szeretettel meghív minden tagot, családtagot és érdeklődőt az idei nyári rendezvényére.</p>

<p>A látogatók megtekinthetik a repülő-, hajó- és járműmodellek széles választékát. Tapasztalt tagjaink bemutatják legújabb munkáikat, és szívesen válaszolnak minden felmerülő kérdésre.</p>

<h3>Programok</h3>
<ul>
<li>RC modellek élő bemutatója</li>
<li>Interaktív foglalkozások gyermekeknek</li>
<li>Szakmai beszélgetések és tanácsadás</li>
<li>Grillételek és frissítők</li>
</ul>

<p>Ez az esemény kiváló alkalom arra, hogy kötetlen hangulatban megismerjék egyesületünket.</p>
',
    ],
    'select' => [
        'placeholder' => 'Veranstaltung auswählen',
        'empty' => 'Keine Veranstaltung gefunden',
    ],
    'send_publication' => [
        'no_email_members' => 'Keine Mitglieder ohne E-Mail adresse gefunden',
        'abort_heading' => 'Abbruch',
    ],
];
