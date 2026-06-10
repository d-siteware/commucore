<?php

declare(strict_types=1);

return [
    'title' => 'Adatvédelmi nyilatkozat',
    'p_1' => 'Adatkezelő az adatvédelmi törvények értelmében:',
    'p_2' => 'Képviseli a vezetőség',

    'sections' => [
        [
            'header' => '1. Általános rendelkezések',
            'body' => 'Személyes adatainak védelme kiemelten fontos számunkra. Adatait kizárólag a GDPR (általános adatvédelmi rendelet) és a BDSG (szövetségi adatvédelmi törvény) jogszabályi rendelkezései alapján kezeljük. Ez az adatvédelmi nyilatkozat tájékoztatja Önt az adatkezelés legfontosabb szempontjairól egyesületi tevékenységünk és weboldalunk keretében.',
        ],
        [
            'header' => '2. Adatkezelés ezen a weboldalon',
            'body' => 'Ez a weboldal személyes adatokat csak olyan mértékben kezel, amennyire az a biztonságos és működőképes szolgáltatás nyújtásához szükséges. Nem használunk elemző- vagy követőeszközöket. A weboldal kizárólag technikailag szükséges session cookie-kat használ a bejelentkezési munkamenet fenntartásához (Laravel Session Management). Harmadik félnek történő adattovábbítás nem történik.',
        ],
        [
            'header' => '3. Tagok kezelése',
            'body' => 'Az egyesületi tagok által megadott adatok (név, cím, e-mail cím, telefonszám, születési dátum, bankszámlaszám, valamint a tagsággal kapcsolatos információk) a GDPR 6. cikk (1) bekezdés b) pontja alapján a tagsági szerződés teljesítése céljából kerülnek feldolgozásra. A tagság megszűnése után a személyes törzsadatok 3 éves megőrzési idő után pszeudonimizálásra kerülnek. A pénzügyi adatokat (tagdíjfizetések, könyvelési tételek) a német adótörvénykönyv (AO) 147. §-a és a kereskedelmi törvénykönyv (HGB) 257. §-a szerint 10 évig őrizzük. Minden adatmódosítás revízióbiztos naplóban (audit log) kerül rögzítésre.',
        ],
        [
            'header' => '4. Hírlevél és rendezvényinformációk',
            'body' => 'A nem tagok önkéntesen regisztrálhatnak egyesületi hírek és rendezvényinformációk fogadására. Az e-mail cím feldolgozása a GDPR 6. cikk (1) bekezdés a) pontja (hozzájárulás) alapján történik. A hozzájárulás időbélyegzővel kerül dokumentálásra. Hozzájárulását bármikor visszavonhatja az egyes e-mailekben található leiratkozási linkre kattintva. A leiratkozás után adatait 30 napos átmeneti időszak után teljes mértékben töröljük. Harmadik félnek történő adattovábbítás nem történik.',
        ],
        [
            'header' => '5. Rendezvényregisztráció',
            'body' => 'Rendezvényekre történő jelentkezéskor a nevet, e-mail címet, valamint opcionális adatokat (telefon, megjegyzések) a GDPR 6. cikk (1) bekezdés b) pontja alapján kezeljük. Ezeket az adatokat kizárólag az adott rendezvény lebonyolítására használjuk, és a rendezvény dátumát követő 30 napon belül automatikusan töröljük.',
        ],
        [
            'header' => '6. Tárhely és technikai üzemeltetés',
            'body' => 'A weboldal saját szerveren üzemel (self-hosted). Az e-mail kommunikáció a Strato AG (Németország) szerverein keresztül történik. Az adatvédelmi követelmények betartásra kerülnek.',
        ],
        [
            'header' => '7. Cookie-k',
            'body' => 'Ez a weboldal nem használ cookie-kat elemzési vagy követési célokra. Kizárólag egy technikailag szükséges session cookie kerül alkalmazásra, amely a böngésző bezárása után törlődik. Ehhez a TTDSG 25. § (2) bekezdése szerint nem szükséges hozzájárulás.',
        ],
        [
            'header' => '8. Adatbiztonság',
            'body' => 'A személyes adatokhoz való minden hozzáférés a háttérrendszerben audit naplóban kerül rögzítésre. A tagadatokhoz való hozzáférés hitelesített felhasználókra (vezetőség, pénztáros) korlátozódik. A dokumentumok titkosítva kerülnek tárolásra egy privát tárolóban, és csak hitelesített hozzáféréseken keresztül érhetők el.',
        ],
        [
            'header' => '9. Az Ön jogai',
            'body' => 'A GDPR értelmében Ön bármikor jogosult tájékoztatáshoz (15. cikk), helyesbítéshez (16. cikk), törléshez (17. cikk), az adatkezelés korlátozásához (18. cikk), valamint az adathordozhatósághoz (20. cikk). Egy megadott hozzájárulást bármikor visszavonhat a jövőre nézve. Ezenkívül panasztételi joggal élhet az illetékes adatvédelmi hatóságnál.',
        ],
        [
            'header' => '10. Kapcsolat',
            'body' => 'Adatvédelmi kérdések esetén kérjük, forduljon a következő címre:',
            'email' => true,
        ],
    ],
];
