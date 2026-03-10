<?php

declare(strict_types=1);

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\StoreFinancialYearSessionAfterLogin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| CommuCore – Shared Codebase Bootstrap (Laravel 11)
|--------------------------------------------------------------------------
| INSTANCE_PATH wird von nginx per fastcgi_param gesetzt und zeigt auf
| /var/instances/{subdomain} – dort liegen .env, storage/ und SQLite.
|
| Priorität:
|   1. $_SERVER['INSTANCE_PATH']  (nginx / CLI mit INSTANCE_PATH=...)
|   2. Fallback: normales Laravel-Verhalten (lokale Herd-Entwicklung)
*/

$instancePath = rtrim(
    getenv('INSTANCE_PATH') ?: ($_SERVER['INSTANCE_PATH'] ?? ''),
    '/'

);

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web([
            SetLocale::class,
            StoreFinancialYearSessionAfterLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Instanz-spezifische Pfade überschreiben (nur wenn INSTANCE_PATH gesetzt)
if (!empty($instancePath)) {
    $app->useStoragePath($instancePath . '/storage');
    $app->useEnvironmentPath($instancePath);
}

return $app;
