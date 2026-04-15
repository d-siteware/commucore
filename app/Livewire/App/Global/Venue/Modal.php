<?php

declare(strict_types=1);

namespace App\Livewire\App\Global\Venue;

use Livewire\Component;

final class Modal extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.global.venue.modal');
    }
}
