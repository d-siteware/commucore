<?php

namespace App\Livewire\App\Branding;

use App\Services\SettingsService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class Page extends Component
{
    use WithFileUploads;

    public Form $form;

    #[Validate('nullable|image|max:2048')]
    public $newLogo;

    #[Validate('nullable|image|mimes:png,ico|max:512')]
    public $newFavicon;

    public bool $showLogoUpload = false;
    public bool $showFaviconUpload = false;

    public function mount(SettingsService $settings)
    {
        $this->form->load();
    }

    public function save(SettingsService $settings)
    {
        $this->form->validate();
        $this->form->save($settings);

        $this->dispatch('branding-updated');
        $this->dispatch('notify', 'Branding gespeichert');
    }

    public function restoreDefaults(SettingsService $settings): void
    {
        $settings->resetGroup('branding');
        $settings->resetGroup('organization');

        $this->form->load();

        $this->dispatch('branding-updated');
        $this->dispatch('notify', 'Branding zurückgesetzt');
    }

    public function uploadLogo(SettingsService $settings)
    {
        $this->validate([
            'newLogo' => 'required|image|max:2048',
        ]);

        $settings->uploadLogo($this->newLogo);
        $this->newLogo = null;
        $this->showLogoUpload = false;

        $this->dispatch('branding-updated');
        $this->dispatch('notify', 'Logo hochgeladen');
    }

    public function uploadFavicon(SettingsService $settings)
    {
        $this->validate([
            'newFavicon' => 'required|image|mimes:png,ico|max:512',
        ]);

        $settings->uploadFavicon($this->newFavicon);
        $this->newFavicon = null;
        $this->showFaviconUpload = false;

        $this->dispatch('branding-updated');
        $this->dispatch('notify', 'Favicon hochgeladen');
    }

    public function resetLogo(SettingsService $settings)
    {
        $settings->resetLogo();

        $this->dispatch('branding-updated');
        $this->dispatch('notify', 'Logo zurückgesetzt');
    }

    public function resetFavicon(SettingsService $settings)
    {
        $settings->resetFavicon();

        $this->dispatch('branding-updated');
        $this->dispatch('notify', 'Favicon zurückgesetzt');
    }

    public function render()
    {
        return view('livewire.app.branding.page');
    }
}