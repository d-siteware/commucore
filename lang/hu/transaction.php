<?php

declare(strict_types=1);

return [
    'documents' => [
        'heading' => 'Dokumentumok',
        'category' => [
            'label' => 'Kategória',
            'invoice' => 'Számla',
            'receipt' => 'Nyugta',
            'bank_statement' => 'Banki kivonat',
            'contract' => 'Szerződés',
            'other' => 'Egyéb',
        ],
        'btn' => [
            'upload' => 'Dokumentum feltöltése',
        ],
        'modal_title' => 'Dokumentumok csatolása a tranzakcióhoz',
        'drag_hint' => 'Húzza ide a fájlokat vagy kattintson a kiválasztáshoz',
    ],
    'edit-text-modal' => [
        'heading' => 'Tranzakció szövegek módosítása',
        'label' => 'Címke',
        'reference' => 'Referencia',
        'description' => 'Leírás',
        'btn' => [
            'label' => 'Mentés',
        ],
        'update-success' => [
            'text' => 'A szövegek sikeresen frissítve',
            'heading' => 'Siker!',
        ],
    ],
    'detach-member-success' => [
        'text' => 'A tranzakció és a tag közötti kapcsolat sikeresen megszüntetve',
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
        'text' => 'A tranzakció és a rendezvény közötti kapcsolat sikeresen megszüntetve',
        'heading' => 'Siker',
    ],
    'access' => [
        'denied' => 'Nincs jogosultsága tranzakciók kezelésére: ',
    ],
    'cancel-transaction-modal' => [
        'reason' => [
            'label' => 'Adja meg a visszavonás okát',
            'error' => 'Meg kell adni a visszavonás indoklását!',
        ],
        'heading' => 'Tranzakció visszavonása',
        'btn' => [
            'submit' => [
                'label' => 'Visszavonás',
            ],
        ],
    ],
    'delete' => [
        'success' => [
            'heading' => 'Siker',
            'msg' => 'A tranzakció sikeresen törölve',
        ],

    ],
    'delete-transaction-confirmation-modal' => [
        'heading' => 'Tranzakcióhoz bizonylat tartozik',
        'has_documents' => 'A tranzakcióhoz csatolt bizonylat tartozik, amely szintén törlésre kerül. Ez a művelet nem vonható vissza!|A tranzakcióhoz még :count bizonylat van csatolva. Ezek szintén törlésre kerülnek. A művelet nem vonható vissza!',
        'btn' => 'Végleges törlés',
    ],
    'index' => [
        'title' => 'Tranzakciók áttekintése',
        'menu-item' => [
            'book' => 'Könyvelés',
            'edit' => 'Szerkesztés',
            'delete' => 'Törlés',
            'cancel' => 'Visszavonás',
            'edit_text' => 'Szövegek módosítása',
            'rebook' => 'Átkönyvelés',
            'attach_document' => 'Bizonylat csatolása',
            'attach_event' => 'Rendezvény',
            'attach_member' => 'Tag',
            'detach_event' => 'Rendezvény',
            'detach_member' => 'Tag',
            'send_invoice' => 'E-mail küldése',
            'print_invoice' => 'Nyomtatás',
            'attach_project' => 'Projekt hozzárendelése',
            'detach_project' => 'Projekt eltávolítása',
            'attach_funding' => 'Támogatás hozzárendelése',
            'detach_funding' => 'Támogatás eltávolítása',
        ],
        'menu-group' => [
            'booking' => 'Könyvelés',
            'receipt' => 'Nyugta',
        ],
        'menu-submenu' => [
            'assign' => 'Hozzárendelés',
            'detach' => 'Leválasztás',
        ],
        'table' => [
            'empty-results' => 'Nincs tranzakció találat',
            'columns' => [
                'booking' => 'Könyvelés',
                'date' => 'Teljesítve',
                'created' => 'Benyújtva',
                'status' => 'Státusz',
                'account' => 'Számla',
                'amount' => 'Összeg [EUR]',
                'type' => 'Típus',
                'receipt' => 'Bizonylat',
                'linked' => 'Kapcsolódó',
            ],
            'tooltip' => [
                'reference' => 'Referencia',
                'description' => 'Leírás',
                'event_assigned' => 'Rendezvényhez rendelve',
                'member_assigned' => 'Taghoz rendelve',
                'receipt_sent' => 'Nyugta elküldve',
                'project_assigned' => 'Projekt',
                'funding_assigned' => 'Támogatás',
            ],
        ],
        'search' => [
            'placeholder' => 'Keresés ...',
        ],
        'filter' => [
            'date_range' => [
                'placeholder' => 'szűrés időszakra',
            ],
            'type' => [
                'placeholder' => 'szűrés típusra',
                'suffix' => 'Tranzakció típus',
            ],
            'status' => [
                'placeholder' => 'szűrés státuszra',
                'suffix' => 'Tranzakció státusz',
            ],
        ],
        'btn' => [
            'create' => 'Új tranzakció létrehozása',
        ],
        'confirm' => [
            'resend_invoice' => 'Az e-mail már elküldésre került. Újraküldés?',
            'detach_project' => 'Valóban megszünteti a projekt hozzárendelést?',
            'detach_funding' => 'Valóban megszünteti a támogatás hozzárendelést?',
        ],
        'modal' => [
            'max' => 'Max',
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
                'fee_year' => 'Rögzítés a könyvelési évhez',
                'btn' => [
                    'submit' => 'Tag hozzárendelése',
                ],
            ],
            'append_project' => [
                'heading' => 'Projekt hozzárendelése',
                'select_placeholder' => 'Projekt kiválasztása...',
                'allocated_amount' => 'Részarányos összeg',
                'allocated_amount_hint' => 'Opcionális: Csak a tranzakció részarányos összegét rendelje a projekthez.',
                'btn' => ['submit' => 'Hozzárendelés'],
            ],

            'append_funding' => [
                'heading' => 'Támogatás hozzárendelése',
                'select_placeholder' => 'Támogatás kiválasztása...',
                'allocated_amount' => 'Részarányos összeg',
                'allocated_amount_hint' => 'Opcionális: Csak a tranzakció részarányos összegét rendelje a támogatáshoz.',
                'booking_amount' => 'Tranzakció összege',
                'funding_remaining' => 'Még elérhető a támogatásban',
                'max_allocatable' => 'Max. hozzárendelhető',
                'btn' => ['submit' => 'Hozzárendelés'],
                'error' => [
                    'exceeds_amount' => 'A részarányos összeg nem haladhatja meg a tranzakció összegét (:amount).',
                ],
            ],
        ],
    ],
    'create' => [
        'page' => [
            'title' => 'Tranzakció létrehozása',
            'heading' => 'Új könyvelés rögzítése',
        ],
        'title' => 'Új tranzakció',
    ],
    'account-transfer-modal' => [
        'heading' => 'Átkönyvelés (pénzügyi számla módosítása)',
        'content' => 'Az átkönyvelés visszavonja a kiválasztott tranzakciót és létrehoz egy új tranzakciót az új pénzügyi számlára hivatkozással',
        'reason' => 'Átkönyvelés oka',
        'new_account' => 'Új pénzügyi számla',
        'account_placeholder' => 'Fizetési számla, pl. készpénz, bankszámla stb.',
        'btn' => [
            'submit' => 'Átkönyvelés',
        ],
        'error' => [
            'transaction_id' => 'Nincs tranzakció kiválasztva',
            'account_id' => 'Nincs pénzügyi számla kiválasztva',
            'identical' => 'Nem az eredeti számlát kell kiválasztani',
            'reason' => 'Az indoklás megadása kötelező!',
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
            'subject' => 'Nyugta a beérkezett tagdíjról',
            'title' => 'Nyugta a beérkezett tagdíjról',
            'greeting' => '',
            'header' => 'Áttekintés',
            'body' => 'Köszönjük a hozzájárulását! A csatolmányban megtalálja a nyugtát a dokumentumaihoz. Kérdés esetén válaszoljon erre az e-mailre.',
            'date' => 'Befizetés érkezett:',
            'amount' => 'Beérkezett összeg',
            'label' => 'Közlemény/Tárgy',
            'reference' => 'Referencia',
        ],
        'send' => [
            'success' => 'A számla sikeresen elküldve ide: :email.',
            'success_heading' => 'Siker',
            'error' => 'Hiba a számla küldésekor: :message',
            'error_heading' => 'Hiba',
            'no_email' => 'A számla nem küldhető el, mert a tagnak nincs e-mail címe. Kérlek add meg, vagy nyomtasd ki és küldd postán.',
            'no_email_heading' => 'Hiba',
        ],
    ],
    'event' => [
        'boxoffice' => [
            'heading' => 'Helyszíni pénztár',
            'paymentsection' => 'Tranzakció adatok',
            'visitorsection' => 'Látogató adatok',
            'visitorname' => 'Név',
            'visitoremail' => 'E-mail',
            'submit' => 'Helyszíni pénztár rögzítése',
            'select_cash_desk' => 'Pénztár választása',
            'select_account' => 'Számla választása',
        ],
    ],
    'status' => [
        'submitted' => 'benyújtva',
        'booked' => 'könyvelve',
    ],
    'locked' => [
        'tooltip' => 'Ez a tranzakció zárolva van (lezárt üzleti év része)',
        'cannot_modify' => 'Ez a tranzakció nem szerkeszthető, mert egy lezárt üzleti év része.',
    ],
    'type' => [
        'deposit' => 'Befizetés',
        'withdrawal' => 'Kifizetés',
        'transfer' => 'Átkönyvelés',
        'reversal' => 'Visszavonás',
    ],
    'attach-project-success' => [
        'heading' => 'Projekt hozzárendelve',
        'text' => 'A tranzakció sikeresen hozzárendelve a projekthez.',
        'error' => [
            'exceeds_amount' => 'A részarányos összeg nem haladhatja meg a tranzakció összegét (:amount).',
        ],
    ],
    'detach-project-success' => [
        'heading' => 'Projekt eltávolítva',
        'text' => 'A projekt hozzárendelés megszüntetve.',
    ],
    'attach-funding-success' => [
        'heading' => 'Támogatás hozzárendelve',
        'text' => 'A tranzakció sikeresen hozzárendelve a támogatáshoz.',
        'error' => [
            'exceeds_amount' => 'A részarányos összeg nem haladhatja meg a tranzakció összegét (:amount).',
        ],
    ],
    'detach-funding-success' => [
        'heading' => 'Támogatás eltávolítva',
        'text' => 'A támogatás hozzárendelés megszüntetve.',
    ],

    'form' => [
        'type' => 'Könyvelés',
        'status' => 'Státusz',
        'separator' => [
            'accounts' => 'Számlák',
            'amounts' => 'Összegek',
            'texts' => 'Szövegek',
        ],
        'account' => [
            'placeholder' => 'Fizetési számla, pl. készpénz, bankszámla stb.',
            'new' => 'Új fizetési számla',
        ],
        'booking_account' => [
            'placeholder' => 'SKR42 számla',
            'new' => 'Új könyvelési számla',
        ],
        'area' => [
            'placeholder' => 'Adójogi szféra (KOST1)',
        ],
        'amount_gross' => 'Bruttó',
        'vat_percent' => 'ÁFA [%]',
        'vat_amount' => 'ÁFA [EUR]',
        'amount_net' => 'Nettó',
        'label' => 'Megnevezés',
        'reference' => 'Referencia',
        'date' => 'Dátum',
        'description' => 'Leírás',
        'btn' => [
            'new' => 'Új könyvelés kezdése',
            'save_event' => 'Esemény könyvelés mentése',
            'save_member' => 'Tag könyvelés mentése',
            'save' => 'Könyvelés mentése',
        ],
        'validation' => [
            'label_required' => 'Kérlek add meg a könyvelés megnevezését.',
            'account_id_required' => 'Kérlek válassz fizetési számlát',
            'type_required' => 'A könyvelés típusát meg kell adni',
            'status_required' => 'A könyvelés státuszát meg kell adni',
        ],
    ],

    'modal' => [
        'account' => [
            'heading' => 'Fizetési számla létrehozása',
            'type_placeholder' => 'Számlatípus',
            'name' => 'Név',
            'number' => 'Szám',
            'starting_amount' => 'Kezdő egyenleg',
            'institute' => 'Intézmény',
            'iban' => 'IBAN',
            'bic' => 'BIC',
            'btn' => [
                'save_and_continue' => 'Mentés és további létrehozás',
                'save_and_select' => 'Létrehozás és átvétel',
            ],
        ],
        'booking' => [
            'heading' => 'Könyvelési számla létrehozása',
            'category_label' => 'Számlatípus',
            'category_placeholder' => 'Kategória választása',
            'area_label' => 'Adójogi szféra',
            'area_placeholder' => 'Terület választása',
            'subtype_label' => 'Altípus',
            'subtype_placeholder' => 'Nincs altípus',
            'label' => 'Megnevezés',
            'skr49' => 'SKR-49 szám',
            'btn' => [
                'save_and_continue' => 'Mentés és további létrehozás',
                'save_and_select' => 'Létrehozás és átvétel',
            ],
        ],
        'missing' => [
            'heading' => 'Nincs könyvelés',
            'text' => 'Még nem került könyvelés rögzítésre, amelyhez bizonylat rendelhető',
        ],
    ],

    'booking' => [
        'heading' => 'Könyvelés hozzárendelése',
        'label' => 'SKR számla hozzárendelése',
        'new_booking_account' => 'Új SKR 49 könyvelési számla',
        'submit' => 'Könyvelés befejezése',
    ],

    'booking-update-success' => [
        'text' => 'A könyvelés frissítve',
        'heading' => 'Siker',
    ],

    'cancel-success' => [
        'text' => ':label könyvelés visszavonva',
        'heading' => 'Siker',
    ],

    'change-success' => [
        'text' => ':label könyvelés módosítva',
        'heading' => 'Siker',
    ],

    'event-create-success' => [
        'text' => 'A rendezvény könyvelése rögzítve',
        'heading' => 'Siker',
    ],

    'member-create-success' => [
        'text' => 'A tagdíj könyvelése rögzítve',
        'heading' => 'Siker',
    ],

    'create-success' => [
        'text' => ':label könyvelés rögzítve',
        'heading' => 'Siker',
    ],

    'update-success' => [
        'text' => ':label könyvelés frissítve',
        'heading' => 'Siker',
    ],

    'attach-success' => [
        'text' => 'A könyvelés sikeresen hozzárendelve',
    ],

    'area-reset-warning' => [
        'text' => 'A könyvelési számla visszaállítva – nem tartozik a kiválasztott szférához.',
    ],

    'create-error' => [
        'text' => 'A tranzakció nem menthető: :message',
        'heading' => 'Hiba',
    ],

    'validation' => [
        'valid_amount' => 'Kérlek adj meg egy érvényes összeget.',

        'event' => [
            'account_id' => [
                'required' => 'Kérlek add meg a fizetési számlát',
                'doesnt_start_with' => 'Kérlek add meg vagy hozz létre egy fizetési számlát',
            ],
        ],

        'member' => [
            'account_id' => [
                'required' => 'Kérlek válassz ki vagy hozz létre egy fizetési számlát',
            ],
            'label' => [
                'required' => 'Kérlek adj meg egy megnevezést',
            ],
            'amount_gross' => [
                'required' => 'Kérlek adj meg egy összeget',
            ],
        ],

        'append_event' => [
            'target_event' => [
                'required' => 'Kérlek válassz egy rendezvényt',
            ],
            'transaction_id' => [
                'unique' => 'A könyvelés már hozzá van rendelve ehhez a rendezvényhez',
            ],
        ],

        'append_member' => [
            'target_member' => [
                'required' => 'Kérlek válassz egy tagot',
            ],
            'transaction_id' => [
                'unique' => 'A könyvelés már hozzá van rendelve egy taghoz',
            ],
            'fee_year' => [
                'integer' => 'A könyvelések nem lehetnek régebbiek 2010-nél',
            ],
        ],

        'append_project' => [
            'target_project' => [
                'required' => 'Kérlek válassz egy projektet.',
            ],
            'transaction_id' => [
                'unique' => 'Ez a könyvelés már hozzá van rendelve egy projekthez.',
            ],
        ],

        'append_funding' => [
            'target_funding' => [
                'required' => 'Kérlek válassz egy támogatást.',
            ],
            'transaction_id' => [
                'unique' => 'Ez a könyvelés már hozzá van rendelve egy támogatáshoz.',
            ],
        ],

        'boxoffice' => [
            'amount_gross' => [
                'required' => 'Add meg a belépő árat (0 az ingyenes belépéshez)',
            ],
            'account_id' => [
                'required' => 'Kérlek válassz egy pénzügyi számlát',
            ],
        ],
    ],

    'member_transaction' => [
        'assign_event_label' => 'Rendezvény hozzárendelése (opcionális)',
    ],
];
