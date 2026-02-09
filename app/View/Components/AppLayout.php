<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class AppLayout extends Component
{
    public string $title = 'CommuCore';

    public int $counter = 0;

    public function __construct(?string $title)
    {
        if ($title) {
            $this->title = $title.' | '.setting('organization.name');
        } else {
            $this->title = setting('organization.name');
        }
    }

    public function mount(): void {}

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
