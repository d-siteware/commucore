<?php

declare(strict_types=1);

return [
    'index' => [
        'page' => [
            'title' => 'Publikált cikkek áttekintése',
        ],
    ],

    'type' => [
        'label' => 'Cikk típus',
        'review' => 'Visszatekintés',
        'other' => 'Egyéb',
        'announcement' => 'Bejelentés',
        'report' => 'Beszámoló',
    ],

    'backend' => [
        'index' => [
            'page' => [
                'title' => 'Cikkek áttekintése',
            ],
            'btn' => [
                'start_new' => 'Új cikk',
            ],
            'filter_status' => 'Státusz szűrése...',
            'filter_type' => 'Típus szűrése...',
            'header_label' => 'Név (belső)',
            'header_published' => 'Publikálva',
            'header_status' => 'Státusz',
            'header_type' => 'Típus',
            'header_title' => 'Cím',
            'action_edit' => 'szerkesztés',
            'action_delete' => 'törlés',
        ],
    ],

    'body' => 'Tartalom',
    'user_id' => 'Felhasználó / Szerző',
    'status' => 'Publikálási státusz',
    'label' => 'Belső azonosító/cím',
    'title' => 'Cím',
    'slug' => 'Slug',

    'create' => [
        'page' => [
            'title' => 'Új cikk létrehozása',
        ],
        'btn' => [
            'submit' => 'Cikk létrehozása',
        ],
        'success' => [
            'title' => 'Cikk létrehozva',
            'msg' => 'A cikk sikeresen létrehozva.',
        ],
        'steps' => [
            'head' => 'Fej adatok',
            'content' => 'Tartalom',
            'images' => 'Képek',
        ],
        'title_explanation' => 'A cím lesz a cikk címsora és egyben a lista bejegyzés is az áttekintésekben. Ne legyen sokkal hosszabb 100 karakternél, és ne ismétlődjön meg a szövegtörzsben címsorként.',
        'slug_explanation' => 'A slug a cikk linkjeként szolgál. Ideális esetben a cím legyen szóközök és speciális karakterek nélkül. A (slug létrehozása) gombra kattintva mindkét címhez elkészül. FONTOS, hogy a cikk publikálása után a slug-ot csak szükség esetén szabad megváltoztatni.',
        'page_title' => 'Új cikk létrehozása',
        'images_upload_explanation' => 'Töltsön fel képeket, amelyek galériaként jelennek meg.',
    ],

    'images' => [
        'existing' => 'A következő képek kapcsolódnak a cikkhez',
        'no_existing' => 'Nincsenek képek a cikkhez',
        'upload_explanation' => 'Minden cikk több képet is tartalmazhat. Ebben a maszkban tölthetők fel képek. Kérjük, adjon meg egy rövid leírást és a kép szerzőjét, ha ismert.',
        'preview' => 'Feltöltött képek előnézete',
        'image_filename' => 'Kép neve',
        'image_caption' => 'Leírás',
        'image_author' => 'Szerző',
        'image_btn_remove' => 'Eltávolítás',
        'empty_list' => 'Nincs kép kiválasztva',
        'btn' => [
            'upload' => 'Képek feltöltése',
            'remove' => 'Kép eltávolítása',
        ],
        'upload' => 'Képek feltöltése',
        'dropzone' => [
            'heading' => 'Húzza ide a képeket vagy kattintson a területre',
            'text' => 'JPG, PNG, WebP, GIF, maximum 20 MB',
        ],
    ],

    'section' => [
        'images' => [
            'gallery' => 'Képgaléria',
            'header' => 'Új kép feltöltése',
        ],
    ],

    'form' => [
        'toasts' => [
            'msg' => [
                'image_removed' => 'Kép sikeresen eltávolítva!',
                'post_published' => 'A cikk publikálásra került!',
                'post_retracted' => 'A cikk visszavonásra került!',
                'post_deleted' => 'A cikk törlésre került!',
            ],
            'heading' => [
                'success' => 'Siker!',
                'warning' => 'Figyelmeztetés!',
                'error' => 'Figyelem, hiba!',
            ],
            'create_success' => 'A cikk :num képpel sikeresen frissítve!',
            'edit_success' => 'A cikk :num képpel sikeresen frissítve!',
            'notifications_sent_success' => 'A publikálásról szóló értesítések elküldve',
            'notification_sent_success' => 'A publikálásról szóló értesítések elküldve',
            'eventDetachedSuccess' => 'A cikkhez való kapcsolat törölve',
            'eventAtachedSuccess' => 'A cikkhez való kapcsolat létrehozva',
        ],
    ],

    'show' => [
        'title' => 'Cikk szerkesztése',
        'tabs' => [
            'header' => [
                'main' => 'Fej adatok',
                'content' => 'Tartalom',
                'images' => 'Képek',
            ],
        ],
        'tab' => [
            'main' => [
                'btn_make_slug' => 'Slug létrehozása',
                'published' => [
                    'header' => 'Cikk publikálva',
                    'status_msg' => 'Ez a cikk :datum -án került publikálásra.',
                    'btn_reset' => 'Visszavonás',
                    'confirmation_msg' => 'Kérjük, erősítse meg a cikk visszavonását. Ezután már nem lesz látható az oldal nyilvános részén.',
                    'btn_sendMails' => 'Körlevél küldése',
                    'btn_publish_now' => 'Cikk publikálása most',
                ],
                'attached_event' => [
                    'header' => 'Cikk hozzárendelve',
                    'status_msg' => 'Ez a cikk a(z) :title rendezvénnyel együtt lett publikálva.',
                ],
                'detach_from_event' => [
                    'confirmation_msg' => 'Kérjük, erősítse meg, hogy a cikk elválasztásra kerüljön a rendezvénytől.',
                ],
                'detach' => [
                    'btn_reset' => 'Kapcsolat megszüntetése',
                ],
                'event' => [
                    'btn_connect_now' => 'Cikk összekapcsolása rendezvénnyel most',
                ],
            ],
        ],
        'btn' => [
            'save' => 'Mentés',
        ],
        'label' => [
            'created_at' => 'Létrehozva',
            'updated_at' => 'Utoljára módosítva',
        ],
        'delete' => [
            'confirm_prompt' => 'A cikk publikálva van. Kérjük, erősítse meg a törlést. A cikk és az összes kép elvész!',
        ],
    ],

    'notification_mail' => [
        'subject' => 'Új cikk jelent meg weboldalunkon!',
        'header_subscriber' => 'Frissen megjelent: Egy új cikk Önnek',
        'header_member' => 'Frissen megjelent: Egy új cikk Neked',
        'greeting' => [
            'member_male' => 'Kedves :name,',
            'member_female' => 'Kedves :name,',
            'subscriber' => 'Szia,',
        ],
        'content_member' => 'izgalmas híreink vannak a számodra! Egy vadonatúj cikk jelent meg weboldalunkon – nézz be hát!',
        'content_subscriber' => 'izgalmas híreink vannak az Ön számára – egy vadonatúj cikk jelent meg weboldalunkon! Nézzen be hozzá:',
        'btn_link_label' => 'tovább olvasom',
        'btn_unsubscribe_link_label' => 'Ezt az e-mailt azért kapja, mert feliratkozott frissítéseinkre. Ha módosítani szeretné beállításait vagy leiratkozna, kattintson a „Beállítások módosítása / leiratkozás" gombra.',
        'content' => [
            'excerpt' => [
                'header' => 'Előnézet',
            ],
        ],
    ],

    'editor_description' => 'Szerkesztő :locale szöveghez Markdown funkcióval',
    'editor_help' => 'Segítség',
    'language_label' => 'Nyelv',
];
