<?php

namespace App\Livewire\App;

use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;

class DemoBanner extends Component
{
    public bool  $isDemo    = false;
    public ?int  $resetAt   = null;   // Unix-Timestamp des nächsten Resets
    public string $timeLeft = '';
    public bool  $resetting = false;

    public function mount(): void
    {
        $this->isDemo  = (bool) config('app.is_demo', false);
        $resetAt       = config('app.demo_reset_at');

        if ($this->isDemo && $resetAt) {
            $this->resetAt = (int) $resetAt;
        }

        $this->tick();
    }

    /**
     * Wird von Livewire per Wire:poll aufgerufen (jede Sekunde)
     */
    public function tick(): void
    {

        $diff = $this->resetAt - now()->timestamp;

        if ($diff <= 0) {
            $this->resetting = true;
            $this->timeLeft  = 'Reset läuft...';
            return;
        }

        $hours   = floor($diff / 3600);
        $minutes = floor(($diff % 3600) / 60);
        $seconds = $diff % 60;

        $this->timeLeft = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

}