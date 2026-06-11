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

    'failed' => 'Ezek a megadott adatok nem egyeznek a nyilvántartásunkkal.',
    'verify_email' => 'E-mail megerősítése',
    'password' => 'A megadott jelszó helytelen.',
    'throttle' => 'Túl sok bejelentkezési próbálkozás. Próbáld újra :seconds másodperc múlva.',

    'sso_error' => 'SSO hiba',
    'sso_retry' => 'Újra bejelentkezés',

    'register' => [
        'page_title' => 'Regisztráció',
        'btn' => 'Regisztráció',
        'name' => 'Név',
        'email' => 'E-mail',
        'password' => 'Jelszó',
        'password_confirm' => 'Jelszó megerősítése',
        'terms' => 'ÁSZF',
    ],

    'api_tokens' => [
        'create' => 'API Token létrehozása',
        'create_btn' => 'Létrehozás',
        'description' => 'Az API tokenek lehetővé teszik harmadik féltől származó szolgáltatások számára, hogy az Ön nevében hitelesítsék magukat az alkalmazásunkban.',
        'token_name' => 'Token neve',
        'permissions' => 'Jogosultságok',
        'created' => 'Létrehozva.',
        'manage' => 'API Tokenek kezelése',
        'manage_description' => 'Törölheti a meglévő tokeneket, ha már nincs rájuk szükség.',
        'last_used' => 'Utoljára használva',
        'delete' => 'Törlés',
        'api_token' => 'API Token',
        'copy_token' => 'Kérjük, másolja le az új API tokenjét. Biztonsági okokból nem fog újra megjelenni.',
        'close' => 'Bezárás',
        'token_permissions' => 'API Token jogosultságok',
        'cancel' => 'Mégse',
        'save' => 'Mentés',
        'delete_token' => 'API Token törlése',
        'delete_confirm' => 'Biztosan törölni szeretné ezt az API tokent?',
    ],

];
