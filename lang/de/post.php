<?php

declare(strict_types=1);

return [
    'index' => [
        'page' => [
            'title' => 'Veröffentlichte Artikel in der Übersicht',
        ],
    ],

    'type' => [
        'label' => 'Artikeltyp',
        'review' => 'Rückblick',
        'other' => 'Anderes',
        'announcement' => 'Ankündigung',
        'report' => 'Bericht',
    ],

    'backend' => [
        'index' => [
            'page' => [
                'title' => 'Übersicht der Artikel',
            ],
            'btn' => [
                'start_new' => 'Neuer Artikel',
            ],
        ],
    ],

    'body' => 'Inhalt',
    'user_id' => 'Benutzer / Author',
    'status' => 'Status Veröffentlichung',
    'label' => 'Interner Bezeichner/Titel',
    'title' => 'Titel',
    'slug' => 'Slug',

    'create' => [
        'page' => [
            'title' => 'Neuen Artikel anlegen',
        ],
        'btn' => [
            'submit' => 'Artikel anlegen',
        ],
        'success' => [
            'title' => 'Artikel angelegt',
            'msg' => 'Der Artikel wurde erfolgreich angelegt.',
        ],
        'steps' => [
            'head' => 'Kopfdaten',
            'content' => 'Inhalt',
            'images' => 'Bilder',
        ],
        'title_explanation' => 'Der Titel wird zur Überschrift des Artikels gemacht und auch als Listeneintrag in den Übersichten. Er sollte nicht viel länger als 100 Zeichen sein und nicht im Fließtext als Überschrift wiederholt werden.',
        'slug_explanation' => 'Der Slug dient als Link zum Artikel. Er sollte im Idealfall der Titel ohne Leer- und Sonderzeichen sein. Mit einem Klick auf (gen slug) wird für beide Titel dies gemacht. WICHTIG ist, dass nach der Veröffentlichung des Artikels der Slug nur im Notfall geändert werden sollte.',
        'page_title' => 'Neuen Artikel anlegen',
        'images_upload_explanation' => 'Lade Bilder hoch, die als Galerie angezeigt werden.',
    ],

    'images' => [
        'existing' => 'Folgende Bilder sind mit dem Artikel verknüpft',
        'no_existing' => 'Keine Bilder zum Artikel gefunden',
        'upload_explanation' => 'Jeder Artikel kann mehrere Bilder enthalten.  In dieser Maske können Bilder hochgeladen werden. Bitte geben Sie eine jeweilige Beschreibung an, sowie den Autor des Bildes, soweit bekannt.',
        'preview' => 'Vorschau der hochgeladenen Bilder',
        'image_filename' => 'Bildname',
        'image_caption' => 'Beschreibung',
        'image_author' => 'Author',
        'image_btn_remove' => 'Entfernen',
        'empty_list' => 'Keine Bilder ausgewählt',
        'btn' => [
            'upload' => 'Bilder hochladen',
            'remove' => 'Bilde entfernen',
        ],
        'upload' => 'Bilder hochladen',
        'dropzone' => [
            'heading' => 'Bilder hier ablegen oder auf Bereich klicken',
            'text' => 'JPG, PNG, WebP, GIF bis max 20 MB',
        ],
    ],

    'section' => [
        'images' => [
            'gallery' => 'Bildergalerie',
            'header' => 'Neues Bild hochladen',
        ],
    ],

    'form' => [
        'toasts' => [
            'msg' => [
                'image_removed' => 'Bild erfolgreich entfernt!',
                'post_published' => 'Der Artikel wurde veröffentlicht!',
                'post_retracted' => 'Der Artikel wurde zurückgezogen!',
                'post_deleted' => 'Der Artikel wurde gelöscht!',
            ],
            'heading' => [
                'success' => 'Erfolg!',
                'warning' => 'Warnung!',
                'error' => 'Achtung, Fehler!',
            ],
            'create_success' => 'Der Artikel mit :num Bildern wurde erfolgreich aktualisiert!',
            'edit_success' => 'Der Artikel mit :num Bildern wurde erfolgreich aktualisiert!',
            'notifications_sent_success' => 'Mitteilungen über die Veröffentlichung wurden versendet',
            'notification_sent_success' => 'Mitteilungen über die Veröffentlichung wurden versendet',
            'eventDetachedSuccess' => 'Die Verknüpung zum Artikel wurde gelöscht',
            'eventAtachedSuccess' => 'Die Verknüpung zum Artikel wurde erstellt',
        ],
    ],

    'show' => [
        'title' => 'Artikel bearbeiten',
        'tabs' => [
            'header' => [
                'main' => 'Kopfdaten',
                'content' => 'Inhalt',
                'images' => 'Bilder',
            ],
        ],
        'tab' => [
            'main' => [
                'btn_make_slug' => 'Slug erstellen',
                'published' => [
                    'header' => 'Artikel ist veröffentlicht',
                    'status_msg' => 'Dieser Artikel wurde am :datum veröffentlicht.',
                    'btn_reset' => 'Widerrufen',
                    'confirmation_msg' => 'Bitte bestätigen, dass der Atikel widerrufen werden soll. Dieser ist dann nicht mehr auf dem öffentlichen Teil der Seite sichtbar.',
                    'btn_sendMails' => 'Rundbrief verschicken',
                    'btn_publish_now' => 'Artikel jetzt veröffentlichen',
                ],
                'attached_event' => [
                    'header' => 'Artikel ist verknüpft',
                    'status_msg' => 'Dieser Artikel wurde am mit der Veranstaltung :title veröffentlicht.',
                ],
                'detach_from_event' => [
                    'confirmation_msg' => 'Bitte bestätigen, dass der Atikel von der Veranstaltung werden soll.',
                ],
                'detach' => [
                    'btn_reset' => 'Verknüpfung lösen',
                ],
                'event' => [
                    'btn_connect_now' => 'Artikel jetzt mit Veranstaltung verknüpfen',
                ],
            ],
        ],
        'btn' => [
            'save' => 'Speichern',
        ],
        'label' => [
            'created_at' => 'Erstellt',
            'updated_at' => 'Zuletzt geändert',
        ],
        'delete' => [
            'confirm_prompt' => 'Der Artikel ist veröffentlicht. Bitte die Löschung bestätigen. Der Artikel und alle Bilder gehen verloren!',
        ],
    ],

    'notification_mail' => [
        'subject' => 'Neuer Artikel auf unserer Webseite veröffentlicht!',
        'header_subscriber' => 'Frisch erschienen: Ein neuer Artikel für Sie',
        'header_member' => 'Frisch erschienen: Ein neuer Artikel für Dich',
        'content_member' => 'wir haben spannende Neuigkeiten für Dich! Ein brandneuer Artikel ist soeben auf unserer Webseite erschienen – schau doch mal rein!',
        'content_subscriber' => 'wir haben spannende Neuigkeiten für Sie – ein neuer Artikel ist soeben auf unserer Webseite erschienen! Schauen Sie doch mal rein:',
        'btn_link_label' => 'weiterlesen',
        'btn_unsubscribe_link_label' => 'Sie erhalten diese E-Mail, weil Sie unsere Updates abonniert haben. Falls Sie Ihre Präferenzen anpassen oder sich abmelden möchten, klicken Sie auf „Einstellungen ändern / abmelden".',
        'content' => [
            'excerpt' => [
                'header' => 'Vorschau',
            ],
        ],
    ],

    '' => '',
];
