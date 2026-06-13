<?php

declare(strict_types=1);

return [
    'event.title' => 'Rendezvény beszámoló',
    'event.subject' => 'A(z) :name rendezvény értékelése',
    'event.visitor.name' => 'Látogató',

    'account' => [
        'title' => 'Pénztári jelentés',
        'timespan' => 'Időszak',
        'heading' => 'Fej adatok',
        'start' => 'Kezdet',
        'end' => 'Vég',
        'starting_amount' => 'Kezdő összeg',
        'end_amount' => 'Záró összeg',
        'total_income' => 'Összes bevétel',
        'total_expenditure' => 'Összes kiadás',
        'notes' => 'Megjegyzések',
        'new' => [
            'header' => 'Új jelentés létrehozása',
        ],
        'edit' => [
            'heading' => 'Szerkesztés',
        ],
        'btn' => [
            'get_transactions' => 'Tranzakciók lekérése az időszakra',
            'store_data' => 'Adatok mentése',
        ],
    ],

    'table.header.date' => 'Létrehozva',
    'table.header.name' => 'Pénzügyi számla',
    'table.header.status' => 'Státusz',
    'table.header.range' => 'Időszak',
    'table.header.audited' => 'Ellenőrizve',

    'initiate-report-audit-modal.title' => 'Jelentés ellenőrzés indítása',
    'initiate-report-audit-modal.content' => 'Kérjük, válassza ki azokat a tagokat, akik az ellenőrzést végzik.',
    'initiate-report-audit-modal.btn.submit' => 'Meghívók küldése',
    'initiate-report-audit-modal.select_member_id' => 'Tag',

    'index' => [
        'title' => 'Havi jelentések',
        'actions' => [
            'datev_export' => 'DATEV CSV',
            'print' => 'Nyomtatás',
            'audit' => 'Ellenőrzés',
            'edit' => 'Szerkesztés',
            'delete' => 'Törlés',
        ],
        'export_warning' => [
            'title' => 'Jelentés már exportálva',
            'body' => 'Ezt a jelentést már elküldték DATEV exportként az adótanácsadónak. Az újbóli ellenőrzés érvénytelenítheti a meglévő exportot.',
            'steuerberater_hint' => 'Ha folytatja, kérjük, tájékoztassa adótanácsadóját a javított exportról.',
            'confirm' => 'Folytatás ennek ellenére',
        ],

    ],

    'status' => [
        'eingereicht' => 'ellenőrzés alatt',
        'entwurf' => 'benyújtva',
        'geprueft' => 'ellenőrizve',
        'draft' => 'vázlat',
        'submitted' => 'benyújtva',
        'audited' => 'ellenőrizve',
        'rejected' => 'elutasítva',
    ],
    'get_transactions_short' => 'Tranzakciók lekérése',
    'transactions_found' => ':count tranzakció található',
    'no_transactions_in_period' => 'Nem található tranzakció ebben az időszakban!',
    'no_email_for_auditor' => 'Nincs e-mail cím :email számára',
    'no_auditors_selected' => 'Kérlek válassz ki ellenőröket a vizsgálathoz!',
    'delete_error' => 'A jelentés törlése nem sikerült: :message',
    'delete_success' => 'A jelentés sikeresen törölve',
    'data_updated' => 'Jelentés adatok frissítve',
    'default_filename' => 'Jelentés',
    'audits_found_heading' => 'Ellenőrzések találhatók',
    'audits_delete_warning' => 'A törlendő jelentéshez kapcsolódó ellenőrzések vannak. Ezek elvesznek a jelentés törlésével.',
    'delete_all' => 'Összes törlése',
    'select_member_placeholder' => 'Tag kiválasztása',
    'add_auditor' => 'Hozzáad',
    'nobody' => 'Senki',
    'create_report_btn' => 'Jelentés létrehozása',
    'auditor' => 'Ellenőr',
    'board_member_not_allowed_as_auditor' => 'Elnökségi tagok nem választhatók ki ellenőrként',
];
