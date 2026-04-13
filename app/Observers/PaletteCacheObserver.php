<?php

// app/Observers/PaletteCacheObserver.php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class PaletteCacheObserver
{
    public function saved(mixed $model): void
    {
        $this->flush($model);
    }

    public function deleted(mixed $model): void
    {
        $this->flush($model);
    }

    private function flush(mixed $model): void
    {
        $tag = match (true) {
            $model instanceof \App\Models\Membership\Member => 'members',
            $model instanceof \App\Models\Event\Event => 'events',
            $model instanceof \App\Models\Accounting\Transaction => 'transactions',
            default => null,
        };

        if ($tag !== null) {
            Cache::tags(['palette', $tag])
                ->flush();
        }
    }
}
