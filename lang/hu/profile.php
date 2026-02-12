<?php

declare(strict_types=1);



return [
    'page' => [
        'title' => 'Saját fiók',
    ],
    'section' => [
        'profile' => [
            'title' => 'Fiókinformációk',
            'description' => 'Frissítse fiókprofiladatait és e-mail címét.',
        ],
        'password' => [
            'title' => 'Új jelszó',
            'description' => 'A biztonság megőrzése érdekében győződjön meg róla, hogy fiókja hosszú, véletlenszerű jelszót használ. ',
        ],
        '2FA' => [
            'header' => 'Kéttényezős hitelesítés',
            'text' => 'Növelje fiókja biztonságát a kéttényezős hitelesítés használatával.',
        ],
    ],
    'email' => [
        'verification' => [
            'status' => [
                'unverified' => 'Az Ön e-mail címe nincs ellenőrizve.',
            ],
            'resend' => [
                'label' => 'Kattintson ide az ellenőrző e-mail újraküldéséhez.',
                'status' => 'Új ellenőrző linket küldtünk az Ön e-mail címére.',
            ],
        ],
    ],
    'update' => [
        'info' => [
            'status' => [
                'saved' => 'frissítve',
            ],
            'btn' => [
                'label' => [
                    'save' => 'Adatok frissítése',
                ],
            ],
        ],
    ],
    'password' => [
        'current' => 'Jelenlegi jelszó',
        'new' => 'Új jelszó',
        'confirm' => 'Új jelszó megerősítése',
        'status' => [
            'msg' => 'A jelszó sikeresen megváltozott',
        ],
        'btn' => [
            'label' => 'Új jelszó beállítása',
        ],
        'msg' => [
            'error' => [
                'mismatch' => 'A jelszavak nem egyeznek!',
            ],
        ],
    ],
    '2FA' => [
        'status' => '[HU] Fügen Sie Ihrem Konto zusätzliche Sicherheit hinzu, indem Sie die Zwei-Faktor-Authentifizierung verwenden.',
        'initiate' => [
            'title' => 'Teljes kéttényezős hitelesítés.',
            'instruction' => 'A kéttényezős hitelesítés befejezéséhez olvassa be a következő QR-kódot telefonja hitelesítő alkalmazásával, vagy írja be a beállítási kulcsot, és adja meg a generált OTP-kódot.',
            'prompt' => 'Kéttényezős hitelesítés teljes.',
            'finalize' => 'A kéttényezős hitelesítés befejezéséhez olvassa be a következő QR-kódot a telefon hitelesítő alkalmazásával, vagy adja meg a beállítási kulcsot, és írja be a generált OTP-kódot.',
            'scan_barcode' => 'A kéttényezős hitelesítés most engedélyezve van. Olvassa be a következő QR-kódot telefonja hitelesítő alkalmazásával, vagy írja be a beállítási kulcsot.',
            'store_codes' => 'Tárolja ezeket a helyreállítási kódokat egy biztonságos jelszókezelőben. Lehetővé teszi fiókjához való hozzáférés visszaállítását, ha a kéttényezős hitelesítési eszköz elveszik. ',
        ],
        'description' => 'Ha a kéttényezős hitelesítés engedélyezve van, akkor a hitelesítés során egy biztonságos, véletlenszerű tokent kell megadni. Ezt a tokent telefonja Google Authenticator alkalmazásából kérheti le.',
        'btn' => [
            'enable' => 'Aktiválás',
            'disable' => 'Letiltás',
            'cancel' => 'Mégse',
            'confirm' => 'Megerősítés',
            'show_codes' => 'Helyreállítási kódok megjelenítése',
            'regen_codes' => 'Helyreállítási kódok újragenerálása',
            'reset_codes' => 'Helyreállítási kódok visszaállítása',
        ],
        'modal-confirm' => [
            'title' => 'Hitelesítés szükséges',
            'content' => 'Biztonsági okokból arra kérjük, erősítse meg jelszavával a folyamatot.',
            'btn' => [
                'label' => 'Megerősítés',
                'cancel' => [
                    'label' => 'Mégse',
                ],
            ],
        ],
    ],
    'sessions' => [
        'title' => 'Böngésző munkamenetek',
        'description' => 'Az aktív munkamenetek kezelése és kijelentkezése más böngészőkben és más eszközökön.',
        'content' => 'Ha szükséges, minden eszközén kijelentkezhet az összes többi böngészési munkamenetből. Az alábbiakban felsorolunk néhány legutóbbi munkamenetét. Ez a lista azonban nem biztos, hogy teljes. Ha úgy gondolja, hogy fiókját feltörték, frissítse jelszavát is.',
    ],
    'session' => [
        'device' => [
            'unkown' => 'Ismeretlen',
            'found' => 'Ez az eszköz',
            'lastlog' => 'Utolsó aktív',
            'logout_other' => '[HU] Von anderen Browsersitzungen abmelden',
        ],
        'logout' => [
            'modal' => [
                'title' => 'Kijelentkezés a többi böngésző munkamenetből',
                'content' => 'Kérjük, adja meg jelszavát annak megerősítéséhez, hogy ki szeretne jelentkezni a többi böngészőmunkamenetből az összes eszközéről.',
                'inp' => [
                    'password' => [
                        'label' => 'Jelszó',
                    ],
                ],
                'btn' => [
                    'cancel' => 'Mégse',
                    'confirm' => 'Kijelentkezés a többi böngésző munkamenetből',
                ],
            ],
        ],
    ],
    'delete' => [
        'title' => 'Fiók törlése',
        'description' => 'Fiókja végleges törlése.',
        'warning' => [
            'text' => 'A fiók törlését követően minden erőforrás és adat véglegesen törlődik. Fiókja törlése előtt töltse le a megtartani kívánt adatokat vagy információkat.',
        ],
        'btn' => [
            'delete' => 'Törlés',
        ],
        'modal' => [
            'title' => 'Fiók törlése',
            'text' => 'Biztosan törölni szeretné fiókját? A fiók törlése után minden erőforrás és adat véglegesen törlődik. Kérjük, adja meg jelszavát annak megerősítéséhez, hogy véglegesen törölni kívánja fiókját.',
            'inp' => [
                'password' => [
                    'placeholder' => 'Jelszó',
                ],
            ],
            'btn' => [
                'cancel' => 'Mégse',
                'delete' => 'Fiók végleges törlése',
            ],
        ],
    ],
    'label' => [
        'name' => 'Név',
        'first_name' => 'Keresztnév',
        'phone' => '[HU] Telefon fest',
        'mobile' => 'Telefon Hbiltelefone',
        'photo' => '[HU] Profilbild',
        'gender' => '[HU] Geschlecht',
        'locale' => 'Nyelv',
        'email' => 'E-Mail Cím',
    ],
];
