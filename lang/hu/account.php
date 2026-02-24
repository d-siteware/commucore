<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Számlák áttekintése',
        'btn' => [
            'fetch_data' => 'Számlaadatok lekérése',
            'create_report' => 'Jelentés készítése',
            'create_vcashcount' => 'Számlálási lista létrehozása',
        ],
    ],
    'dashboard' => [
        'heading' => 'Pénztári év :year',
        'transactions' => [
            'title' => 'Könyvelések',
            'columns' => [
                'label' => 'Megnevezés',
                'amount' => 'Összeg',
            ],
            'btn' => [
                'overview' => 'Áttekintés',
                'create' => 'Könyvelés benyújtása',
                'create_short' => 'Benyújtás',
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
                'print' => 'Nyomtatás',
            ],
        ],
    ],
    'cashcount' => [
        'heading' => 'Áttekintés',
        'dated' => 'Dátum:',
        'empty_state' =>  'Nincs rögzített számlálás',
        'btn' => [
            'delete' => 'Törlés',
            'edit' => 'Szerkesztés',
        ],
        'delete' => [
            'heading' => 'Számlálási lista törlése',
            'label' => 'Kérjük, erősítse meg a(z) :label számlálási lista törlését',
            'warning' => 'A törlés nem vonható vissza!',
            'btn' => [
                'cancel' => 'Mégse',
                'submit' => 'Törlés',
            ],
            'confirmationtoast' => [
                'head' => 'Siker',
                'txt' => 'A számlálási lista sikeresen törölve lett!',
            ],
        ],
        'create' => [
            'heading' => 'Új számlálási lista létrehozása',
            'btn' => [
                'submit' => 'Rögzítés',
            ],
        ],
        'edit' => [
            'heading' => 'Számlálási lista szerkesztése',
            'btn' => [
                'submit' => 'Frissítés',
            ],
        ],

    ],
    'balance_sheet' => [
        'total' => 'Teljes számlaegyenleg',
        'dated' => 'Állapot:',
    ],

];
