<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Diese Anmeldeinformationen stimmen nicht mit unseren Aufzeichnungen überein.',
    'verify_email' => 'E-Mail bestätigen',
    'password' => 'Das angegebene Passwort ist falsch.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuchen Sie es in :Sekunden Sekunden erneut.',

    'sso_error' => 'Fehler beim SSO',
    'sso_retry' => 'Neu anmelden',

    'account_deleted' => [
        'title' => 'Dein Zugang wurde gelöscht',
        'text' => 'Dein Benutzerkonto wurde entfernt. Falls das ein Versehen war oder du wieder rein möchtest, melde dich bei uns — wir richten dir den Zugang neu ein.',
        'cta' => 'Support kontaktieren',
        'home' => 'Zur Startseite',
    ],

    'register' => [
        'page_title' => 'Registrieren',
        'btn' => 'Registrieren',
        'name' => 'Name',
        'email' => 'E-Mail',
        'password' => 'Passwort',
        'password_confirm' => 'Passwort bestätigen',
        'terms' => 'AGB',
    ],

    'api_tokens' => [
        'create' => 'API-Token erstellen',
        'create_btn' => 'Erstellen',
        'description' => 'API-Tokens ermöglichen es Drittanbietern, sich in Ihrem Namen bei unserer Anwendung zu authentifizieren.',
        'token_name' => 'Token-Name',
        'permissions' => 'Berechtigungen',
        'created' => 'Erstellt.',
        'manage' => 'API-Tokens verwalten',
        'manage_description' => 'Sie können vorhandene Tokens löschen, wenn sie nicht mehr benötigt werden.',
        'last_used' => 'Zuletzt verwendet',
        'delete' => 'Löschen',
        'api_token' => 'API-Token',
        'copy_token' => 'Bitte kopieren Sie Ihren neuen API-Token. Aus Sicherheitsgründen wird er nicht erneut angezeigt.',
        'close' => 'Schließen',
        'token_permissions' => 'API-Token-Berechtigungen',
        'cancel' => 'Abbrechen',
        'save' => 'Speichern',
        'delete_token' => 'API-Token löschen',
        'delete_confirm' => 'Sind Sie sicher, dass Sie diesen API-Token löschen möchten?',
    ],

];
