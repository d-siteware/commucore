<?php

declare(strict_types=1);



return [
    'index' => [
        'page' => [
            'title' => 'Megjelent cikkek áttekintése',
        ],
    ],
    'type' => [
        'review' => '[HU] Rückblick',
        'other' => '[HU] Anderes',
        'announcement' => '[HU] Ankündigung',
        'report' => '[HU] Bericht',
    ],
    'backend' => [
        'index' => [
            'page' => [
                'title' => 'Cikkek áttekintése',
            ],
            'btn' => [
                'start_new' => 'Új cikk',
            ],
        ],
    ],
    'title_de' => 'Német cím',
    'title_hu' => 'Magyar cím',
    'slug_de' => 'Német slug',
    'slug_hu' => 'Magyar slug',
    'body' => '',
    'user_id' => '',
    'status' => 'Publikáció állapota',
    'label' => 'Belső megnevezés/cím',
    'create' => [
        'title_explanation' => 'A cím lesz a cikk fő címe, és megjelenik az áttekintő listákban is. Nem ajánlott 100 karakternél hosszabbra írni, és nem kell ismétlődni a szövegben főcímként.',
        'slug_explanation' => 'A slug a cikk linkjeként szolgál. Ideális esetben ez a cím szóközök és különleges karakterek nélkül. Egy kattintással ("slug létrehozása") mindkét cím alapján elkészíthető. FONTOS: a slugot a publikálás után csak kivételes esetekben szabad módosítani.',
        'page_title' => 'Új cikk létrehozása',
        'images_upload_explanation' => 'Tölts fel képeket, amelyek galériaként jelennek meg.',
    ],
    'images' => [
        'existing' => 'A következő képek kapcsolódnak a cikkhez',
        'no_existing' => 'Nincsenek képek a cikkhez',
        'upload_explanation' => 'Minden cikkhez több kép is tartozhat. Ebben a felületen lehet képeket feltölteni. Kérjük, adj meg egy leírást német és magyar nyelven, valamint a kép szerzőjét, ha ismert.',
        'preview' => 'Feltöltött képek előnézete',
        'image_filename' => 'Kép neve',
        'image_caption_de' => 'Leírás (német)',
        'image_caption_hu' => 'Leírás (magyar)',
        'image_author' => 'Szerző',
        'image_btn_remove' => 'Eltávolítás',
        'empty_list' => 'Nincsenek kiválasztott képek',
    ],
    'section' => [
        'images' => [
            'header' => 'Új kép feltöltése',
        ],
    ],
    'form' => [
        'toasts' => [
            'msg' => [
                'image_removed' => 'A képet sikeresen eltávolítottuk!',
                'post_published' => 'A cikket publikáltuk!',
                'post_retracted' => 'A cikket visszavontuk!',
            ],
            'heading' => [
                'success' => 'Siker!',
                'warning' => 'Figyelem!',
                'error' => 'Hiba!',
            ],
            'create_success' => 'A cikk :num képpel sikeresen frissítve!',
            'edit_success' => 'A cikk :num képpel sikeresen frissítve!',
            'notifications_sent_success' => 'A közzétételről szóló értesítések elküldve',
            'notification_sent_success' => 'A közzétételről szóló értesítés elküldve',
            'eventDetachedSuccess' => 'Ke Verknüpung zum Cikkek wurde gelöscht',
            'eventAtachedSuccess' => 'Ke Verknüpung zum Cikkek wurde erstellt',
        ],
    ],
    'show' => [
        'title' => 'Cikk szerkesztése',
        'tabs' => [
            'header' => [
                'main' => 'Alapadatok',
                'content' => 'Tartalom',
                'images' => 'Képek',
            ],
        ],
        'tab' => [
            'main' => [
                'btn_make_slug' => 'Slug létrehozása',
                'published' => [
                    'header' => 'A cikk publikálva van',
                    'status_msg' => 'Ez a cikk :datum dátummal lett publikálva.',
                    'btn_reset' => 'Visszavonás',
                    'confirmation_msg' => 'Kérjük, erősítsd meg a cikk visszavonását. A publikáció megszűnik, és a cikk nem lesz látható a nyilvános oldalon.',
                    'btn_sendMails' => 'Hírlevél küldése',
                    'btn_publish_now' => 'Cikk publikálása most',
                ],
                'detach_from_event' => [
                    'confirmation_msg' => 'Kérem bestätigen, dass der Atikel a der Veranstaltung werden soll.',
                ],
                'detach' => [
                    'btn_reset' => '[HU] Verknüpfung lösen',
                ],
                'attached_event' => [
                    'header' => 'Cikkek ist verknüpft',
                    'status_msg' => 'Keser Cikkek wurde am mit der Veranstaltung :title veröffentlicht.',
                ],
                'event' => [
                    'btn_connect_now' => 'Cikkek jetzt mit Veranstaltung verknüpfen',
                ],
            ],
        ],
        'btn' => [
            'save' => 'Mentés',
        ],
        'label' => [
            'created_at' => 'Létrehozva',
            'updated_at' => 'Legutóbb módosítva',
        ],
        'delete' => [
            'confirm_prompt' => 'Der Cikkek ist veröffentlicht. Kérem die Löschung bestätigen. Der Cikkek und alle Bilder gehen verloren!',
        ],
    ],
    'notification_mail' => [
        'subject' => 'Új cikk jelent meg a weboldalunkon!',
        'header_subscriber' => 'Frissen megjelent: Egy új cikk Önnek',
        'header_member' => 'Frissen megjelent: Egy új cikk Neked',
        'content_member' => 'Izgalmas híreink vannak számodra! Egy vadonatúj cikk jelent meg a weboldalunkon – nézz be és olvasd el!',
        'content_subscriber' => 'Izgalmas híreink vannak az Ön számára – egy új cikk jelent meg a weboldalunkon! Tekintse meg most:',
        'btn_link_label' => 'tovább olvasom',
        'btn_unsubscribe_link_label' => 'Ezt az e-mailt azért kapta, mert feliratkozott frissítéseinkre. Ha módosítani szeretné beállításait vagy leiratkozni, kattintson az „Beállítások módosítása / leiratkozás” gombra.',
        'content' => [
            'excerpt' => [
                'header' => 'Előnézet',
            ],
        ],
    ],
];
