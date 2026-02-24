<?php

declare(strict_types=1);

return [
    'name' => [
        'required' => 'Kérjük, adjon meg egy nevet',
    ],
    'status' => 'Állapot',
    'event_date' => 'Dátum',
    'start_time' => 'Kezdés',
    'end_time' => 'Befejezés',
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
        'title' => 'Borítókép',
        'upload' => 'Borítókép a rendezvényhez',
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
    'ends' => 'Befejezés',
    'show' => [
        'label' => 'Részletek',
        'title' => 'Esemény',
        'page' => [
            'title' => 'Esemény',
        ],
        'timeline' => [
            'empty' => [
                'heading' => 'Még nincs elérhető program',
                'message' => 'A programterv még nem került közzétételre. Jelentkezz a levelezőlistánkra, hogy naprakész maradj!',
            ],
            'heading' => 'Programterv',
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
            'content' => 'Ehhez az eseményhez az alábbi cikkek jelentek meg.',
        ],
        'btn' => [
            'link_to_post' => 'Cikk olvasása',
        ],
        'section' => [
            'published' => [
                'btn_publish_now' => 'Esemény közzététele',
            ],
        ],
        'tab' => [
            'main' => [
                'published' => [
                    'confirmation_msg' => 'Kérjük, erősítse meg a rendezvény lemondását',
                    'btn_reset' => 'Esemény lemondása',
                    'btn_sendMails' => 'Körlevél küldése',
                    'btn_makeLetters' => 'Levél írása',
                    'header' => 'Az esemény közzétéve',
                    'status_msg' => 'Ez a rendezvény közzé lett téve és mostantól látható.',
                    'sent_at' => 'elküldve :time',
                ],
            ],
        ],
    ],
    'make_ics' => 'Naptárbejegyzés létrehozása',
    'buy_tickets' => 'Jegyvásárlás most',
    'upcoming' => [
        'title' => 'Következő események',
    ],
    'recent' => [
        'title' => 'Elmúlt események',
    ],
    'today' => [
        'title' => 'Ma',
    ],
    'validation_error' => [
        'event_date' => [
            'required' => 'Kérjük, adjon meg egy dátumot',
            'after' => 'A dátumnak a jövőben kell lennie',
        ],
        'start_time' => [
            'required' => 'Kérjük, adjon meg egy kezdési időpontot',
        ],
        'end_time' => [
            'after' => 'A befejezésnek a kezdés után kell lennie',
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
            'is_subscriber' => 'Regisztráló',
        ],
    ],
    'subscribe' => 'Érdekel?',
    'tickets' => [
        'start' => [
            'label' => 'Jegyfoglalás',
            'btn' => 'Foglalás',
        ],
    ],
    'subscription' => [
        'text' => 'Nagyon örülünk, hogy érdekel az esemény! A jobb tervezés érdekében kitöltheted az alábbi űrlapot. Így jobban átlátjuk, hány látogatóra számíthatunk.',
        'consent' => [
            'label' => 'Igen, kérem küldjenek üzeneteket erről az eseményről.',
        ],
        'confirm_subscription_message' => 'Köszönjük! Küldtünk neked egy e-mailt a visszaigazoláshoz.',
        'submit-button' => [
            'label' => 'Esemény követése',
        ],
        'subscribe-button' => [
            'label' => 'Részvétel bejelentése',
        ],
        'disclaimer' => [
            'header' => 'Fontos információ',
            'body' => 'Ezeket az adatokat kizárólag az esemény tervezésére használjuk, és az esemény végeztével töröljük őket.',
        ],
        'mail' => [
            'text' => 'Kérjük, erősítse meg a jelentkezését az eseményre az alábbi linkre kattintva:',
            'link' => [
                'label' => 'Most visszaigazolom',
            ],
            'bring_a_guest' => 'Örülünk, hogy :num vendéget hozol magaddal!',
            'notification' => 'Jelzünk, ha bármilyen változás történik.',
            'ignore' => 'Ha nem te jelentkeztél, kérjük, hagyd figyelmen kívül ezt az e-mailt.',
        ],
        'title' => 'Részt veszek az eseményen',
        'name' => 'Teljes név',
        'email' => [
            'label' => 'E-mail cím',
            'confirmation' => [
                'heading' => 'Sikeres',
                'text' => 'Köszönjük! A részvételed biztosított – örömmel várunk az eseményen.',
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
            'title' => 'Látogató regisztráció',
            'sendNotification' => [
                'label' => 'Visszaigazoló e-mail küldése a látogatónak',
            ],
            'consent' => [
                'label' => 'Látogató hozzáadása a levelezőlistához',
            ],
            'confirm_subscription_message' => 'A visszaigazoló e-mail sikeresen elküldve.',
            'submit-button' => [
                'label' => 'Jelentkezés mentése',
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
            'title_label' => 'Cím nyelvhez',
            'title_description' => 'A cím az oldalon lesz használva',
            'description_label' => 'Tartalom/Leírás nyelvhez',
            'slug_label' => 'slug nyelv',
            'slug_description' => 'A slug linkként lesz használva',
            'excerpt_label' => 'Szöveg kivonat nyelvhez',
            'excerpt_description' => 'Előnézethez használva. Kérjük max 200 karakter',
        ],
    ],
    'visitors' => [
        'empty_results_msg' => 'Még nem regisztráltak látogatók',
        'search_placeholder' => 'Látogatók keresése',
        'table' => [
            'paid' => 'Fizetve',
        ],
        'menu' => [
            'assign' => 'Hozzárendelés',
            'assign_member' => 'Tag',
            'assign_subscriber' => 'Regisztráló',
            'delete' => 'Törlés',
        ],
    ],
    'visitor' => [
        'btn' => [
            'add' => [
                'label' => 'Új látogató rögzítése',
            ],
        ],
    ],
    'visitor-modal' => [
        'heading' => 'Látogató regisztrálása',
        'select_member' => 'Tag hozzákapcsolása',
        'select_subscribers' => 'Regisztráló hozzákapcsolása',
        'name' => 'Név, keresztnév',
        'email' => 'E-mail cím',
        'phone' => 'Telefonszám',
        'gender' => 'Nem',
        'btn' => [
            'submit' => 'Mentés',
            'store' => 'Mentés + Új létrehozása',
        ],
        'separator' => [
            'values' => 'Adatok',
            'optional' => 'Opcionális adatok átvétele innen',
            'or' => 'vagy',
        ],
        'toast' => [
            'msg' => 'Látogató sikeresen rögzítve!',
            'heading' => 'Siker',
        ],
    ],
    'email' => [
        'required' => 'Szükségünk van az e-mail címedre',
        'unique' => 'Ellenőrizd, hogy kaptál-e már tőlünk e-mailt.',
    ],
    'index' => [
        'title' => 'Cím',
        'table' => [
            'header' => [
                'name' => 'Név (belső)',
                'title' => 'Cím',
                'image' => 'Borítókép',
                'subscriptions' => 'Regisztrációk',
            ],
        ],
        'btn' => [
            'start_new' => 'Új létrehozása',
            'generateList' => 'Program lista',
        ],
    ],
    'create' => [
        'slug' => [
            'notice' => 'A slug két nyelven készül el linkként. Ezt később nem lehet módosítani!',
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
        'start_time' => 'Kezdés ideje',
        'end_time' => 'Befejezés ideje',
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
            'title' => 'Borítókép',
            'upload' => 'Borítókép az eseményhez',
        ],
        'entry_fee' => 'Belépő',
        'entry_fee_discounted' => 'Kedvezményes belépő',
        'venue_id' => 'Helyszín',
        'venue' => [
            'select' => 'Helyszín kiválasztása',
            'new' => 'Új',
        ],
        'status' => 'Állapot',
        'status_placeholder' => 'Állapot ...',
        'payment_link' => 'Fizetési link',
        'btn' => [
            'save' => 'Mentés',
        ],
    ],
    'update' => [
        'success' => [
            'title' => 'Siker',
            'content' => 'Az esemény sikeresen frissítve.',
        ],
    ],
    'delete_image' => [
        'success' => [
            'title' => 'Törlés megtörtént',
            'content' => 'A borítókép sikeresen törölve.',
        ],
    ],
    'store_image' => [
        'success' => [
            'title' => 'Feltöltés sikeres',
            'content' => 'A borítókép sikeresen mentve és az eseménnyel összekapcsolva.',
        ],
    ],
    'type' => [
        'label' => 'Állapot',
        'draft' => 'Piszkozat',
        'pending' => 'Függőben',
        'published' => 'Közzétéve',
        'rejected' => 'Elutasítva',
        'retracted' => 'Visszavonva',
    ],
    'assignments' => [
        'heading' => 'Feladatok',
    ],
    'timeline' => [
        'heading' => 'Menetrend',
        'title' => 'Pont',
        'start' => 'Kezdés',
        'end' => 'Befejezés',
        'place' => 'Helyszín',
        'performer' => 'Előadó',
        'type' => 'Visszatekintés',
    ],
    'section' => [
        'published' => [
            'toast_success' => [
                'msg' => 'Az esemény sikeresen közzétéve.',
                'heading' => 'Siker',
            ],
        ],
    ],
    'notification_mail' => [
        'subject' => 'Új esemény a weboldalunkon!',
        'header_subscriber' => 'Újdonság: Egy esemény neked',
        'header_member' => 'Újdonság: Egy esemény neked',
        'content_member' => 'Szuper hír! Új eseményt tettünk közzé a weboldalunkon – örülnénk, ha benéznél!',
        'content_subscriber' => 'Szuper hír! Új eseményt tettünk közzé a weboldalunkon – nézz be hozzánk!',
        'btn_link_label' => 'Tudj meg többet',
        'btn_unsubscribe_link_label' => 'Ezt az e-mailt azért kaptad, mert feliratkoztál a frissítéseinkre. Szeretnéd módosítani a beállításaidat vagy leiratkozni? Kattints ide:',
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
    ],
    'notification_letter' => [
        'title' => 'Meghívó',
        'subject' => 'Meghívó rendezvényünkre',
        'greeting' => 'Kedves :name!',
        'text' => 'Örömmel értesítünk, hogy :datum időpontban egy rendezvényt szervezünk, amelyre szeretettel meghívunk Téged is.',
        'overview' => 'Időpont és helyszín',
        'closing_text' => 'Reméljük, hogy el tudsz jönni, és örömmel találkozunk újra.',
        'signature' => 'Üdvözlettel',
        'board' => 'A Magyar Kolónia Berlin e. V. elnöksége',
        'timelines' => [
            'header' => 'A tervezett program:',
            'empty' => 'Még nem kerültek közzétételre programpontok.',
        ],
    ],
    'program_letter' => [
        'title' => 'Programáttekintés',
        'modal' => [
            'heading' => 'Események szűrése',
            'text' => 'Az összes közzétett esemény PDF formátumban kerül legenerálásra. Az időszűrők határozzák meg, hogy mely események kerülnek bele a dokumentumba.',
            'radio' => [
                'year' => [
                    'label' => 'Aktuális év',
                    'desc' => 'Az aktuális évben közzétett összes esemény',
                ],
                'upcoming' => [
                    'label' => 'Mától',
                    'desc' => 'Minden jövőbeni, ma vagy később közzétett esemény',
                ],
                'all' => [
                    'label' => 'Összes',
                    'desc' => 'Az összes múltbeli és jövőbeni közzétett esemény',
                ],
            ],
            'btn' => 'Lista létrehozása',
        ],
    ],
    'boxoffice' => [
        'btn' => [
            'openmodal' => 'Pénztár',
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
            'guests' => '# Vendégek',
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
            'create_report' => 'Jelentés létrehozása',
        ],
    ],
    'modal' => [
        'resend_notification' => [
            'heading' => 'Kérjük, erősítse meg',
            'text_1' => 'Az értesítés már el lett küldve :date.',
            'text_2' => 'Újra el kell küldeni?',
            'btn_cancel' => 'Mégsem',
            'btn_confirm' => 'Igen, kérem küldje el újra',
        ],
    ],
];