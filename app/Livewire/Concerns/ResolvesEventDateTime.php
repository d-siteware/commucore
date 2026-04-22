<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Carbon\Carbon;

trait ResolvesEventDateTime
{
    private function resolveDateTime(string $date, string $time): string
    {
        $parsedDate = Carbon::parse($date)->format('Y-m-d');
        $resolved = Carbon::createFromFormat('Y-m-d H:i', "{$parsedDate} {$time}");

        return $resolved
            ? $resolved->format('Y-m-d H:i:s')
            : "{$parsedDate} {$time}:00";
    }
}
