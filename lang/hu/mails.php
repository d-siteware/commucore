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
        'text' => [
            'sent' => 'Az e-mail elküldve :count címzettnek!',
        ],
    ],
    'tab' => [
        'create' => 'Létrehozás',
        'history' => 'Előzmények',
        'external_list' => 'Külső lista',
    ],
    'tool' => [
        'options_heading' => 'E-mail küldési opciók',
        'reason' => 'Levél oka',
        'new_event' => 'Új esemény',
        'new_article' => 'Új cikk',
        'change' => 'Cikk/esemény módosítása',
        'include_external_list' => 'Külső levelezőlista bekapcsolása',
        'include_external_list_desc' => 'Bekapcsolásakor egy link kerül az e-mail végére, amely a megfelelő oldalra vezet.',
        'create_link' => 'Link létrehozása',
        'create_link_desc' => 'Bekapcsolásakor egy link kerül az e-mail végére, amely a megfelelő oldalra vezet.',
        'personal_greeting' => 'Személyes megszólítás',
        'personal_greeting_desc' => 'Bekapcsolásakor a címzett névvel lesz megszólítva. Kikapcsolva nem jön létre köszöntés!',
        'attachments' => 'E-mail mellékletek',
        'attachments_desc' => 'Fájlok csatolása az e-mailhez?',
    ],
    'attached_file' => 'Csatolt fájl',
    'empty_mailing_list' => 'Nincsenek ellenőrzött bejegyzések a levelezőlistában',
    'mailing_list_subscriptions' => [
        'new_in_month' => 'új regisztráció ebben a hónapban: :month',
        'one_in_month' => 'Egy regisztráció ebben a hónapban: :month',
        'none_in_month' => 'Nincs új regisztráció ebben a hónapban: :month',
        'new_in_year' => 'új regisztráció :year',
        'one_in_year' => 'Egy új regisztráció :year',
        'none_in_year' => 'Nincs új regisztráció :year',
    ],
    'mailing_list_unsubscribe_greeting' => 'Üdvözlettel / Viszlát',
    'history_heading' => 'Elküldött körlevelek',
    'history_description' => 'Az összes eddig elküldött tömeges e-mail dokumentációja.',
    'history_empty' => 'Még nem küldtél ki körlevelet.',
    'history_recipients_total' => 'Címzettek összesen',
    'history_members' => 'Tagok',
    'history_mailing_list' => 'Levelezőlista',
    'history_attachments' => 'Mellékletek',
    'history_attachments_label' => 'Csatolt fájlok (csak fájlnevek tárolva)',
    'history_sender' => 'Feladó',
    'history_included_mailing_list' => 'Levelezőlista beküldve',
    'history_personal_greeting_enabled' => 'Személyes megszólítás',
    'history_attachments_enabled' => 'Mellékletek engedélyezve',
    'footer_greeting' => 'Üdvözlettel,',
    'subscription_footer_greeting' => 'Üdvözlettel,',
];
