<?php

declare(strict_types=1);



return [
    'president' => '[HU] Präsident',
    'treasury' => 'Pénztáros',
    'secretariat' => [
        'hu' => 'Magyar titkárság',
        'de' => 'Német titkárság',
    ],
    'cultural' => [
        'director' => 'Kulturális vezető',
    ],
    'social' => [
        'affairs' => '[HU] Sozialleitung',
    ],
    'contact' => 'Kapcsolat',
    'invitation' => [
        'subject' => 'Meghívás a :name portáljára',
        'greeting' => 'Szia :name',
        'header' => 'Kérjük, erősítsd meg az e-mail címedet',
        'text' => 'A :name aktív tagjaként szeretettel meghívunk, hogy regisztrálj a portálunkra.',
        'btn' => [
            'label' => 'Kattints ide a regisztráció befejezéséhez',
        ],
    ],
    'acceptance' => [
        'subject' => 'Jóváhagyott tagsági kérelem a :name-nél',
        'greeting' => 'Szia :name',
        'header' => 'Üdvözlünk',
        'text' => 'Örömmel értesítünk, hogy tagsági kérelmedet a :name elfogadta.',
    ],
    'audit_invitation' => [
        'header' => 'Szükségünk van rád!',
        'text' => 'Meghívást kaptál a :range időszak havi pénztári jelentésének ellenőrzésére. Az alábbi linkre kattintva elindíthatod az ellenőrzést, vagy a portálon a Pénztár -> Jelentések menüpont alatt a megfelelő jelentésnél a "most ellenőriz" gombra kattintva végezheted el. Köszönjük a segítségedet!',
    ],
    'audit' => [
        'invitation' => [
            'subject' => 'A havi pénztári jelentés ellenőrzése',
            'link_label' => '[HU] Hier geht es zur Prüfung',
        ],
    ],
    'members' => [
        'heading' => 'E-mail küldése minden tag számára, akik megadták e-mail címüket',
        'content' => 'Az e-mail azon a nyelven készül, amelyet a felhasználó a profiljában megadott.',
        'btn' => [
            'preview' => 'Előnézet (Mellékletek nélkül)',
            'test_mail' => 'Teszt e-mail küldése magamnak (Mellékletek nélkül)',
            'submit' => 'Küldés',
            'cancel' => 'Mégsem',
            'final' => 'Biztos vagyok benne, küldés!',
        ],
        'confirm' => [
            'header' => 'Küldés előtt gondoljuk át',
            'warning' => 'Sok tag fogja megkapni ezt az üzenetet. Ha hiba történik, kellemetlen következmények lehetnek.',
            'info' => 'Küldés előtt egy bejegyzés készül a történelemben arról, hogy ki, mikor és milyen e-mailt küldött.',
        ],
        'subject' => [
            'hu' => 'Tárgy',
            'de' => '[HU] Betreff',
        ],
        'message' => [
            'hu' => 'Üzenet',
            'de' => 'Üzenet',
        ],
    ],
    'member' => [
        'separator' => [
            'text' => 'Szövegek',
            'links' => 'Linkek',
            'attachments' => 'Mellékletek (csak pdf|jpg|jpeg|png|tif)',
            'options' => '[HU] Optionen',
        ],
    ],
    'mailing_list' => [
        'label' => [
            'email' => 'E-mail cím',
        ],
        'text' => [
            'privacy' => 'Elfogadom, hogy adataimat tárolják és az érvényes adatvédelmi törvények szerint kezelik.',
            'privacy_full' => 'Az Ön adatait kizárólag eseményekről és cikkekről szóló értesítések küldésére használjuk, és nem adjuk át illetéktelen harmadik feleknek.',
        ],
        'btn_subscribe' => [
            'label' => 'Feliratkozás a listára',
        ],
        'header' => 'Értesüljön a :name új eseményeiről és cikkeiről',
        'options_group_header' => 'Témakörök kiválasztása',
        'options_header' => 'Beállítások',
        'options' => [
            'all_label' => 'Mindent!',
            'events_label' => 'Csak eseményekről kérek értesítést',
            'posts_label' => 'Csak cikkekről kérek értesítést',
            'all_description' => 'Értesítést kap minden új eseményről, cikkről és módosításokról.',
            'events_description' => 'Jelölje be ezt az opciót, ha csak eseményekről szeretne értesítést kapni.',
            'posts_description' => 'Jelölje be ezt az opciót, ha csak cikkekről szeretne értesítést kapni.',
            'update_notifications_label' => 'Frissítések',
            'update_notifications_description' => 'Értesítést kérek egy esemény vagy cikk frissítéseiről is.',
        ],
        'validation_error' => [
            'email' => 'Kérjük, adjon meg egy e-mail címet',
            'terms_accepted' => 'Kérjük, fogadja el az adatvédelmi nyilatkozatot',
        ],
        'show' => [
            'confirmation_msg' => 'Sikeresen megerősítette e-mail címét',
            'update_msg' => 'Sikeresen megerősítette az adatokat',
            'change' => 'Módosítsa beállításait, hogy értesítéseket kapjon ezekről a témákról.',
            'btn' => [
                'save' => 'Beállítások mentése',
            ],
        ],
        'confirmation_email_subject' => 'Kérjük, erősítse meg az e-mail címét',
        'confirmation_email_msg' => 'Köszönjük, hogy feliratkozott hírlevelünkre! Kérjük, erősítse meg feliratkozását az alábbi gombra kattintva, hogy az Ön érdeklődési körének megfelelő frissítéseket kaphassa.',
        'confirmation_email_msg_changes' => 'Beállításait bármikor módosíthatja egy link segítségével, amelyet a jövőbeni e-mailekben mellékelünk.',
        'confirmation_email_msg_ignore' => 'Ha nem iratkozott fel, egyszerűen hagyja figyelmen kívül ezt az e-mailt.',
        'confirmation_email' => [
            'selected_summary' => 'A következő beállítások vonatkoznak erre az e-mail címre:',
            'selected_events' => 'Értesítést kérek új eseményekről',
            'selected_posts' => 'Értesítést kérek új cikkekről',
            'selected_notifications' => 'Értesítést kérek módosításokról is',
            'locale' => 'Nyelv, amelyen az értesítések érkeznek',
            'btn' => [
                'verify_now' => 'E-mail cím megerősítése',
            ],
        ],
        'subscription_success' => 'Köszönjük! A megerősítő e-mailt elküldtük',
        'verify' => [
            'header' => 'Kérjük, erősítse meg e-mail címét',
            'btn' => 'Megerősítés',
        ],
        'unsubscribe' => '[HU] Abmelden',
        'verified_emails' => 'Verifizierte E-mail címn',
    ],
    'unsubscribe_link_label' => 'Beállítások módosítása / leiratkozás',
    'toast' => [
        'header' => [
            'success' => 'Siker',
        ],
    ],
];
