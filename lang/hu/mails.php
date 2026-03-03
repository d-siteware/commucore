<?php

declare(strict_types=1);

return [
    'president' => [
        'deputy' => 'Alelnök',
    ],
    'treasury' => 'Pénztáros',
    'secretariat' => [
        'hu' => 'Magyar titkárság',
        'de' => 'Német titkárság',
    ],
    'cultural' => [
        'director' => 'Kulturális vezető',
    ],
    'social' => [
        'affairs' => [
            'deputy' => 'Szociális ügyekért felelős helyettes vezető',
        ],
    ],
    'contact' => 'Kapcsolat',
    'invitation' => [
        'subject' => 'Meghívó a(z) :name portáljára',
        'greeting' => 'Szia :name',
        'header' => 'Kérjük, erősítsd meg az e-mail-címedet',
        'text' => 'Mint a(z) :name aktív tagját, szeretettel meghívunk, hogy regisztrálj felhasználóként a portálunkon.',
        'btn' => [
            'label' => 'Kattints ide a regisztráció befejezéséhez',
        ],
    ],
    'acceptance' => [
        'subject' => 'A(z) :name tagsági kérelme elfogadva',
        'greeting' => 'Szia :name',
        'header' => 'Szeretettel üdvözlünk',
        'text' => 'Örömmel értesítünk, hogy a(z) :name egyesületbe benyújtott tagsági kérelmedet elbíráltuk és elfogadtuk.',
    ],
    'audit_invitation' => [
        'header' => 'Szükségünk van rád!',
        'text' => 'Meghívunk, hogy ellenőrizd a :range időszak havi pénztárjelentését. Az ellenőrzést elindíthatod az alábbi linkre kattintva, vagy a portálon a Pénztár → Jelentések menüpontban, majd a megfelelő jelentésnél a „Most ellenőrzöm” gombra kattintva. Köszönjük a segítségedet!',
    ],
    'audit' => [
        'invitation' => [
            'subject' => 'A havi pénztárjelentés ellenőrzése',
            'link_label' => 'Itt kezdheted meg az ellenőrzést',
        ],
    ],
    'members' => [
        'heading' => 'E-mail küldése minden tagnak, aki megadott e-mail-címmel rendelkezik',
        'content' => 'Az e-mail azon a nyelven készül el, amelyet a felhasználó a profiljában megadott.',
        'btn' => [
            'preview' => 'Előnézet (mellékletek nélkül)',
            'test_mail' => 'Teszt e-mail küldése magamnak (mellékletek nélkül)',
            'submit' => 'Küldés',
            'cancel' => 'Mégsem',
            'final' => 'Igen, küldés',
        ],
        'subject' => [
            'de' => 'Tárgy',
            'hu' => '[DE] Tárgy',
        ],
        'message' => [
            'de' => 'Üzenet',
            'hu' => 'Üzenet',
        ],
        'confirm' => [
            'header' => 'Kérjük, küldés előtt alaposan ellenőrizze',
            'warning' => 'Sok tag fogja megkapni az üzenetet. Hiba esetén kellemetlen következmények adódhatnak.',
            'info' => 'A küldés előtt bejegyzés készül az előzményekbe arról, hogy ki, mikor és milyen e-mailt küldött.',
        ],
    ],
    'member' => [
        'separator' => [
            'text' => 'Szövegek',
            'links' => 'Hivatkozások',
            'attachments' => 'Mellékletek (csak pdf|jpg|jpeg|png|tif)',
            'options' => 'Beállítások',
        ],
    ],
    'mailing_list' => [
        'label' => [
            'email' => 'E-mail-cím',
        ],
        'text' => [
            'privacy' => 'Hozzájárulok, hogy adataimat tárolják és a hatályos adatvédelmi jogszabályoknak megfelelően kezeljék.',
            'privacy_full' => 'Az Ön adatait kizárólag rendezvényekről és cikkekről szóló értesítések küldésére használjuk fel, és nem adjuk át harmadik félnek.',
        ],
        'btn_subscribe' => [
            'label' => 'Feliratkozás a listára',
        ],
        'header' => 'Kapjon értesítést a(z) :name új rendezvényeiről és cikkeiről',
        'options_group_header' => 'Témák kiválasztása',
        'options_header' => 'Beállítások',
        'options' => [
            'all_label' => 'Mindent!',
            'events_label' => 'Csak rendezvényekről kérek értesítést',
            'posts_label' => 'Csak cikkekről kérek értesítést',
            'all_description' => 'Értesítést kap, amint új rendezvény vagy cikk jelenik meg, illetve ha változás történik.',
            'events_description' => 'Jelölje be ezt a mezőt, ha kizárólag új rendezvényekről szeretne értesítést kapni.',
            'posts_description' => 'Jelölje be ezt a mezőt, ha kizárólag új cikkekről szeretne értesítést kapni.',
            'update_notifications_label' => 'Frissítések',
            'update_notifications_description' => 'Kérek értesítést rendezvények vagy cikkek módosításáról is',
        ],
        'validation_error' => [
            'email' => 'Kérjük, adjon meg egy e-mail-címet',
            'terms_accepted' => 'Kérjük, fogadja el az adatvédelmi nyilatkozatot',
        ],
        'show' => [
            'confirmation_msg' => 'Sikeresen megerősítette e-mail-címét',
            'update_msg' => 'Sikeresen módosította beállításait',
            'change' => 'Módosítsa választását, ha a jövőben ezekről a témákról szeretne értesítést kapni.',
            'btn' => [
                'save' => 'Választás mentése',
            ],
        ],
        'confirmation_email_subject' => 'Kérjük, erősítse meg e-mail-címét',
        'confirmation_email_msg' => 'Köszönjük, hogy feliratkozott levelezőlistánkra! Kérjük, erősítse meg feliratkozását az alábbi gombra kattintva, hogy az érdeklődésének megfelelő értesítéseket kapjon.',
        'confirmation_email_msg_changes' => 'Beállításait bármikor módosíthatja egy, a jövőbeni e-mailekben található linken keresztül.',
        'confirmation_email_msg_ignore' => 'Ha nem Ön iratkozott fel, kérjük, hagyja figyelmen kívül ezt az e-mailt.',
        'confirmation_email' => [
            'selected_summary' => 'Az alábbi beállítások érvényesek az Ön e-mail-címére:',
            'selected_events' => 'Értesítést kérek új rendezvényekről',
            'selected_posts' => 'Értesítést kérek új cikkekről',
            'selected_notifications' => 'További értesítést kérek módosításokról is',
            'locale' => 'Az értesítések nyelve',
            'btn' => [
                'verify_now' => 'E-mail-cím megerősítése',
            ],
        ],
        'subscription_success' => 'Köszönjük! A megerősítéshez szükséges e-mail elküldésre került',
        'verify' => [
            'header' => 'Kérjük, erősítse meg e-mail-címét',
            'btn' => 'Megerősítés',
        ],
        'unsubscribe' => [
            'label' => 'Leiratkozás',
            'error_heading' => 'Váratlan hiba történt',
            'error_msg' => 'Sajnos e-mail-címét váratlan hiba miatt nem tudtuk törölni. Elnézését kérjük a kellemetlenségért. A rendszer jelezte számunkra a hibát, és már dolgozunk a megoldáson. Amint a törlés sikeresen megtörtént, értesítjük Önt. Addig is kérjük türelmét az esetlegesen érkező értesítések miatt.',
            'success_msg' => 'E-mail-címét sikeresen eltávolítottuk a listáról. A jövőben nem küldünk további értesítéseket.',
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
