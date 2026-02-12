<?php

declare(strict_types=1);



return [
    'title' => 'Tagjaink áttekintése',
    'header' => 'Itt találja az összes tagunk rendezhető áttekintését. Az almenüben szerkesztheti a tagokat, rögzítheti a befizetéseket, vagy inaktívként megjelölheti őket. Ez utóbbi helyettesíti a végleges törlést.',
    'table' => [
        'header' => [
            'name' => 'Név',
            'phone' => 'Mobiltelefon',
            'status' => 'Státusz',
            'fee_status' => 'Tagdíj státusz',
            'birthday' => 'Születésnap',
        ],
    ],
    'con' => [
        'men' => [
            'edit' => 'Szerkesztés',
            'payment' => 'Befizetés rögzítése',
            'delete' => 'Inaktívként megjelölés',
        ],
    ],
    'widget' => [
        'birthday' => [
            'card' => [
                'table' => [
                    'header' => [
                        'member' => 'Tag',
                        'birthday' => 'Születésnap',
                        'newage' => 'Életkor',
                    ],
                ],
                'heading' => 'Közelgő születésnapok: :name',
            ],
        ],
    ],
    'fee-type' => [
        'label' => 'Tagdíj típusa',
        'free' => 'Tagdíjmentesség',
        'standard' => 'Normál tagdíj',
        'discounted' => 'Kedvezményes tagdíj',
    ],
    'apply' => [
        'discount' => [
            'label' => 'Kedvezményes tagdíj igénylése',
            'reason' => [
                'label' => 'A kedvezmény indoka',
            ],
        ],
        'fee' => [
            'text' => 'Tájékoztatást kaptam a havi :sum € tagdíjról, és kötelezem magam annak megfizetésére.',
            'label' => 'A fizető tagok havi :sum € tagdíjat fizetnek. A 75 év feletti tagok mentesülnek a tagdíjfizetés alól.',
            'payment' => [
                'banktt' => 'A tagdíjat a megadott bankszámlaszámra kérjük utalni.',
                'paypals' => 'A tagdíj PayPal-on is küldhető. Kérjük, a „Barátoknak küldött pénz” opciót válassza, különben a PayPal 1,8%-os díjat von le.',
                'paypal' => 'A tagdíj a :iban PayPal-címre küldhető. Kérjük, a „Barátoknak küldött pénz” opciót válassza, különben a PayPal díjat von le.',
            ],
        ],
        'full_fee' => [
            'label' => 'A fizető tagok havi :sum € tagdíjat fizetnek.',
        ],
        'discounted_fee' => [
            'label' => 'A tagok kedvezményes havi :sum € tagdíjat igényelhetnek.',
        ],
        'free_fee' => [
            'label' => ':age év feletti tagjaink mentesülnek a tagdíjfizetési kötelezettség alól.',
        ],
        'email' => [
            'none' => 'Nincs e-mail címem!',
            'without' => [
                'text' => 'Ha nem rendelkezik e-mail-címmel, nyomtassa ki ezt az űrlapot, írja alá, és küldje el postai úton az alábbi címre:',
            ],
            'benefits' => 'Az e-mail-címmel rendelkező tagok automatikusan értesítést kapnak a rendezvényekről, és hozzáférnek a hirdetőtáblához.',
            'note' => [
                'header' => 'Fontos!',
                'content' => 'Az online beküldéshez kötelező e-mail-címet megadni. Ha nincs e-mail-címe, válassza a postai útvonalat.',
            ],
        ],
        'checkAndSubmit' => 'Adatok ellenőrzése és elküldése',
        'printAndSubmit' => 'Űrlap nyomtatása',
        'title' => 'Tagsági kérelem – Ungarische Kolonie Berlin e.V.',
        'text' => 'Örülünk, hogy érdeklődik a tagság iránt az Ungarische Kolonie Berlin e.V.-nél!',
        'process' => 'A felvétel az alábbi lépésekben történik:',
        'step1' => [
            'label' => '1. lépés',
            'text' => 'Töltse ki az alábbi űrlapot.',
        ],
        'via' => [
            'web' => 'Online elküldés',
            'postal' => 'Postai úton',
        ],
        'step2' => [
            'label' => '2. lépés',
            'text' => 'Ellenőrizze az adatokat',
        ],
        'click' => [
            'button' => 'Kattintson a gombra',
            'checkbox' => 'Jelölje be a négyzetet',
        ],
        'step3a' => [
            'label' => '3a. lépés (online)',
            'text' => 'Kattintson az „Adatok ellenőrzése és elküldése” gombra.',
        ],
        'step3b' => [
            'label' => '3b. lépés (postai)',
            'text' => 'Kattintson a „Űrlap nyomtatása” gombra.',
        ],
        'step4a' => [
            'label' => '4a. lépés (online)',
            'text' => 'A rendszer egy egyszeri megerősítő linket küld e-mail-ben.',
        ],
        'step4b' => [
            'label' => '4b. lépés (postai)',
            'text' => 'Nyomtassa ki és írja alá az űrlapot, majd küldje el a rajta szereplő címre.',
        ],
        'step5a' => [
            'label' => '5a. lépés (online)',
            'text' => 'A linkre kattintva igazolja, hogy Ön küldte a kérelmet.',
        ],
        'step5b' => [
            'label' => '5b. lépés (postai)',
            'text' => 'A aláírt űrlapot postázza el.',
        ],
        'step6' => [
            'label' => '6. lépés',
            'text' => 'Kérelem feldolgozása alatt áll. Szükség esetén felvesszük Önnel a kapcsolatot.',
        ],
        'step7' => [
            'label' => '7. lépés',
            'text' => 'A vezetőség dönt a felvételről, és értesítjük Önt e-mailben vagy postai úton.',
        ],
        'submission' => [
            'success' => [
                'head' => 'Sikeres beküldés!',
                'msg' => 'Kérelmét megkaptuk, hamarosan feldolgozzuk. Köszönjük szépen!',
            ],
            'failed' => [
                'head' => 'Hiba történt',
                'msg' => 'Sajnos technikai hiba lépett fel. Kérjük, próbálja újra később.',
            ],
        ],
        'print' => [
            'title' => 'Tagsági kérelem – Ungarische Kolonie Berlin e.V.',
            'greeting' => 'Tisztelt Elnökség!',
            'text' => 'Ezennel kérem felvételemet az Ungarische Kolonie Berlin e.V. tagjai közé.',
            'regards' => 'Tisztelettel',
            'overview' => [
                'person' => 'Személyes adataim',
                'contact' => 'Elérhetőségeim',
            ],
            'filename' => 'Tagsagi_kerelem_Ungarische_Kolonie_Berlin_:id.pdf',
        ],
    ],
    'birth_date' => 'Születési dátum',
    'birth_place' => 'Születési hely',
    'name' => 'Vezetéknév',
    'first_name' => 'Keresztnév',
    'email' => 'E-mail-cím',
    'phone' => 'Telefon',
    'mobile' => 'Mobiltelefon',
    'address' => 'Lakcím',
    'zip' => 'Irányítószám',
    'city' => 'Város',
    'country' => 'Ország',
    'locale' => 'Előnyben részesített nyelv',
    'gender' => 'Nem',
    'type' => 'Tag típusa',
    'linked_user' => 'Felhasználói fiókhoz csatolva',
    'unlink_user' => 'Kapcsolat megszüntetése',
    'left_at' => 'Kilépés dátuma',
    'section' => [
        'admins' => 'Csak a vezetőség töltheti ki',
        'person' => 'Személyes adatok',
        'address' => 'Lakcím',
        'phone' => 'Elérhetőségek',
        'fees' => 'Tagdíj',
        'payments' => 'Befizetések',
        'deduction' => 'Kedvezmény',
        'email' => 'E-mail-cím',
    ],
    'update' => [
        'success' => [
            'title' => 'Sikeres mentés',
            'content' => 'A tag adatai sikeresen frissítve lettek.',
        ],
    ],
    'date' => [
        'applied_at' => 'Tagság kérelmezve',
        'verified_at' => 'E-mail megerősítve',
        'entered_at' => 'Tagság elfogadva',
        'left_at' => 'Kilépés dátuma',
    ],
    'btn' => [
        'sendVerificationMail' => [
            'label' => 'Emlékeztető e-mail küldése',
        ],
        'addMember' => 'Új tag felvétele',
        'sendAcceptanceMail' => [
            'label' => 'Kérelem elfogadása és e-mail küldése',
        ],
        'sendAcceptance' => [
            'label' => 'Kérelem elfogadása',
        ],
        'setEnteredAt' => [
            'label' => 'Elfogadozás dátuma',
        ],
        'inviteAsUser' => [
            'label' => 'Tag meghívása felhasználóként',
        ],
        'cancelMembership' => [
            'label' => 'Tagság megszüntetése',
        ],
    ],
    'accordion' => [
        'optionals' => [
            'label' => 'Opcionális adatok',
        ],
    ],
    'optional-data' => [
        'text' => 'Itt további információkat adhat meg.',
    ],
    'familystatus' => [
        'label' => 'Családi állapot',
        'single' => 'Hajadon / Nőtlen',
        'married' => 'Házas',
        'divorced' => 'Elvált',
        'n_a' => 'Nem kívánom megadni',
    ],
    'show' => [
        'title' => 'Tag adatai: :name',
        'created_at' => 'Létrehozva',
        'updated_at' => 'Utoljára módosítva',
        'about' => 'Személyes adatok',
        'membership' => 'Tagság',
        'payments' => 'Befizetések',
        'store' => 'Mentés',
        'fee_msg' => [
            'exempted' => 'Tagdíjmentes',
            'paid' => 'Tagdíj rendezve',
        ],
        'invitation_sent' => 'Meghívó elküldve',
        'member' => [
            'reactivate' => 'Tag újraaktiválása',
        ],
        'select_user' => 'Felhasználó kiválasztása',
        'empty_user_list' => 'Nincs találat',
        'attached' => [
            'success' => [
                'head' => 'Siker!',
                'msg' => ':name felhasználó sikeresen csatolva lett.',
            ],
            'failed' => [
                'head' => 'Hiba!',
                'msg' => 'A felhasználó nem csatolható.',
            ],
            'placeholder' => 'Tagot választani',
        ],
        'detached' => [
            'success' => [
                'head' => 'Siker!',
                'msg' => ':name felhasználó csatolása megszüntetve.',
            ],
        ],
        'heading' => 'Szetglied Daten zeigen',
    ],
    'register' => [
        'title' => 'Jelszó beállítása a regisztrációhoz',
        'page_title' => 'Regisztráció befejezése',
        'password_requirements' => 'A jelszónak meg kell felelnie az alábbi követelményeknek:',
        'password' => 'Jelszó',
        'password_confirm' => 'Jelszó megerősítése',
        'submit' => 'Regisztráció befejezése',
        'checkLength' => 'Legalább 8 karakter',
        'checkCapital' => 'Legalább egy nagybetű',
        'checkNumbers' => 'Legalább egy szám',
        'checkSpecial' => 'Legalább egy speciális karakter (!"$§%(){}[])',
    ],
    'widgets' => [
        'applicants' => [
            'title' => 'Új tagsági kérelmek',
            'empty_search' => 'Nincs találat',
            'empty_list' => 'Nincs függőben lévő kérelem',
            'confirm' => [
                'deletion' => [
                    'title' => 'Sikeres törlés',
                    'text' => 'A kijelölt kérelmek törölve lettek.',
                ],
            ],
            'options' => [
                'label' => 'Műveletek',
                'deletion' => [
                    'confirm' => 'Kérjük, erősítse meg a kijelölt kérelmek törlését!',
                    'btn' => [
                        'label' => 'Törlés',
                    ],
                ],
                'edit' => [
                    'btn' => [
                        'label' => 'Szerkesztés',
                    ],
                ],
            ],
            'tab' => [
                'header' => [
                    'from' => 'Dátum',
                    'name' => 'Név',
                ],
            ],
            'search' => [
                'label' => 'Keresés',
            ],
        ],
    ],
    'cancel' => [
        'modal' => [
            'title' => 'Tagság megszüntetése',
            'text' => 'Kérjük, erősítse meg a tagság megszüntetését.',
        ],
        'confirm_text_input' => [
            'label' => 'Erősítse meg a vezetéknév megadásával',
        ],
        'btn' => [
            'final' => [
                'label' => 'Tagság végleges megszüntetése',
            ],
        ],
    ],
    'appliance_received' => [
        'mail' => [
            'subject' => 'Tagsági kérelme megérkezett!',
            'greeting' => 'Kedves :name!',
            'text' => 'Megkaptuk tagsági kérelmét, köszönjük érdeklődését közösségünk iránt. Hamarosan feldolgozzuk és visszajelzünk Önnek.',
        ],
    ],
    'create' => [
        'message' => [
            'success' => 'Az új tag sikeresen létrehozva.',
            'fail' => 'A tag létrehozása nem sikerült. Kérjük, jelezze az adminisztrátornak.',
        ],
        'title' => 'Szetglied anlegen',
    ],
    'index' => [
        'search-placeholder' => 'Keresés',
    ],
    'backend' => [
        'create' => [
            'heading' => 'Új tag felvétele',
            'btn' => [
                'submit' => 'Tag rögzítése',
            ],
        ],
    ],
    'application' => [
        'errors' => [
            'name-reqipred' => 'Kérem den Vezetéknévn angeben',
        ],
    ],
    'fees' => [
        // Page header
        'overview_title' => 'Tagdíjak áttekintése',
        'year' => 'Év',

        // Filter & Search
        'search_member_placeholder' => 'Tag keresése...',
        'show_inactive' => 'Inaktívak megjelenítése',
        'pdf_export' => 'PDF exportálás',
        'csv_export' => 'CSV exportálás',

        // Summary cards
        'members' => 'Tagok',
        'paid' => 'Könyvelve',
        'open' => 'Beküldve',
        'transactions' => 'Tranzakciók',
        'payments' => 'Befizetések',

        // Table columns
        'member' => 'Tag',
        'type' => 'Típus',
        'date' => 'Dátum',
        'status' => 'Státusz',
        'receipt' => 'Nyugta',

        // Status badges
        'status_booked' => 'könyvelve',
        'status_submitted' => 'beküldve',

        // Actions
        'send' => 'Küldés',
    ],
];
