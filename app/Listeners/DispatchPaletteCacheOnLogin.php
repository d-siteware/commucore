<?php

namespace App\Listeners;

use App\Jobs\CacheCommandPaletteJob;
use Illuminate\Auth\Events\Login;

class DispatchPaletteCacheOnLogin
{
    public function handle(Login $event): void
    {
        /**
         * @var \App\Models\User $user
         */
        $user = $event->user;
        // Instanzen haben keinen Queue-Worker — synchron wärmen,
        // sonst bliebe die Command-Palette dauerhaft leer.
        CacheCommandPaletteJob::dispatchSync($user->id);
    }
}
