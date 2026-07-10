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
