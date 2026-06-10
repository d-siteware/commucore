<?php

declare(strict_types=1);

return [
    'name' => [
        'required' => 'Kérjük, adj meg egy nevet',
    ],
    'status' => [
        'label' => 'Státusz',
        'draft' => 'vázlat',
        'pending' => 'függőben',
        'published' => 'közzétéve',
        'rejected' => 'elutasítva',
        'retracted' => 'visszavonva',

    ],
    'event_date' => 'Dátum',
    'start_time' => 'Kezdés',
    'end_time' => 'Vége',
    'title' => [
        'de' => 'Cím',
    ],
    'slug' => [
        'de' => 'slug',
    ],
    'description' => [
        'de' => 'Tartalom',
    ],
    'excerpt' => [
        'de' => 'Kivonat',
    ],
    'image' => [
        'title' => 'Címkép',
        'upload' => 'Címkép az eseményhez',
    ],
    'entry_fee' => 'Belépő',
    'entry_fee_discounted' => 'Kedvezményes belépő',
    'venue_id' => 'Helyszín',
    'venue' => 'Hely',
    'payment_link' => 'Fizetési link',
    'more' => 'tovább olvasom',
    'page' => [
        'title' => 'Összes esemény áttekintése',
    ],
    'date' => 'Dátum',
    'begins' => 'Kezdés',
    'ends' => 'Vége',
    'show' => [
        'label' => 'Részletek',
        'title' => 'Esemény',
        'page' => [
            'title' => 'Esemény',
        ],
        'timeline' => [
            'empty' => [
                'heading' => 'Még nincs program',
                'message' => 'A programot még nem tettük közzé. Iratkozz fel a hírlevelünkre, hogy értesülj a változásokról.',
            ],
            'heading' => 'Program',
        ],
        'details' => [
            'heading' => 'Áttekintés',
        ],
        'posts' => [
            'heading' => 'Cikkek',
            'poster' => [
                'heading' => 'Plakát',
                'download' => 'PDF plakát letöltése',
            ],
            'content' => 'Ehhez az eseményhez a következő cikkek jelentek meg.',
        ],
        'btn' => [
            'link_to_post' => 'Cikk elolvasása',
        ],
        'section' => [
            'published' => [
                'btn_publish_now' => 'Esemény közzététele',
            ],
        ],
        'tab' => [
            'main' => [
                'published' => [
                    'confirmation_msg' => 'Kérjük, erősítsd meg az esemény lemondását',
                    'btn_reset' => 'Esemény lemondása',
                    'btn_sendMails' => 'Körlevél küldése',
                    'btn_makeLetters' => 'Körlevél írása',
                    'header' => 'Esemény közzétéve',
                    'status_msg' => 'Az esemény közzétételre került, és most már látható.',
                    'sent_at' => 'elküldve :time',
                ],
            ],
        ],
    ],
    'make_ics' => 'Naptárbejegyzés létrehozása',
    'buy_tickets' => 'Jegyvásárlás',
    'upcoming' => [
        'title' => 'Közelgő események',
    ],
    'recent' => [
        'title' => 'Múltbeli események',
    ],
    'today' => [
        'title' => 'Ma',
    ],
    'validation_error' => [
        'event_date' => [
            'required' => 'Kérjük, adj meg egy dátumot',
            'after' => 'A dátum a jövőben kell legyen',
        ],
        'start_time' => [
            'required' => 'Kérjük, adj meg egy kezdési időt',
        ],
        'end_time' => [
            'after' => 'A végének a kezdés után kell lennie',
        ],
        'entry_fee' => '',
        'entry_fee_discounted' => '',
        'venue_id' => '',
        '' => '',
    ],
    'tabs' => [
        'nav' => [
            'dates' => 'Dátumok',
            'texts' => 'Szövegek',
            'poster' => 'Plakát',
            'payments' => 'Fizetések',
            'subscriptions' => 'Regisztrációk',
            'visitors' => 'Látogatók',
            'planing' => 'Tervezés',
        ],
    ],
    'visitor-table' => [
        'header' => [
            'name' => 'Név',
            'email' => 'E-mail',
            'gender' => 'Nem',
            'is_member' => 'Tag',
            'is_subscriber' => 'Feliratkozó',
        ],
    ],
    'subscribe' => 'Érdekel?',
    'tickets' => [
        'start' => [
            'label' => 'Jegyek foglalása',
            'btn' => 'Foglalás',
        ],
    ],
    'subscription' => [
        'text' => 'Nagyon örülünk, hogy érdeklődsz az esemény iránt. A jobb tervezés érdekében kérjük, töltsd ki az alábbi űrlapot. Így jobb képet kapunk a várható látogatószámról.',
        'consent' => [
            'label' => 'Igen, kérek értesítéseket ehhez az eseményhez.',
        ],
        'confirm_subscription_message' => 'Köszönjük! Elküldtük a megerősítő e-mailt.',
        'submit-button' => [
            'label' => 'Esemény követése',
        ],
        'subscribe-button' => [
            'label' => 'Részvétel bejelentése',
        ],
        'disclaimer' => [
            'header' => 'Fontos tudnivaló',
            'body' => 'Ezeket az adatokat kizárólag az esemény tervezéséhez használjuk, és az esemény után töröljük őket.',
        ],
        'mail' => [
            'text' => 'Kérjük, erősítsd meg a regisztrációdat az eseményre az alábbi linkre kattintva:',
            'link' => [
                'label' => 'Megerősítés',
            ],
            'bring_a_guest' => 'Örülünk, hogy :num vendéget szeretnél hozni.',
            'notification' => 'Értesítünk, ha változás történik',
            'ignore' => 'Ha nem te regisztráltál, kérjük, hagyd figyelmen kívül ezt az e-mailt.',
        ],
        'title' => 'Részvétel az eseményen',
        'name' => 'Teljes név',
        'email' => [
            'label' => 'E-mail cím',
            'confirmation' => [
                'heading' => 'Siker',
                'text' => 'Köszönjük! A részvételed biztosítva van – örülünk, hogy hamarosan találkozunk az eseményen.',
            ],
        ],
        'phone' => 'Telefonszám vagy mobilszám',
        'remarks' => 'További megjegyzések',
        'amountGuests' => 'További vendégek száma',
        'bringFriends' => 'Vendégeket hozok',
        'optional_section' => 'További adatok',
    ],
    'backend' => [
        'subscription' => [
            'title' => 'Látogatói regisztráció',
            'sendNotification' => [
                'label' => 'Megerősítő e-mail küldése a látogatónak',
            ],
            'consent' => [
                'label' => 'Látogató hozzáadása a hírlevélhez',
            ],
            'confirm_subscription_message' => 'A megerősítő e-mailt sikeresen elküldtük.',
            'submit-button' => [
                'label' => 'Regisztráció mentése',
            ],
            'subscribe-button' => [
                'label' => 'Részvétel bejelentése',
            ],
        ],
        'text-nav' => [
            'btn-make-web-texts' => 'Kivonat és slug létrehozása a linkhez',
            'btn-store' => 'Szövegek mentése',
        ],
        'texts' => [
            'title_label' => 'Cím nyelvre',
            'title_description' => 'A cím az oldalon jelenik meg',
            'description_label' => 'Tartalom/leírás nyelvre',
            'slug_label' => 'slug nyelvre',
            'slug_description' => 'A slug a linkként szolgál',
            'excerpt_label' => 'Kivonat szöveg nyelvre',
            'excerpt_description' => 'Az előnézethez használjuk. Maximum 200 karakter',
        ],
    ],
    'visitors' => [
        'empty_results_msg' => 'Még nincsenek látogatók rögzítve',
        'search_placeholder' => 'Látogatók keresése',
        'table' => [
            'paid' => 'Fizetve',
        ],
        'menu' => [
            'assign' => 'Hozzárendelés',
            'assign_member' => 'Tag',
            'assign_subscriber' => 'Feliratkozó',
            'delete' => 'Törlés',
        ],
    ],
    'visitor' => [
        'label' => 'Látogató',
        'name' => 'Látogató neve',
        'btn' => [
            'add' => [
                'label' => 'Új látogató rögzítése',
            ],
        ],
    ],
    'visitor-modal' => [
        'heading' => 'Látogató regisztrálása',
        'select_member' => 'Tag összekapcsolása',
        'select_subscribers' => 'Feliratkozó összekapcsolása',
        'name' => 'Vezetéknév, Keresztnév',
        'email' => 'E-mail cím',
        'phone' => 'Telefon',
        'gender' => 'Nem',
        'btn' => [
            'submit' => 'Mentés',
            'store' => 'Mentés + új létrehozása',
        ],
        'separator' => [
            'values' => 'Adatok',
            'optional' => 'Adatok lekérése opcionálisan innen',
            'or' => 'vagy',
        ],
        'toast' => [
            'msg' => 'Látogató sikeresen létrehozva',
            'heading' => 'Siker',
        ],
    ],
    'email' => [
        'required' => 'Meg kell adnod az e-mail címedet',
        'unique' => 'Ellenőrizd, hogy kaptál-e már tőlünk e-mailt.',
    ],
    'index' => [
        'title' => 'Cím',
        'table' => [
            'header' => [
                'name' => 'Név (belső)',
                'title' => 'Cím',
                'image' => 'Címkép',
                'subscriptions' => 'Regisztrációk',
            ],
        ],
        'btn' => [
            'start_new' => 'Új létrehozása',
            'generateList' => 'Program listázása',
        ],
    ],
    'create' => [
        'slug' => [
            'notice' => 'A slug két nyelven jön létre linkként. Ezt később nem lehet megváltoztatni!',
        ],
        'page' => [
            'title' => 'Új esemény létrehozása',
        ],
    ],
    'store' => [
        'success' => [
            'content' => 'Az esemény sikeresen létrejött.',
            'title' => 'Siker',
        ],
    ],
    'form' => [
        'name' => 'Név (belső)',
        'event_date' => 'Dátum',
        'start_time' => 'Kezdés',
        'end_time' => 'Vége',
        'title' => [
            'de' => 'Cím',
        ],
        'slug' => [
            'de' => 'slug',
        ],
        'description' => [
            'de' => 'Tartalom',
        ],
        'excerpt' => [
            'de' => 'Kivonat',
        ],
        'image' => [
            'title' => 'Címkép',
            'upload' => 'Címkép az eseményhez',
        ],
        'entry_fee' => 'Belépő',
        'entry_fee_discounted' => 'Kedvezményes belépő',
        'venue_id' => 'Helyszín',
        'venue' => [
            'select' => 'Helyszín választása',
            'new' => 'Új',
        ],
        'status' => 'Státusz',
        'status_placeholder' => 'Státusz ...',
        'payment_link' => 'Fizetési link',
        'btn' => [
            'save' => 'Mentés',
        ],
    ],
    'update' => [
        'success' => [
            'title' => 'Siker',
            'content' => 'Az esemény sikeresen frissült.',
        ],
    ],
    'delete_image' => [
        'success' => [
            'title' => 'Törölve',
            'content' => 'A címkép sikeresen törlésre került.',
        ],
    ],
    'store_image' => [
        'success' => [
            'title' => 'Feltöltés sikeres',
            'content' => 'A címkép sikeresen elmentésre került és összekapcsolódott az eseménnyel.',
        ],
    ],
    'type' => [
        'label' => 'Státusz',
        'draft' => 'Vázlat',
        'pending' => 'Függőben',
        'published' => 'Közzétéve',
        'rejected' => 'Elutasítva',
        'retracted' => 'Visszavonva',
    ],
    'assignments' => [
        'heading' => 'Feladatok',
    ],
    'timeline' => [
        'heading' => 'Ütemterv',
        'title' => 'Pont',
        'start' => 'Kezdés',
        'end' => 'Vége',
        'place' => 'Hely',
        'performer' => 'Előadó',
        'type' => 'Visszatekintés',
    ],
    'section' => [
        'published' => [
            'toast_success' => [
                'msg' => 'Az esemény sikeresen közzétételre került.',
                'heading' => 'Siker',
            ],
        ],
    ],
    'notification_mail' => [
        'subject' => 'Új esemény a weboldalunkon!',
        'header_subscriber' => 'Új megjelenés: Egy esemény számodra',
        'header_member' => 'Új megjelenés: Egy esemény számodra',
        'content_member' => 'Nagyszerű hírek számodra! Egy új eseményt tettünk közzé a weboldalunkon – örülünk, ha benézel!',
        'content_subscriber' => 'Nagyszerű hírek számodra! Egy új eseményt tettünk közzé a weboldalunkon – nézz be hozzánk!',
        'btn_link_label' => 'Tudj meg többet',
        'btn_unsubscribe_link_label' => 'Te azért kapod ezt az e-mailt, mert feliratkoztál a hírlevelünkre. Szeretnéd módosítani a beállításaidat vagy leiratkozni? Kattints ide:',
        'content' => [
            'excerpt' => [
                'header' => 'Rövid leírás',
            ],
            'details' => [
                'header' => 'Időpont',
                'event_date' => 'Dátum',
                'start_time' => 'Kezdési idő',
                'venue' => 'Helyszín',
            ],
        ],
    ],
    'poster' => [
        'separator' => [
            'text' => 'Plakát készítése az eseményhez',
        ],
        'option' => [
            'image' => 'Címkép megjelenítése',
            'text' => 'Szöveg',
            'text_excerpt' => 'Rövid szöveg',
            'text_full' => 'Hosszú szöveg',
            'preview_locale' => 'Előnézet nyelve',
        ],
        'generate' => 'Plakát generálása',
        'generate_jpeg' => 'JPEG generálása',
        'generate_pdf' => 'PDF generálása',
        'preview' => 'Előnézet',
        'jpeg_files' => 'JPEG plakátok',
        'pdf_files' => 'PDF plakátok',
        'confirm_delete' => 'Valóban törlöd a plakátot?',
    ],
    'notification_letter' => [
        'title' => 'Meghívó',
        'subject' => 'Meghívó eseményünkre',
        'greeting' => 'Kedves :name,',
        'text' => 'örömünkre szolgál, hogy értesíthetünk, hogy :datum napján egy eseményre kerül sor, amelyre szeretettel meghívunk.',
        'overview' => 'Időpont és helyszín',
        'closing_text' => 'Reméljük, részt tudsz venni, és örülünk a hamarosan történő viszontlátásnak.',
        'signature' => 'Szívélyes üdvözlettel',
        'board' => 'A Magyar Kolónia Berlin e. V. elnöksége',
        'timelines' => [
            'header' => 'Az alábbi programot tervezzük:',
            'empty' => 'Még nem tettek közzé programpontokat.',
        ],
    ],
    'program_letter' => [
        'title' => 'Program áttekintés',
        'modal' => [
            'heading' => 'Események szűrése',
            'text' => 'Az összes közzétett esemény egy PDF-listában lesz generálva. Az időbeli szűrők határozzák meg, mely események kerülnek a dokumentumba.',
            'radio' => [
                'year' => [
                    'label' => 'Aktuális év',
                    'desc' => 'Az aktuális év összes közzétett eseménye',
                ],
                'upcoming' => [
                    'label' => 'Mától',
                    'desc' => 'Minden jövőbeli közzétett esemény mától kezdődően',
                ],
                'all' => [
                    'label' => 'Összes',
                    'desc' => 'Minden múltbeli és jövőbeli közzétett esemény',
                ],
            ],
            'btn' => 'Lista létrehozása',
        ],
    ],
    'boxoffice' => [
        'btn' => [
            'openmodal' => 'Helyszíni jegyvásárlás',
        ],
    ],
    'subscriptions' => [
        'btn' => [
            'add_new' => 'új regisztráció hozzáadása',
        ],
        'table' => [
            'name' => 'Név',
            'date' => 'Dátum',
            'email' => 'E-mail',
            'notifications' => 'Értesítések',
            'phone' => 'Telefon',
            'guests' => '# Vendég',
            'confirmed_at' => 'E-mail megerősítve',
        ],
    ],
    'payments' => [
        'table' => [
            'text' => 'Szöveg',
            'date' => 'Dátum',
            'visitor' => 'Látogató',
            'amount' => 'Összeg',
        ],
        'btn' => [
            'add_new' => 'Új fizetés rögzítése',
            'create_report' => 'Jelentés készítése',
        ],
    ],
    'modal' => [
        'resend_notification' => [
            'heading' => 'Kérjük, erősítsd meg',
            'text_1' => 'Az értesítés már elküldésre került :date napján.',
            'text_2' => 'Szeretnéd újra elküldeni?',
            'btn_cancel' => 'Mégse',
            'btn_confirm' => 'Igen, küldd újra',
        ],
    ],
];
