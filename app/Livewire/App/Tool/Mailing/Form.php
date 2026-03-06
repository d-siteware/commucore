<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\Mailing;

use Illuminate\View\View;
use Livewire\Component;

final class Form extends Component
{
    public function render(): View
    {
        return view('livewire.app.tool.mailing.form');
    }
}
