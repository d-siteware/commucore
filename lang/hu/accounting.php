<?php

declare(strict_types=1);

return [
    'export' => [
        'type' => [
            'buchungsstapel' => 'DATEV könyvelési köteg',
            'buchungsstapel_desc' => 'Könyvelési adatok DATEV formátumban (EXTF) az adótanácsadónál történő importáláshoz.',
            'stammdaten' => 'DATEV számlamegnevezések',
            'stammdaten_desc' => 'Főkönyvi számlák (SKR42) DATEV-kompatibilis törzsadat-CSV formátumban.',
        ],
    ],
    'datev' => [
        'settings' => [
            'tab' => 'DATEV',
            'heading' => 'DATEV beállítások',
            'subheading' => 'Hozzáférési adatok a DATEV exporthoz (könyvelési köteg). A tanácsadói és ügyfélszámot az adótanácsadótól kapod.',
            'not_configured_heading' => 'A DATEV még nincs konfigurálva',
            'not_configured_text' => 'Amíg a tanácsadói és ügyfélszám hiányzik, a DATEV export helyőrzőket tartalmaz, és nem importálható az adótanácsadónál.',
            'berater_nr' => 'Tanácsadói szám (Beraternummer)',
            'berater_nr_description' => '4–7 számjegy (1001–9999999), az adótanácsadótól',
            'mandant_nr' => 'Ügyfélszám (Mandantennummer)',
            'mandant_nr_description' => '1–5 számjegy (1–99999), az adótanácsadótól',
            'skr' => 'Számlakeret (SKR)',
            'skr_description' => 'Jelenleg kizárólag az SKR42 támogatott – a DATEV által egyesületeknek ajánlott számlakeret.',
            'skr_42' => 'Egyesületek, alapítványok, közhasznú GmbH',
            'skr_49' => 'Egyesületek (régi, 4 számjegyű)',
            'konto_laenge' => 'Főkönyvi számlahossz',
            'konto_laenge_description' => 'Automatikusan a számlakeretből származik (SKR42 = 5).',
            'application_info' => 'Exportálta',
            'application_info_description' => 'Megjelenik a DATEV fejlécben (max. 25 karakter)',
            'info' => [
                'heading' => 'Megjegyzések a DATEV exporthoz',
                'numbers_text' => 'A tanácsadói és ügyfélszám azonosítja az ügyfelet a DATEV irodai szoftverben. Mindkét számnak pontosan meg kell egyeznie az adótanácsadó értékeivel, különben az importálás sikertelen.',
                'validation_text' => 'Az exportált fájlok átadás előtt technikailag ellenőrizhetők a hivatalos DATEV formátum-ellenőrző programmal (developer.datev.de → DATEV-Format → Tools). A végleges ellenőrzés a DATEV Rechnungswesen tesztimportjával történik.',
            ],
            'btn' => [
                'save' => 'DATEV beállítások mentése',
            ],
            'toast' => [
                'save_success_heading' => 'Mentve',
                'save_success_text' => 'A DATEV beállítások mentésre kerültek.',
            ],
        ],
    ],
];
