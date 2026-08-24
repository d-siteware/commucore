<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Jobs\RecordHistory;
use App\Models\History;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

/**
 *  track changes in important models
 */
trait HasHistory
{
    public static function bootHasHistory(): void
    {

        static::created(function ($model): void {
            $model->recordHistory('created');
        });

        static::updating(function ($model): void {
            $model->recordHistory('updated');
        });

        static::deleting(function ($model): void {
            $model->recordHistory('deleted');
        });

    }

    public function histories(): MorphMany
    {
        return $this->morphMany(History::class, 'historable');
    }

    protected function recordHistory($action): void
    {
        $changes = null;

        if ($action === 'updated') {
            $dirty = $this->getDirty();

            // remember_token-Rotation (Login/Logout) ist audit-irrelevant — und
            // würde bei Logout-nach-Löschung synchron gegen eine bereits
            // gelöschte User-Row laufen (FK-Verletzung).
            if (array_keys($dirty) === ['remember_token']) {
                return;
            }

            $changes = [
                'old' => array_intersect_key($this->getOriginal(), $dirty),
                'new' => $dirty,
            ];
        }

        // Synchron statt queued: Instanz-Queues haben keinen Worker —
        // ein dispatch() würde in Redis verpuffen und nie eine History schreiben.
        RecordHistory::dispatchSync($this, $action, $changes, Auth::id());
    }
}
