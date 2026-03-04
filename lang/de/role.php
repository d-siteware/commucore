<?php

declare(strict_types=1);

return [

    'page' => [
        'title' => 'Rollen in der :name',
        'heading' => 'Verfügbare Rollen',
    ],

    'leadership' => [
        'btn_add' => 'Neue Führungsposition hinzufügen',
        'empty_member_list' => 'Keine Mitglieder gefunden',
        'empty_roles_list' => 'Keine Rollen gefunden',
    ],

    'create' => [
        'form' => [
            'header' => 'Leitungsfunktion zuordnen',
            'select_member.label' => 'Mitglied wählen',
            'select_role.label' => 'Rolle zuordnen',
            'title' => 'Rolle zuordnen',
            'btn_add_new_role' => [
                'label' => 'Neu',
            ],
            'option_add_new_role' => 'Neue Rolle anlegen',
            'option_select_role' => 'Rolle auswählen',
            'profile_image' => 'Profilbild',
            'designated_at' => 'Ernannt am',
            'designated_at.placeholder' => 'Datum',
            'about_me' => 'Über mich',
            'btn_add_member' => 'Rolle dem Mitglied zuordnen',
            'btn_update_member' => 'Rolle aktualisieren',
        ],
        'modal' => [
            'title' => 'Neue Rolle anlegen',
            'name' => 'Name',
            'description' => 'Beschreibung',
            'can_manage_accounting' => 'Kann Konten verwalten',
            'can_represent_organization' => 'Ist vertretungsberechtigt',
            'button' => 'Speichern',
        ],
    ],

    'validation' => [
        'error_required' => [
            'role_id' => 'Bitte eine Rolle auswählen',
            'member_id' => 'Bitte ein Mitglied auswählen',
            'designated_at' => 'Das Datum der Ernennung ist erforderlich',
        ],
    ],

    'toast' => [
        'msg' => [
            'leaderrole' => [
                'updated' => 'Daten wurden erfolgreich aktualisiert',
                'revoked' => 'Rolle wurder erfolgreich entzogen',
                'assigened' => 'Die Rolle wurde dem Mitglieg zugeordnet',

            ],
        ],
    ],

];
