<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Public\V1\EventController;
use App\Http\Controllers\Api\Public\V1\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return json_encode([
        'version' => '1.0.0',
        'released_at' => '2025-06-13',
    ]);
});

Route::get('/feed/events', [EventController::class, 'rssFeed'])->name('api.events.feed');

Route::prefix('v1')->group(function (): void {
    Route::get('/events', [EventController::class, 'apiIndex'])->name('api.events.index');
    Route::get('/events-all', [EventController::class, 'apiAll'])->name('api.events.all');

    Route::get('/event/{slug}', [EventController::class, 'apiShow'])->name('api.v1.event.show');
});

Route::prefix('public/v1')
    ->middleware(['auth:sanctum', 'ability:read'])
    ->group(function () {
        Route::get('events', [EventController::class, 'index']);
        Route::get('events/{event}', [EventController::class, 'show']);
        Route::get('posts', [PostController::class, 'index']);
        Route::get('posts/{post}', [PostController::class, 'show']);
    });
