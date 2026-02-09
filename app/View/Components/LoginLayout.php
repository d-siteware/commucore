<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class LoginLayout extends Component
{
    public string $title = 'CommuCore';

    public function __construct(string $title)
    {
        $this->title = $title ? $title.' | '.setting('organization.name') : setting('organization.name');

    }

    public function render(): View
    {
        return view('layouts.login');
    }
}
