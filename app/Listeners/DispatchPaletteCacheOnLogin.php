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
        CacheCommandPaletteJob::dispatch($user->id)
            ->onQueue('default');
    }
}
