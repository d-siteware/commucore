<?php

declare(strict_types=1);

return [

    'page' => [
        'title' => 'Rollen in der :name',
        'subtitle' => 'Führungspositionen und Rollen der Organisation verwalten.',
        'heading' => 'Verfügbare Rollen',
    ],

    'leadership' => [
        'heading' => 'Leitungsteam',
        'btn_add' => 'Neue Führungsposition hinzufügen',
        'empty_member_list' => 'Keine Mitglieder gefunden',
        'empty_roles_list' => 'Keine Rollen gefunden',
        'empty_roster' => 'Noch keine Führungspositionen vergeben.',
        'confirm_remove' => 'Die Rolle wird dieser Person sofort entzogen. Fortfahren?',
        'edit_label' => 'Zuordnung bearbeiten',
        'remove_label' => 'Rolle entziehen',
        'edit_role_label' => 'Rolle bearbeiten',
        'delete_role_label' => 'Rolle löschen',
        'profile_image_alt' => 'Profilbild von :name',
    ],

    'delete' => [
        'confirm' => 'Die Rolle wird unwiderruflich gelöscht. Fortfahren?',
    ],

    'create' => [
        'form' => [
            'header' => 'Leitungsfunktion zuordnen',
            'select_member' => ['label' => 'Mitglied wählen'],
            'select_role' => ['label' => 'Rolle zuordnen'],
            'title' => 'Rolle zuordnen',
            'btn_add_new_role' => [
                'label' => 'Neu',
            ],
            'option_add_new_role' => 'Neue Rolle anlegen',
            'option_select_role' => 'Rolle auswählen',
            'profile_image' => 'Profilbild',
            'designated_at' => 'Ernannt am',
            'designated_at.placeholder' => 'Datum',
            'section_profile' => 'Profil',
            'about_me' => 'Über mich',
            'btn_add_member' => 'Rolle dem Mitglied zuordnen',
            'btn_update_member' => 'Rolle aktualisieren',
        ],
        'modal' => [
            'title' => 'Neue Rolle anlegen',
            'title_edit' => 'Rolle bearbeiten',
            'name' => 'Name',
            'description' => 'Beschreibung',
            'callout_heading' => 'Wichtig',
            'callout_text' => 'Die Rolle des vertretungsberechtigten Mitgliedes hat rechtliche Konsequenzen, welche die Organisation beeinträchtigen können.',
            'can_manage_accounting' => 'Kann Konten verwalten',
            'can_audit_accounting' => 'Kann Buchhaltung prüfen',
            'can_represent_organization' => 'Ist vertretungsberechtigt',
            'sort' => 'Sortierung',
            'button' => 'Speichern',
        ],
    ],

    'validation' => [
        'error_duplicate_member_role' => 'Dieses Mitglied hat bereits eine Rolle',
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
                'revoked' => 'Rolle wurde erfolgreich entzogen',
                'assigened' => 'Die Rolle wurde dem Mitglied zugeordnet',

            ],
        ],
    ],

];
