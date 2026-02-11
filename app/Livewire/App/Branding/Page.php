<?php

namespace App\Livewire\App\Branding;

use App\Services\SettingsService;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Page extends Component
{
    use WithFileUploads;

    public Form $form;

    #[Validate('nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048')]
    public $newLogo;

    #[Validate('nullable|file|mimes:png,ico,svg|max:512')]
    public $newFavicon;

    public bool $showLogoUpload = false;

    public bool $showFaviconUpload = false;

    public string $currentTab = 'organization';

    public ?string $selectedLightColor = null;

    public ?string $selectedDarkColor = null;

    public function mount(SettingsService $settings)
    {
        $this->form->load();
    }

    public function save(SettingsService $settings)
    {
        $this->form->validate();
        $this->form->save($settings);

        $this->dispatch('branding-updated');

        Flux::toast(
            text: 'Ihre Änderungen wurden erfolgreich gespeichert.',
            heading: 'Branding gespeichert',
            variant: 'success'
        );
    }

    public function restoreDefaults(SettingsService $settings): void
    {
        $settings->resetGroup('branding');
        $settings->resetGroup('organization');

        $this->form->load();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: 'Alle Einstellungen wurden auf die Standardwerte zurückgesetzt.',
            heading: 'Branding zurückgesetzt',
            variant: 'success'
        );
    }

    public function updatedSelectedLightColor()
    {
        // Trigger refresh when color selection changes
    }

    public function updatedSelectedDarkColor()
    {
        // Trigger refresh when color selection changes
    }

    public function uploadLogo(SettingsService $settings)
    {
        $this->validate([
            'newLogo' => 'required|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        try {
            $settings->uploadLogo($this->newLogo);
            $this->newLogo = null;
            $this->showLogoUpload = false;

            $this->dispatch('branding-updated');

            Flux::toast(
                text: 'Das neue Logo wurde erfolgreich gespeichert und bereinigt.',
                heading: 'Logo hochgeladen',
                variant: 'success'
            );
        } catch (\InvalidArgumentException $e) {
            $this->addError('newLogo', $e->getMessage());

            Flux::toast(
                text: $e->getMessage(),
                heading: 'Fehler beim Upload',
                variant: 'danger'
            );
        }
    }

    public function uploadFavicon(SettingsService $settings)
    {
        $this->validate([
            'newFavicon' => 'required|file|mimes:png,ico,svg|max:512',
        ]);

        try {
            $settings->uploadFavicon($this->newFavicon);
            $this->newFavicon = null;
            $this->showFaviconUpload = false;

            $this->dispatch('branding-updated');

            Flux::toast(
                text: 'Das neue Favicon wurde erfolgreich gespeichert und bereinigt.',
                heading: 'Favicon hochgeladen',
                variant: 'success'
            );
        } catch (\InvalidArgumentException $e) {
            $this->addError('newFavicon', $e->getMessage());

            Flux::toast(
                text: $e->getMessage(),
                heading: 'Fehler beim Upload',
                variant: 'danger'
            );
        }
    }

    public function resetLogo(SettingsService $settings)
    {
        $settings->resetLogo();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: 'Das Standard-Logo wird jetzt verwendet.',
            heading: 'Logo zurückgesetzt',
            variant: 'success'
        );
    }

    public function resetFavicon(SettingsService $settings)
    {
        $settings->resetFavicon();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: 'Das Standard-Favicon wird jetzt verwendet.',
            heading: 'Favicon zurückgesetzt',
            variant: 'success'
        );
    }

    public function render()
    {
        return view('livewire.app.branding.page')->title('Einstellungen');
    }
}
