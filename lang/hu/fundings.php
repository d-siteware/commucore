<?php

declare(strict_types=1);

return [

    // -------------------------------------------------------------------------
    // Allgemein (Index Page nutzt 'funding.page.title')
    // -------------------------------------------------------------------------
    'page' => [
        'title' => 'Támogatások',
    ],

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------
    'index' => [
        'search_placeholder' => 'Támogatás vagy támogató keresése...',
        'ongoing' => 'folyamatban',
        'btn' => [
            'create' => 'Új támogatás',
        ],
        'table' => [
            'title' => 'Cím',
            'funder' => 'Támogató',
            'status' => 'Státusz',
            'approved_amount' => 'Jóváhagyva',
            'period' => 'Futamidő',
            'projects' => 'Projektek',
        ],
    ],

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------
    'create' => [
        'page' => [
            'title' => 'Új támogatás',
        ],
        'btn' => [
            'submit' => 'Támogatás létrehozása',
        ],
        'success' => [
            'title' => 'Támogatás létrehozva',
            'content' => 'A támogatás sikeresen el lett mentve.',
        ],
    ],

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------
    'show' => [
        'title' => 'Támogatás:',
        'page' => [
            'title' => 'Támogatás',
        ],
        'toast' => [
            'updated' => 'Támogatás elmentve.',
        ],
    ],

    'reports' => [
        'actions' => [
            'executive' => 'Executive summary készítése',
            'detailed' => 'Részletes jelentés készítése',
            'statusbericht' => 'Állapotjelentés készítése',
        ],
        'toast' => [
            'created' => 'A támogatási jelentés elkészült és a dokumentumok közé került.',
        ],
    ],

    // -------------------------------------------------------------------------
    // Form (genutzt in Show + Create)
    // -------------------------------------------------------------------------
    'form' => [
        'title' => 'Cím',
        'funder' => 'Támogató',
        'reference' => 'Ügyszám / Referencia',
        'reference_hint' => 'A támogató belső ügyszáma',
        'status' => 'Státusz',
        'description' => 'Leírás / Jegyzetek',
        'approved_amount' => 'Jóváhagyott összeg',
        'period_start' => 'Támogatás kezdete',
        'period_end' => 'Támogatás vége',
        'btn' => [
            'save' => 'Mentés',
            'delete' => 'Törlés',
        ],
        'confirm' => [
            'delete' => 'Valóban törlöd a támogatást? Ez nem vonható vissza.',
        ],
    ],

    // -------------------------------------------------------------------------
    // Tabs
    // -------------------------------------------------------------------------
    'tabs' => [
        'details' => 'Részletek',
        'receipts' => 'Bevételek',
        'projects' => 'Projektek',
        'positions' => 'Pozíciók',
        'documents' => 'Dokumentumok',
    ],

    // -------------------------------------------------------------------------
    // Tab: Zahlungseingänge
    // -------------------------------------------------------------------------
    'receipts' => [
        'stat' => [
            'approved' => 'Jóváhagyva',
            'received' => 'Beérkezett',
            'remaining' => 'Függőben',
        ],
        'table' => [
            'date' => 'Dátum',
            'label' => 'Megnevezés',
            'allocated' => 'Arány',
            'amount' => 'Összeg',
            'full_amount' => 'Teljes összeg',
        ],
        'empty' => 'Még nincsenek bevételek rögzítve.',
    ],

    // -------------------------------------------------------------------------
    // Tab: Projekte (Verwendungsnachweis)
    // -------------------------------------------------------------------------
    'projects' => [
        'stat' => [
            'approved' => 'Jóváhagyva',
            'allocated' => 'Projektekre elosztva',
            'unallocated' => 'Nincs elosztva',
        ],
        'table' => [
            'title' => 'Projekt',
            'status' => 'Státusz',
            'period' => 'Időszak',
            'allocated' => 'Hozzárendelve',
        ],
        'empty' => 'Még nincsenek projektek kapcsolva.',
    ],

    // -------------------------------------------------------------------------
    // Status (genutzt von FundingStatus::label())
    // -------------------------------------------------------------------------
    'status' => [
        'applied' => 'Igényelve',
        'approved' => 'Jóváhagyva',
        'active' => 'Aktív',
        'completed' => 'Befejezve',
        'rejected' => 'Elutasítva',
    ],

    // -------------------------------------------------------------------------
    // Verknüpfung von Projekten
    // -------------------------------------------------------------------------
    'link_project' => [
        'btn' => ['open' => 'Projekt kapcsolása'],
        'heading' => [
            'new' => 'Projekt kapcsolása',
            'edit' => 'Hozzárendelés szerkesztése',
        ],
        'form' => [
            'project' => 'Projekt',
            'project_placeholder' => 'Projekt kiválasztása...',
            'allocated_amount' => 'Hozzárendelt (határozat szerint)',
            'allocated_amount_hint' => 'A támogatási határozat szerinti összeg ehhez a projekthez.',
            'editing_hint' => 'A hozzárendelés összegének módosítása.',
            'remaining_hint' => 'Még elérhető ebből a támogatásból',
            'btn' => [
                'attach' => 'Kapcsolás',
                'update' => 'Frissítés',
            ],
        ],
        'menu' => [
            'edit' => 'Összeg szerkesztése',
            'detach' => 'Kapcsolat bontása',
            'detach_confirm' => 'Valóban bontod a kapcsolatot? A hozzárendelt összeg elvész.',
        ],
        'success' => [
            'attached' => 'Projekt sikeresen kapcsolva.',
            'updated' => 'Hozzárendelés frissítve.',
            'detached' => 'Kapcsolat bontva.',
        ],
        'error' => [
            'already_linked' => 'Ez a projekt már kapcsolva van.',
            'invalid_amount' => 'Kérlek adj meg egy 0-nál nagyobb érvényes összeget.',
            'exceeds_remaining' => 'Az összeg meghaladja a rendelkezésre álló maradékot (:remaining).',
        ],
    ],
    // -------------------------------------------------------------------------
    // Tab: Pozíciók (terv/tény támogatási pozíciónként)
    // -------------------------------------------------------------------------
    'positions' => [
        'btn' => [
            'create' => 'Pozíció létrehozása',
        ],
        'table' => [
            'title' => 'Pozíció',
            'category' => 'Kategória',
            'budget' => 'Terv',
            'actual' => 'Tény',
            'remaining' => 'Maradvány',
            'due_date' => 'Esedékes',
            'responsible' => 'Felelős',
        ],
        'empty' => 'Még nincsenek pozíciók. Hozz létre pozíciókat, hogy a tervezett költségvetést a tényleges kiadásokkal összehasonlíthasd.',
        'menu' => [
            'edit' => 'Szerkesztés',
            'delete' => 'Törlés',
            'delete_confirm' => 'Valóban törlöd a pozíciót? A tranzakció-hozzárendelések elvesznek.',
        ],
        'modal' => [
            'heading_create' => 'Pozíció létrehozása',
            'heading_edit' => 'Pozíció szerkesztése',
        ],
        'form' => [
            'title' => 'Cím',
            'budget' => 'Tervezett költségvetés (bruttó)',
            'budget_hint' => 'A támogatási okirat szerinti tervezett költségvetés ehhez a pozícióhoz.',
            'category' => 'Kategória',
            'category_placeholder' => 'Nincs kategória',
            'responsible' => 'Felelős személy',
            'responsible_placeholder' => 'Senki sincs hozzárendelve',
            'due_date' => 'Esedékesség',
            'description' => 'Leírás / Jegyzetek',
            'btn' => [
                'save' => 'Mentés',
            ],
        ],
        'toast' => [
            'saved' => 'Pozíció mentve.',
            'deleted' => 'Pozíció törölve.',
        ],
        'warning' => [
            'budget_exceeded' => [
                'heading' => 'A pozíciók költségvetése meghaladja a jóváhagyott összeget',
                'text' => 'A pozíciók költségvetésének összege (:sum) magasabb, mint a jóváhagyott összeg (:approved). Kérlek, ellenőrizd a támogatási okiratot.',
            ],
        ],
        'categories' => [
            'heading' => 'Kategóriák kezelése',
            'system_badge' => 'Rendszer',
            'new_label' => 'Egyéni kategória',
            'new_placeholder' => 'Kategória neve...',
            'btn' => [
                'add' => 'Hozzáadás',
            ],
            'delete_confirm' => 'Valóban törlöd ezt az egyéni kategóriát? A pozíciók megőrzik adataikat, de elvesztik a kategóriát.',
            'toast' => [
                'created' => 'Kategória létrehozva.',
                'deleted' => 'Kategória törölve.',
            ],
            'error' => [
                'duplicate' => 'Ilyen nevű kategória már létezik.',
                'system_readonly' => 'A rendszerkategóriák nem módosíthatók és nem törölhetők.',
            ],
        ],
    ],

    'documents' => [
        'category' => [
            'approval_notice' => 'Támogatási határozat',
            'usage_proof' => 'Felhasználási igazolás',
            'correspondence' => 'Levelezés / E-mailek',
            'contract' => 'Szerződés / Megállapodás',
            'report' => 'Szakmai beszámoló',
            'other' => 'Egyéb',
        ],
    ],

];
