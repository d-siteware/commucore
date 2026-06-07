<?php

declare(strict_types=1);

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\StoreFinancialYearSessionAfterLogin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

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
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'ability' => CheckAbilities::class,
            'abilities' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

if (! empty($instancePath)) {
    if (! is_dir($instancePath)) {
        http_response_code(404);
        echo 'Instance not found.';
        exit;
    }
    $app->useStoragePath($instancePath.'/storage');
    $app->useEnvironmentPath($instancePath);
}

return $app;
