<?php

declare(strict_types=1);

namespace App\Livewire\App\Branding;

use App\Livewire\Forms\Global\LocaleForm;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Locale;
use App\Models\Setting;
use App\Services\SettingsService;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

final class Page extends Component
{
    use HasPrivileges;
    use WithFileUploads;

    public Form $form;

    public LocaleForm $localeForm;

    #[Validate('nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048')]
    public $newLogo;

    #[Validate('nullable|file|mimes:png,ico,svg|max:512')]
    public $newFavicon;

    public bool $showLogoUpload = false;

    public bool $showFaviconUpload = false;

    public string $currentTab = 'org-info';

    public ?string $selectedLightColor = null;

    public ?string $selectedDarkColor = null;

    #[Computed]
    public function locales(): LengthAwarePaginator
    {
        return Locale::query()->paginate(10);
    }

    public function mount(SettingsService $settings): void
    {
        $this->form->load();
//        $this->localeForm = new LocaleForm($this, 'localeForm');
    }

    public function save(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
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
        $this->checkPrivilege(Setting::class);
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

    public function updatedSelectedLightColor(): void
    {
        // Trigger refresh when color selection changes
    }

    public function updatedSelectedDarkColor(): void
    {
        // Trigger refresh when color selection changes
    }

    public function uploadLogo(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
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

    public function uploadFavicon(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
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

    public function resetLogo(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $settings->resetLogo();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: 'Das Standard-Logo wird jetzt verwendet.',
            heading: 'Logo zurückgesetzt',
            variant: 'success'
        );
    }

    public function resetFavicon(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $settings->resetFavicon();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: 'Das Standard-Favicon wird jetzt verwendet.',
            heading: 'Favicon zurückgesetzt',
            variant: 'success'
        );
    }

    public function editLocale(int $id): void
    {
        $this->checkPrivilege(Locale::class);
        $this->localeForm->set($id);
    }

    public function createLocale(): void
    {
        $this->checkPrivilege(Locale::class);
        $this->localeForm->reset();
    }

    public function storeLocale(): void
    {
        $this->checkPrivilege(Locale::class);

        if ($this->localeForm->id !== null) {
            $this->localeForm->update();
            Flux::toast(text: 'Sprache aktualisiert.', variant: 'success');
        } else {
            $this->localeForm->create();
            Flux::toast(text: 'Sprache erstellt.', variant: 'success');
        }
    }

    public function deleteLocale(): void
    {
        $this->checkPrivilege(Locale::class);
        if (isset($this->localeForm->id)) {
            if ($this->localeForm->delete()) {
                Flux::toast(
                    text: 'Sprache erfolgreich gelöscht.',
                );
                Flux::modal('delete-locale')->close();
            }
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.branding.page')->title('Einstellungen');
    }
}
