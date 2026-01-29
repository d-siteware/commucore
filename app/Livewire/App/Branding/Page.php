<?php

namespace App\Livewire\App\Branding;

use App\Services\SettingsService;
use Livewire\Component;

class Page extends Component
{
    public Form $form;

    public function mount(SettingsService $settings)
    {
        $this->form->load();
    }

    public function save(SettingsService $settings)
    {
        $this->form->save($settings);

        $this->dispatch('branding-updated');
        $this->dispatch('notify', 'Branding gespeichert');
    }

    public function restoreDefaults(SettingsService $settings): void
    {
        $settings->resetGroup('branding');

        $this->form->load();

        $this->dispatch('notify', 'Branding zurückgesetzt');
    }


    public function render()
    {
        return view('livewire.app.branding.page');
    }
}
