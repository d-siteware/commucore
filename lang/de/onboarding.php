<?php

declare(strict_types=1);

return [
    'step' => [
        '01' => 'Organisation',
        '02' => 'Einstellungen',
        '03' => 'Team einladen',
        '04' => 'Fertig',
    ],
    'org' => [
        'heading' => 'Organisation',
        'subheading' => 'Grundlegende Informationen zu deiner Organisation.',
        'org_name' => 'Organisationsname',
        'email' => 'E-Mail',
        'website' => 'Website',
        'website_placeholder' => 'https://',
        'address' => 'Adresse',
        'zip' => 'PLZ',
        'city' => 'Stadt',
        'legal_heading' => 'Rechtliches',
        'legal_subheading' => 'Diese Angaben werden für Belege und Berichte verwendet.',
        'register_id' => 'Vereinsregister-Nr.',
        'register_id_placeholder' => 'VR 12345',
        'registered_date' => 'Eingetragen am',
        'court' => 'Amtsgericht',
        'tax_id' => 'Steuer-ID',
        'vat_id' => 'USt-ID',
        'vat_id_placeholder' => 'DE123456789',
    ],
    'settings' => [
        'fy_heading' => 'Geschäftsjahr',
        'fy_subheading' => 'Das Startjahr für die Buchhaltung.',
        'fy_label' => 'Startjahr',
        'locales_heading' => 'Sprachen',
        'locales_subheading' => 'Welche Sprachen sollen in deiner Instanz aktiv sein?',
        'locales_available' => 'verfügbare Sprachen',
    ],
    'team' => [
        'profile_heading' => 'Dein Profil',
        'profile_subheading' => 'Vervollständige deine eigenen Angaben.',
        'surname' => 'Nachname',
        'firstname' => 'Vorname',
        'username' => 'Benutzername',
        'invite_heading' => 'Team einladen',
        'invite_subheading' => 'Lade weitere Personen ein. Jede eingeladene Person wird automatisch als Mitglied angelegt – nicht jedes Mitglied hat automatisch einen Login.',
        'invite_name_placeholder' => 'Nachname',
        'invite_firstname_placeholder' => 'Vorname',
        'invite_email_placeholder' => 'email@beispiel.de',
        'add_more_btn' => 'Weitere hinzufügen',
        'smtp_warning_heading' => 'Hinweis',
        'smtp_warning_text' => 'Aktuell werden in dieser Instanz alle ausgehenden E-Mail in den log geschrieben und nicht verschickt. Bitte wenden Sie sich an unseren Helpdesk, wenn Sie die Möglichkeit des E-Mail Versandes nutzen möchten. Danke!',
    ],
    'finish' => [
        'heading' => 'Alles bereit!',
        'subheading' => 'Deine Organisation ist eingerichtet. Du kannst jetzt loslegen.',
        'fiscal_year' => 'Geschäftsjahr :year',
        'selected_locales' => 'Ausgewählte Sprachen',
        'selected_locale' => 'Ausgewählte Sprache',
        'invites_sent' => ':count Einladung(en) werden versendet',
        'btn_dashboard' => 'Zum Dashboard',
    ],
    'btn' => [
        'next' => 'Weiter',
        'back' => 'Zurück',
    ],
    'badge' => [
        'red' => 'Setup erforderlich',
        'amber' => 'Setup empfohlen',
    ],

    'checklist' => [
        'title' => 'Einrichtungs-Checkliste',
        'dismissed' => 'Einrichtungs-Checkliste ausgeblendet',
        'reopen' => 'Wieder anzeigen',
        'admin_badge' => 'Admin & Vorstand',
        'all_done' => 'Alles erledigt!',
        'all_done_subtitle' => 'Euer Verein ist startklar. Viel Erfolg mit CommuCore.',
        'go_to_module' => 'Zum Modul',
        'tutorial' => 'Tutorial',
        'hide' => 'Checkliste ausblenden',
        'refresh' => 'Neu bewerten',
        'completed' => ':completed / :total erledigt',

        'desc' => [
            'has_organization_data' => 'Name, Vereinsregister-Nr., Amtsgericht, Straße, PLZ und Ort müssen in den Einstellungen hinterlegt sein.',
            'has_statute' => 'Die Vereinssatzung muss mindestens in deutscher Sprache hinterlegt sein.',
            'has_board_member' => 'Mindestens ein Mitglied muss den Typ "Vorstand" haben und darf nicht ausgetreten sein.',
            'has_account' => 'Ein Zahlungskonto (Bankverbindung) muss angelegt sein.',
            'has_datev_berater_nr' => 'Die DATEV-Beraternummer muss in den Einstellungen hinterlegt sein.',
            'has_datev_mandant_nr' => 'Die DATEV-Mandantennummer muss in den Einstellungen hinterlegt sein.',
            'has_min_members' => 'Neben dem Gründungsmitglied müssen mindestens drei weitere aktive Mitglieder angelegt sein.',
            'has_all_roles_assigned' => 'Es müssen drei Rollen mit den Berechtigungen "Buchhaltung verwalten", "Verein vertreten" und "Buchhaltung prüfen" angelegt und jeweils an ein aktives Mitglied vergeben sein.',
        ],
    ],

    'validation' => [
        'active_locales' => [
            'required' => 'Es muss mindestens eine Sprache ausgewählt werden.',
            'min' => 'Es muss mindestens eine Sprache ausgewählt werden.',
        ],
    ],
];
