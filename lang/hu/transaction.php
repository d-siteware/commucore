<?php

declare(strict_types=1);

return [
    'edit-text-modal' => [
        'heading' => 'Tranzakció szövegek módosítása',
        'label' => 'Címke',
        'reference' => 'Hivatkozás',
        'description' => 'Leírás',
        'btn' => [
            'label' => 'Mentés',
        ],
        'update-success' => [
            'text' => 'A szövegek sikeresen frissítve lettek',
            'heading' => 'Siker!',
        ],
    ],
    'detach-member-success' => [
        'text' => 'A tranzakció és a tag közötti kapcsolat sikeresen törölve lett',
        'heading' => 'Siker',
    ],
    'attach-member-success' => [
        'text' => 'A tranzakció és a tag közötti kapcsolat sikeresen létrehozva',
        'heading' => 'Siker',
    ],
    'attach-event-success' => [
        'heading' => 'Siker',
        'text' => 'A tranzakció és a rendezvény közötti kapcsolat sikeresen létrehozva',
    ],
    'detach-event-success' => [
        'text' => 'A tranzakció és a rendezvény közötti kapcsolat sikeresen törölve lett',
        'heading' => 'Siker',
    ],
    'access' => [
        'denied' => 'Nincs jogosultsága a tranzakciók kezeléséhez: ',
    ],
    'cancel-transaction-modal' => [
        'reason' => [
            'label' => 'Törlés indokának megadása',
            'error' => 'Meg kell adni a törlés indokát!',
        ],
        'heading' => 'Tranzakció törlése',
        'btn' => [
            'submit' => [
                'label' => 'Törlés',
            ],
        ],
    ],
    'index' => [
        'title' => 'Tranzakciók áttekintése',
        'menu-item' => [
            'book' => 'Könyvelés',
            'edit' => 'Szerkesztés',
            'cancel' => 'Sztornó',
            'edit_text' => 'Szövegek módosítása',
            'rebook' => 'Átkönyvelés',
            'attach_event' => 'Rendezvény',
            'attach_member' => 'Tag',
            'detach_event' => 'Rendezvény',
            'detach_member' => 'Tag',
            'send_invoice' => 'E-mail küldése',
            'print_invoice' => 'Nyomtatás',
        ],
        'menu-group' => [
            'booking' => 'Tranzakció',
            'receipt' => 'Nyugta',
        ],
        'menu-submenu' => [
            'assign' => 'Hozzárendelés',
            'detach' => 'Leválasztás',
        ],
        'table' => [
            'empty-results' => 'Nem találhatók tranzakciók',
            'columns' => [
                'booking' => 'Tranzakció',
                'date' => 'Dátum',
                'created' => 'Beküldve',
                'status' => 'Státusz',
                'account' => 'Számla',
                'amount' => 'Összeg [EUR]',
                'type' => 'Típus',
                'receipt' => 'Bizonylat',
                'linked' => 'Kapcsolva',
            ],
            'tooltip' => [
                'reference' => 'Hivatkozás',
                'description' => 'Leírás',
                'event_assigned' => 'Rendezvény hozzárendelve',
                'member_assigned' => 'Tag hozzárendelve',
                'receipt_sent' => 'Nyugta elküldve',
            ],
        ],
        'search' => [
            'placeholder' => 'Keresés ...',
        ],
        'filter' => [
            'date_range' => [
                'placeholder' => 'szűrés időszak szerint',
            ],
            'type' => [
                'placeholder' => 'szűrés típus szerint',
                'suffix' => 'Tranzakció típus',
            ],
            'status' => [
                'placeholder' => 'szűrés státusz szerint',
                'suffix' => 'Tranzakció státusz',
            ],
        ],
        'btn' => [
            'create' => 'Új tranzakció létrehozása',
        ],
        'confirm' => [
            'resend_invoice' => 'Az e-mail már el lett küldve. Újra elküldi?',
        ],
        'modal' => [
            'edit' => [
                'heading' => 'Tranzakció szerkesztése',
            ],
            'append_event' => [
                'heading' => 'Rendezvény hozzárendelése',
                'select_placeholder' => 'Rendezvény kiválasztása',
                'optional' => 'Opcionális',
                'btn' => [
                    'submit' => 'hozzárendelés',
                ],
            ],
            'append_member' => [
                'heading' => 'Tag hozzárendelése',
                'select_placeholder' => 'Tag kiválasztása',
                'membership_fees' => 'Tagdíjak',
                'is_membership_fee' => 'Tagdíj befizetés',
                'fee_year' => 'Könyvelés pénzügyi évre',
                'btn' => [
                    'submit' => 'Tag hozzárendelése',
                ],
            ],
        ],
    ],
    'create' => [
        'page' => [
            'title' => 'Tranzakció létrehozása',
        ],
        'title' => 'Új tranzakció',
    ],
    'account-transfer-modal' => [
        'heading' => 'Átkönyvelés (Pénzügyi számla módosítása)',
        'content' => 'Az átkönyvelés sztornózza a kiválasztott tranzakciót és létrehoz egy új tranzakciót az új pénzügyi számlára való hivatkozással',
        'reason' => 'Átkönyvelés indoka',
        'new_account' => 'Új pénzügyi számla',
        'account_placeholder' => 'Fizetési számla pl. pénztár, bankszámla stb.',
        'btn' => [
            'submit' => 'Átkönyvelés',
        ],
        'error' => [
            'transaction_id' => 'Nincs kiválasztva tranzakció',
            'account_id' => 'Nincs kiválasztva pénzügyi számla',
            'identical' => 'Nem választható ki az eredeti számla',
            'reason' => 'Indoklás megadása kötelező!',
        ],
    ],
    'account' => [
        'name' => 'Pénzügyi számla',
        'number' => 'Szám',
        'institute' => 'Intézmény',
        'type' => 'Típus',
        'iban' => 'IBAN',
        'bic' => 'BIC',
        'starting_amount' => 'Kezdő egyenleg',
    ],
    'mail' => [
        'receipt' => [
            'subject' => 'Nyugta a beérkezett hozzájárulásról',
            'title' => 'Nyugta a beérkezett hozzájárulásról',
            'greeting' => '',
            'header' => 'Áttekintés',
            'body' => 'Köszönjük a hozzájárulását! A mellékletben megtalálja a nyugtát az Ön nyilvántartásához. Kérdés esetén kérjük, válaszoljon erre az e-mailre.',
            'date' => 'Befizetés dátuma:',
            'amount' => 'Beérkezett összeg',
            'label' => 'Közlemény/Tárgy',
            'reference' => 'Hivatkozás',
        ],
    ],
    'event' => [
        'boxoffice' => [
            'heading' => 'Pénztár',
            'paymentsection' => 'Tranzakció adatok',
            'visitorsection' => 'Látogató adatok',
            'visitorname' => 'Név',
            'visitoremail' => 'E-mail',
            'submit' => 'Pénztári befizetés rögzítése',
        ],
    ],
];