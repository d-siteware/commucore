<?php

namespace App\Livewire\Accounting\FiscalYear\Delete;

use Livewire\Component;

class Form extends Component
{
    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('livewire.accounting.fiscal-year.delete.form');
    }
}
