<?php

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],
    'allowed_methods' => ['GET', 'OPTIONS'],
    'allowed_origins' => ['*'],
    'allowed_headers' => ['Authorization', 'Content-Type', 'Accept'],
    'exposed_headers' => [],
    'max_age' => 86400,
    'supports_credentials' => false,

];
