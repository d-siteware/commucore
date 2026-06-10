<?php

declare(strict_types=1);

return [
    'page_title' => 'E-mail küldés',

    'president' => [
        'deputy' => 'Alelnök',
    ],
    'treasury' => 'Pénztáros',
    'secretariat' => [
        'hu' => 'Magyar Titkárság',
        'de' => 'Német Titkárság',
    ],
    'cultural' => [
        'director' => 'Kulturális vezető',
    ],
    'social' => [
        'affairs' => [
            'deputy' => 'Szociális ügyek helyettes vezetője',
        ],
    ],
    'contact' => 'Kapcsolat',
    'invitation' => [
        'subject' => 'Meghívó a(z) :name portáljára',
        'greeting' => 'Szia :name',
        'header' => 'Kérlek, erősítsd meg az e-mail-címedet',
        'text' => 'A(z) :name aktív tagjaként szeretettel meghívunk, hogy regisztrálj felhasználóként a portálunkon.',
        'btn' => [
            'label' => 'Kattints ide a regisztráció befejezéséhez',
        ],
    ],
    'acceptance' => [
        'subject' => 'Jóváhagyott tagsági kérelem a(z) :name-nél',
        'greeting' => 'Szia :name',
        'header' => 'Isten hozott',
        'text' => 'Örömmel értesítünk, hogy a(z) :name tagsági kérelmedet elbíráltuk és elfogadtuk.',
    ],
    'audit_invitation' => [
        'header' => 'Szükségünk van rád!',
        'text' => 'Meghívunk, hogy ellenőrizd a :range időszak havi pénztári jelentését. Kezdheted az ellenőrzést az alábbi linkre kattintva, vagy a portálon a Pénztár → Jelentések menüpontban, majd a megfelelő jelentésnél kattints az "Ellenőrzés most" gombra. Köszönjük a fáradozásodat!',
    ],
    'audit' => [
        'invitation' => [
            'subject' => 'Havi pénztári jelentés ellenőrzése',
            'link_label' => 'Itt érhető el az ellenőrzés',
        ],
    ],
    'members' => [
        'heading' => 'E-mail minden, e-mail-címmel rendelkező tagnak',
        'content' => 'Az e-mail abban a nyelvben készül, amelyet a felhasználó a profiljában megadott.',
        'btn' => [
            'preview' => 'Előnézet (csatolmányok nélkül)',
            'test_mail' => 'Teszt e-mail nekem (csatolmányok nélkül)',
            'submit' => 'Küldés',
            'cancel' => 'Mégse',
            'final' => 'Igen, gyerünk',
        ],
        'subject' => 'Tárgy',
        'message' => 'Üzenet',
        'label' => 'Link felirat',
        'url' => 'Link URL',
        'confirm' => [
            'header' => 'Kérlek, küldés előtt alaposan ellenőrizd',
            'warning' => 'Sok tag fogja megkapni az üzenetet. Hiba esetén sok kellemetlenség adódhat.',
            'info' => 'Küldés előtt a rendszer rögzíti a naplóban, hogy ki, mikor és milyen e-mailt küldött.',
        ],
    ],
    'member' => [
        'separator' => [
            'text' => 'Tartalom',
            'links' => 'Linkek',
            'attachments' => 'Csatolmányok (csak pdf|jpg|jpeg|png|tif)',
            'options' => 'Opciók',
        ],
    ],
    'mailing_list' => [
        'label' => [
            'email' => 'E-mail-cím',
        ],
        'text' => [
            'privacy' => 'Hozzájárulok, hogy adataimat a hatályos adatvédelmi törvényeknek megfelelően tárolják és feldolgozzák.',
            'privacy_full' => 'Adataidat kizárólag eseményekről és cikkekről szóló értesítésekhez használjuk, és nem adjuk tovább harmadik félnek.',
        ],
        'btn_subscribe' => [
            'label' => 'Feliratkozás a listára',
        ],
        'header' => 'Kapj értesítéseket a(z) :name új eseményeiről és cikkeiről',
        'options_group_header' => 'Témák kiválasztása',
        'options_header' => 'Beállítások',
        'options' => [
            'all_label' => 'Mindent!',
            'events_label' => 'Csak eseményekhez',
            'posts_label' => 'Csak cikkekhez',
            'all_description' => 'Kapj értesítést, amint egy új esemény vagy cikk megjelenik, vagy változás történik.',
            'events_description' => 'Kapcsold be ezt a mezőt, ha csak új eseményekről szeretnél értesítést kapni.',
            'posts_description' => 'Kapcsold be ezt a mezőt, ha csak új cikkekről szeretnél értesítést kapni.',
            'update_notifications_label' => 'Frissítések',
            'update_notifications_description' => 'Kérlek, értesítést küldeni az események vagy cikkek frissítéseiről is',
        ],
        'validation_error' => [
            'email' => 'Kérlek, adj meg egy e-mail-címet',
            'terms_accepted' => 'Kérlek, fogadd el az adatvédelmi nyilatkozatot',
        ],
        'show' => [
            'confirmation_msg' => 'Sikeresen megerősítetted az e-mail-címedet',
            'update_msg' => 'Sikeresen módosítottad a beállításaidat',
            'change' => 'Változtasd meg a kiválasztásod, hogy a jövőben ezekről a témákról értesítéseket kapj.',
            'btn' => [
                'save' => 'Kiválasztás mentése',
            ],
        ],
        'confirmation_email_subject' => 'Kérlek, erősítsd meg az e-mail-címedet',
        'confirmation_email_msg' => 'Köszönjük a feliratkozást a hírlevélre! Kérlek, erősítsd meg a feliratkozást az alábbi gombra kattintva. Így az érdeklődésednek megfelelő frissítéseket kapsz.',
        'confirmation_email_msg_changes' => 'Beállításaidat bármikor módosíthatod egy link segítségével, amelyet a jövőbeli e-mailekben mellékelünk.',
        'confirmation_email_msg_ignore' => 'Ha nem te iratkoztál fel, egyszerűen hagyd figyelmen kívül ezt az e-mailt.',
        'confirmation_email' => [
            'selected_summary' => 'Ezek a beállítások érvényesek az e-mail-címedre:',
            'selected_events' => 'Értesítések kérése új eseményekről',
            'selected_posts' => 'Értesítések kérése új cikkekről',
            'selected_notifications' => 'Emellett értesítések kérése változásokról',
            'locale' => 'Nyelv, amelyen az értesítéseket szeretnéd kapni',
            'btn' => [
                'verify_now' => 'E-mail-cím megerősítése',
            ],
        ],
        'subscription_success' => 'Köszönjük! Elküldtünk egy e-mailt a megerősítéshez',
        'verify' => [
            'header' => 'Kérlek, erősítsd meg az e-mail-címedet',
            'btn' => 'Megerősítés',
        ],
        'unsubscribe' => [
            'label' => 'Leiratkozás',
            'error_heading' => 'Váratlan hiba',
            'error_msg' => 'Sajnos az e-mail-címedet váratlanul nem sikerült törölni. Elnézést kérünk a kellemetlenségért. A rendszer jelezte a hibát, és már dolgozunk a megoldáson. Amint a törlés sikeresen megtörtént, értesítünk. Addig is kérjük megértésedet, ha esetleg továbbra is kapsz értesítéseket.',
            'success_msg' => 'Az e-mail-címedet sikeresen eltávolítottuk a listánkból. A jövőben nem kapsz tőlünk további értesítéseket.',
        ],
        'verified_emails' => 'Megerősített e-mail-címek',
    ],
    'unsubscribe_link_label' => 'Beállítások módosítása / leiratkozás',
    'toast' => [
        'header' => [
            'success' => 'Siker',
        ],
    ],
];
