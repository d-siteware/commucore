<?php

declare(strict_types=1);

return [
    'step' => [
        '01' => 'Szervezet',
        '02' => 'Beállítások',
        '03' => 'Csapat meghívása',
        '04' => 'Kész',
    ],
    'org' => [
        'heading' => 'Szervezet',
        'subheading' => 'Alapvető információk a szervezetedről.',
        'org_name' => 'Szervezet neve',
        'email' => 'E-mail',
        'website' => 'Weboldal',
        'website_placeholder' => 'https://',
        'address' => 'Cím',
        'zip' => 'IRSZ',
        'city' => 'Város',
        'legal_heading' => 'Jogi adatok',
        'legal_subheading' => 'Ezek az adatok a bizonylatokhoz és jelentésekhez szükségesek.',
        'register_id' => 'Nyilvántartási szám',
        'register_id_placeholder' => 'VR 12345',
        'registered_date' => 'Bejegyezve',
        'court' => 'Bíróság',
        'tax_id' => 'Adószám',
        'vat_id' => 'ÁFA szám',
        'vat_id_placeholder' => 'DE123456789',
    ],
    'settings' => [
        'fy_heading' => 'Üzleti év',
        'fy_subheading' => 'A könyvelés kezdő éve.',
        'fy_label' => 'Kezdő év',
        'locales_heading' => 'Nyelvek',
        'locales_subheading' => 'Mely nyelvek legyenek aktívak a példányodban?',
        'locales_available' => 'elérhető nyelvek',
    ],
    'team' => [
        'profile_heading' => 'Profilod',
        'profile_subheading' => 'Egészítsd ki a saját adataidat.',
        'surname' => 'Vezetéknév',
        'firstname' => 'Keresztnév',
        'username' => 'Felhasználónév',
        'invite_heading' => 'Csapat meghívása',
        'invite_subheading' => 'Hívj meg további személyeket. Minden meghívott személy automatikusan tagként jön létre – nem minden tagnak van automatikusan bejelentkezése.',
        'invite_name_placeholder' => 'Vezetéknév',
        'invite_firstname_placeholder' => 'Keresztnév',
        'invite_email_placeholder' => 'email@pelda.hu',
        'add_more_btn' => 'További hozzáadása',
        'smtp_warning_heading' => 'Megjegyzés',
        'smtp_warning_text' => 'Ebben a példányban jelenleg az összes kimenő e-mail a naplóba kerül, és nem kerül elküldésre. Kérjük, vedd fel a kapcsolatot az ügyfélszolgálatunkkal, ha élni szeretnél az e-mail küldés lehetőségével. Köszönjük!',
    ],
    'finish' => [
        'heading' => 'Minden készen áll!',
        'subheading' => 'A szervezeted be van állítva. Most már indulhatsz.',
        'fiscal_year' => ':year. üzleti év',
        'selected_locales' => 'Kiválasztott nyelvek',
        'selected_locale' => 'Kiválasztott nyelv',
        'invites_sent' => ':count meghívó(k) elküldésre kerül(nek)',
        'btn_dashboard' => 'Irányítópultra',
    ],
    'btn' => [
        'next' => 'Tovább',
        'back' => 'Vissza',
    ],
    'badge' => [
        'red' => 'Beállítás szükséges',
        'amber' => 'Beállítás ajánlott',
    ],

    'checklist' => [
        'title' => 'Beállítási ellenőrzőlista',
        'dismissed' => 'Beállítási ellenőrzőlista elrejtve',
        'reopen' => 'Újra megjelenítés',
        'admin_badge' => 'Admin & Vezetőség',
        'all_done' => 'Minden kész!',
        'all_done_subtitle' => 'Az egyesületetek indulásra kész. Sok sikert a CommuCore-ral!',
        'go_to_module' => 'Modul megnyitása',
        'tutorial' => 'Bemutató',
        'hide' => 'Ellenőrzőlista elrejtése',
        'completed' => ':completed / :total kész',
    ],

    'validation' => [
        'active_locales' => [
            'required' => 'Legalább egy nyelvet ki kell választani.',
            'min' => 'Legalább egy nyelvet ki kell választani.',
        ],
    ],
];
