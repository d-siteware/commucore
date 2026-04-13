<?php

// app/Jobs/CacheCommandPaletteJob.php

namespace App\Jobs;

use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class CacheCommandPaletteJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $userId) {}

    public function handle(): void
    {
        $ttl = now()->addHours(24);

        // Mitglieder
        Cache::tags(['palette', 'members'])->put(
            'palette:members',
            Member::query()
                ->select('id', 'first_name', 'name', 'type')
                ->orderBy('name')
                ->get()
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'label' => $m->fullName(),
                    'meta' => $m->id,
                    'type' => $m->type,
                    'url' => route('backend.members.show', $m->id),
                ])
                ->all(),
            $ttl
        );

        // Events/Aktivitäten
        Cache::tags(['palette', 'events'])->put(
            'palette:events',
            Event::query()
                ->select('id', 'title', 'event_date', 'status')
                ->orderByDesc('event_date')
                ->limit(100)
                ->get()
                ->map(fn ($e) => [
                    'id' => $e->id,
                    'label' => $e->title[app()->getLocale()] ?? $e->title['de'] ?? '',
                    'meta' => $e->event_date?->format('d.m.Y'),
                    'url' => route('backend.events.show', $e->id),
                ])
                ->all(),
            $ttl
        );

        // Buchungen
        Cache::tags(['palette', 'transactions'])->put(
            'palette:transactions',
            Transaction::query()
                ->select('id', 'label', 'reference', 'amount_gross', 'type', 'date')
                ->orderByDesc('date')
                ->limit(100)
                ->get()
                ->map(fn ($t) => [
                    'id' => $t->id,
                    'label' => $t->label,
                    'meta' => $t->grossForHumans(),
                    'url' => route('transaction.index', ['search' => $t->reference ?? $t->label]),
                ])
                ->all(),
            $ttl
        );
    }
}
