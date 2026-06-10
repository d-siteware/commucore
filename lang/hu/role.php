<?php

declare(strict_types=1);

return [

    'page' => [
        'title' => 'Szerepkörök a(z) :name egyesületben',
        'heading' => 'Elérhető szerepkörök',
    ],

    'leadership' => [
        'btn_add' => 'Új vezetői pozíció hozzáadása',
        'empty_member_list' => 'Nincs tag találat',
        'empty_roles_list' => 'Nincs szerepkör találat',
    ],

    'create' => [
        'form' => [
            'header' => 'Vezetői funkció hozzárendelése',
            'select_member.label' => 'Tag kiválasztása',
            'select_role.label' => 'Szerepkör hozzárendelése',
            'title' => 'Szerepkör hozzárendelése',
            'btn_add_new_role' => [
                'label' => 'Új',
            ],
            'option_add_new_role' => 'Új szerepkör létrehozása',
            'option_select_role' => 'Szerepkör kiválasztása',
            'profile_image' => 'Profilkép',
            'designated_at' => 'Kinevezés dátuma',
            'designated_at.placeholder' => 'Dátum',
            'about_me' => 'Rólam',
            'btn_add_member' => 'Szerepkör hozzárendelése a taghoz',
            'btn_update_member' => 'Szerepkör frissítése',
        ],
        'modal' => [
            'title' => 'Új szerepkör létrehozása',
            'name' => 'Név',
            'description' => 'Leírás',
            'can_manage_accounting' => 'Kezelheti a számlákat',
            'can_audit_accounting' => 'Ellenőrizheti a könyvelést',
            'can_represent_organization' => 'Képviseleti joggal rendelkezik',
            'button' => 'Mentés',
        ],
    ],

    'validation' => [
        'error_required' => [
            'role_id' => 'Kérjük, válasszon szerepkört',
            'member_id' => 'Kérjük, válasszon tagot',
            'designated_at' => 'A kinevezés dátuma kötelező',
        ],
    ],

    'toast' => [
        'msg' => [
            'leaderrole' => [
                'updated' => 'Az adatok sikeresen frissítve',
                'revoked' => 'A szerepkör sikeresen elvételre került',
                'assigened' => 'A szerepkör hozzárendelésre került a taghoz',

            ],
        ],
    ],

];
