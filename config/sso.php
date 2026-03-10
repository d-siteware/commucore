<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSO Secret
    |--------------------------------------------------------------------------
    | Geteiltes Secret zwischen Verwaltungs-App und allen Instanzen.
    | Wird einmalig generiert und in alle .env Dateien eingetragen.
    |
    | Generieren: php artisan tinker --execute="echo Str::random(64);"
    |
    */
    'secret' => env('SSO_SECRET'),

    /*
    | Token-Gültigkeit in Sekunden
    */
    'ttl' => env('SSO_TTL', 60),
];