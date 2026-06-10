<?php

declare(strict_types=1);

return [

    'page' => [
        'title' => 'Projektek',
    ],

    'index' => [
        'search_placeholder' => 'Projekt keresése...',
        'btn' => [
            'create' => 'Új projekt',
        ],
        'table' => [
            'title' => 'Cím',
            'status' => 'Státusz',
            'start_date' => 'Kezdés',
            'end_date' => 'Befejezés',
            'fundings' => 'Támogatások',
            'transactions' => 'Tranzakciók',
        ],
    ],

    'create' => [
        'page' => [
            'title' => 'Új projekt',
        ],
        'btn' => [
            'submit' => 'Projekt létrehozása',
        ],
        'success' => [
            'title' => 'Projekt létrehozva',
            'content' => 'A projekt sikeresen elmentve.',
        ],
    ],

    'show' => [
        'title' => 'Projekt:',
        'page' => [
            'title' => 'Projekt',
        ],
        'toast' => [
            'updated' => 'Projekt elmentve.',
        ],
    ],

    'form' => [
        'title' => 'Cím',
        'description' => 'Leírás / Megjegyzések',
        'status' => 'Státusz',
        'start_date' => 'Kezdő dátum',
        'end_date' => 'Befejező dátum',
        'btn' => [
            'save' => 'Mentés',
            'delete' => 'Törlés',
        ],
        'confirm' => [
            'delete' => 'Valóban törli a projektet? Ez nem vonható vissza.',
        ],
    ],

    'tabs' => [
        'details' => 'Részletek',
        'financials' => 'Pénzügyek',
        'fundings' => 'Támogatások',
        'posts' => 'Blog',
        'documents' => 'Dokumentumok',
    ],

    'financials' => [
        'income' => 'Bevételek',
        'expense' => 'Kiadások',
        'balance' => 'Egyenleg',
        'empty' => 'Még nincsenek tranzakciók rögzítve.',
        'table' => [
            'date' => 'Dátum',
            'label' => 'Megnevezés',
            'type' => 'Típus',
            'allocated' => 'Részarány',
            'amount' => 'Összeg',
            'full_amount' => 'Teljes összeg',
        ],
    ],

    'fundings' => [
        'stat' => [
            'allocated' => 'Odaitélt támogatás',
            'expense' => 'Projekt kiadások',
            'coverage' => 'Fedezeti arány',
        ],
        'table' => [
            'title' => 'Támogatás',
            'funder' => 'Támogató',
            'status' => 'Státusz',
            'allocated' => 'Odaitélve',
        ],
        'empty' => 'Még nincsenek támogatások hozzárendelve.',
    ],

    'posts' => [
        'btn' => ['create' => 'Új bejegyzés'],
        'table' => [
            'title' => 'Cím',
            'author' => 'Szerző',
            'status' => 'Státusz',
            'published_at' => 'Publikálva',
        ],
        'empty' => 'Még nincsenek bejegyzések.',
    ],

    'link_funding' => [
        'btn' => ['open' => 'Támogatás hozzárendelése'],
        'heading' => [
            'new' => 'Támogatás hozzárendelése',
            'edit' => 'Odaitélés szerkesztése',
        ],
        'form' => [
            'funding' => 'Támogatás',
            'funding_placeholder' => 'Támogatás kiválasztása...',
            'allocated_amount' => 'Odaitélt összeg (a határozat szerint)',
            'allocated_amount_hint' => 'A támogatási határozat szerinti összeg erre a projektre.',
            'editing_hint' => 'Az odaitélés összegének módosítása.',
            'btn' => [
                'attach' => 'Hozzárendelés',
                'update' => 'Frissítés',
            ],
        ],
        'menu' => [
            'edit' => 'Összeg szerkesztése',
            'detach' => 'Kapcsolat megszüntetése',
            'detach_confirm' => 'Valóban megszünteti a kapcsolatot? Az odaitélt összeg elvész.',
        ],
        'success' => [
            'attached' => 'Támogatás sikeresen hozzárendelve.',
            'updated' => 'Odaitélés frissítve.',
            'detached' => 'Kapcsolat megszüntetve.',
        ],
        'error' => [
            'already_linked' => 'Ez a támogatás már hozzá van rendelve.',
            'invalid_amount' => 'Kérjük, adjon meg egy érvényes, 0-nál nagyobb összeget.',
            'exceeds_remaining' => 'Az összeg meghaladja a rendelkezésre álló maradékot (:remaining).',
        ],
    ],

    'status' => [
        'planned' => 'Tervezett',
        'active' => 'Aktív',
        'completed' => 'Befejezett',
        'cancelled' => 'Megszakított',
    ],

    'documents' => [
        'category' => [
            'planning' => 'Tervezés / Koncepció',
            'contract' => 'Szerződés',
            'report' => 'Szakmai beszámoló / Zárójelentés',
            'invoice' => 'Számla / Költségkimutatás',
            'correspondence' => 'Levelezés / E-mailek',
            'photo' => 'Fotók / Dokumentáció',
            'other' => 'Egyéb',
        ],
    ],

];
