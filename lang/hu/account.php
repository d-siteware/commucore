<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Számlák áttekintése',
        'title_no_state' => 'Számla kiválasztása',
        'btn' => [
            'fetch_data' => 'Számlaadatok lekérése',
            'create_report' => 'Jelentés készítése',
            'create_vcashcount' => 'Számlálólista készítése',
            'create_account' => 'Új számla létrehozása',
        ],
    ],
    'area' => [
        'ideal' => [
            'label' => 'Ideális terület',
            'description' => 'Egyesületi munka',
        ],
        'asset_management' => [
            'label' => 'Vagyonkezelés',
            'description' => 'Kamatok, bérbeadás',
        ],
        'purpose_operation' => [
            'label' => 'Cél szerinti működés',
            'description' => 'Egyesületi rendezvények',
        ],
        'economic_business' => [
            'label' => 'Gazdasági tevékenység',
            'description' => 'Értékesítés, vendéglátás',
        ],
    ],

    'category' => [
        'asset' => 'Eszközök',
        'liability' => 'Források',
        'income' => 'Bevétel',
        'expense' => 'Ráfordítás',
    ],
    'dashboard' => [
        'heading' => ':year. könyvelési év',
        'transactions' => [
            'title' => 'Könyvelések',
            'columns' => [
                'label' => 'Megnevezés',
                'amount' => 'Összeg',
            ],
            'btn' => [
                'overview' => 'Áttekintés',
                'create' => 'Könyvelés rögzítése',
                'create_short' => 'Rögzítés',
            ],
        ],
        'sections' => [
            'balance_sheet' => 'Számlaáttekintés',
            'cash_counts' => 'Pénztárszámlálások',
        ],
        'reports' => [
            'title' => 'Jelentések',
            'columns' => [
                'period' => 'Időszak',
                'status' => 'Állapot',
            ],
            'btn' => [
                'print' => 'nyomtatás',
            ],
        ],
    ],
    'cashcount' => [
        'heading' => 'Áttekintés',
        'dated' => 'dátum',
        'empty_state' => 'Nincs rögzített számlálás',
        'btn' => [
            'delete' => 'törlés',
            'edit' => 'szerkesztés',
        ],
        'delete' => [
            'heading' => 'Számlálólista törlése',
            'label' => 'Kérlek erősítsd meg a :label számlálólista törlését',
            'warning' => 'A törlés nem vonható vissza!',
            'btn' => [
                'cancel' => 'Mégse',
                'submit' => 'Törlés',
            ],
            'confirmationtoast' => [
                'head' => 'Siker',
                'txt' => 'Számlálólista sikeresen törölve!',
            ],
        ],
        'create' => [
            'heading' => 'Új számlálólista létrehozása',
            'btn' => [
                'submit' => 'Rögzítés',
            ],
        ],
        'edit' => [
            'heading' => 'Számlálólista szerkesztése',
            'btn' => [
                'submit' => 'Frissítés',
            ],
        ],
    ],
    'balance_sheet' => [
        'total' => 'Teljes számlaegyenleg',
        'dated' => 'Egyenleg',
    ],

    'toast' => [
        'created' => [
            'heading' => 'Siker',
            'text' => 'A számla létrehozva.',
        ],
        'updated' => [
            'heading' => 'Siker',
            'text' => 'A számla frissítve.',
        ],
        'payment_account_created' => [
            'heading' => 'Siker',
            'text' => 'A fizetési számla létrehozva',
        ],
        'booking_account_created' => [
            'heading' => 'Siker',
            'text' => 'A könyvelési számla létrehozva',
        ],
    ],

    'select_placeholder' => 'Számla kiválasztása ...',

    'tabs' => [
        'details' => 'Részletek',
        'transactions' => 'Könyvelések',
        'reports' => 'Jelentések',
        'cash_counts' => 'Számlálólisták',
    ],

    'columns' => [
        'label' => 'Megnevezés',
        'amount' => 'Összeg',
        'type' => 'Típus',
        'status' => 'Állapot',
    ],

];
