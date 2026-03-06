<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\Whatsapp;

use Livewire\Component;

final class Send extends Component
{
    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('livewire.app.tool.whatsapp.send');
    }
}
