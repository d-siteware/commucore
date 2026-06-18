<?php

declare(strict_types=1);

namespace App\Livewire\App\Branding;

use App\Livewire\Forms\Global\LocaleForm;
use App\Livewire\Forms\Sepa\SepaSettingsForm;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\PersistsTabs;
use App\Models\Locale;
use App\Models\Setting;
use App\Services\Sepa\SepaSettingsService;
use App\Services\SettingsService;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

final class Page extends Component
{
    use HasPrivileges;
    use WithFileUploads;
    use PersistsTabs;

    public Form $form;

    public LocaleForm $localeForm;

    public SepaSettingsForm $sepaForm;

    #[Validate('nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048')]
    public $newLogo;

    #[Validate('nullable|file|mimes:png,ico,svg|max:512')]
    public $newFavicon;

    public bool $showLogoUpload = false;

    public bool $showFaviconUpload = false;

    public string $defaultTab = 'org-info';

    public ?string $selectedTab;

    public ?string $selectedLightColor = null;

    public ?string $selectedDarkColor = null;


    #[Computed]
    public function locales(): LengthAwarePaginator
    {
        return Locale::query()->paginate(10);
    }

    public function mount(SettingsService $settings, SepaSettingsService $sepaSettings): void
    {
        $this->form->load();
        $this->sepaForm->load();
        //        $this->localeForm = new LocaleForm($this, 'localeForm');
    }

    // ==================== Per-tab save ====================

    public function saveColors(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $this->form->validate($this->form->rulesForColors());
        $this->form->saveColors($settings);

        $this->dispatch('branding-updated');

        Flux::toast(
            text: __('branding.toast.save_success_text'),
            heading: __('branding.toast.save_success_heading'),
            variant: 'success'
        );
    }

    public function saveOrgInfo(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $this->form->validate($this->form->rulesForOrgInfo());
        $this->form->saveOrgInfo($settings);

        $this->dispatch('branding-updated');

        Flux::toast(
            text: __('branding.toast.save_success_text'),
            heading: __('branding.toast.save_success_heading'),
            variant: 'success'
        );
    }

    public function saveTexts(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $this->form->validate($this->form->rulesForTexts());
        $this->form->saveTexts($settings);

        $this->dispatch('branding-updated');

        Flux::toast(
            text: __('branding.toast.save_success_text'),
            heading: __('branding.toast.save_success_heading'),
            variant: 'success'
        );
    }

    public function saveStatute(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $this->form->validate($this->form->rulesForStatute());
        $this->form->saveStatute($settings);

        $this->dispatch('branding-updated');

        Flux::toast(
            text: __('branding.toast.save_success_text'),
            heading: __('branding.toast.save_success_heading'),
            variant: 'success'
        );
    }

    // ==================== Per-tab restore ====================

    public function restoreColors(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $settings->resetGroup('branding');
        $this->form->load();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: __('branding.toast.restore_success_text'),
            heading: __('branding.toast.restore_success_heading'),
            variant: 'success'
        );
    }

    public function restoreOrgInfo(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $settings->resetKeys([
            'organization.name',
            'organization.email',
            'organization.web',
            'organization.register_id',
            'organization.registered_date',
            'organization.court',
            'organization.tax_id',
            'organization.vat_id',
            'organization.address',
            'organization.city',
            'organization.zip',
        ]);
        $this->form->load();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: __('branding.toast.restore_success_text'),
            heading: __('branding.toast.restore_success_heading'),
            variant: 'success'
        );
    }

    public function restoreTexts(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $settings->resetKeys([
            'organization.slogan',
            'organization.description',
            'organization.about_us',
        ]);
        $this->form->load();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: __('branding.toast.restore_success_text'),
            heading: __('branding.toast.restore_success_heading'),
            variant: 'success'
        );
    }

    public function restoreStatute(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $settings->resetKeys([
            'organization.statute',
        ]);
        $this->form->load();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: __('branding.toast.restore_success_text'),
            heading: __('branding.toast.restore_success_heading'),
            variant: 'success'
        );
    }

    public function saveSepa(SepaSettingsService $sepaSettings): void
    {
        $this->checkPrivilege(Setting::class);
        $this->sepaForm->validate();
        $this->sepaForm->save($sepaSettings);

        Flux::toast(
            text: __('sepa.settings.toast.save_success_text'),
            heading: __('sepa.settings.toast.save_success_heading'),
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
                text: __('branding.toast.logo_uploaded_text'),
                heading: __('branding.toast.logo_uploaded_heading'),
                variant: 'success'
            );
        } catch (\InvalidArgumentException $e) {
            $this->addError('newLogo', $e->getMessage());

            Flux::toast(
                text: $e->getMessage(),
                heading: __('branding.toast.upload_error_heading'),
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
                text: __('branding.toast.favicon_uploaded_text'),
                heading: __('branding.toast.favicon_uploaded_heading'),
                variant: 'success'
            );
        } catch (\InvalidArgumentException $e) {
            $this->addError('newFavicon', $e->getMessage());

            Flux::toast(
                text: $e->getMessage(),
                heading: __('branding.toast.upload_error_heading'),
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
            text: __('branding.toast.logo_reset_text'),
            heading: __('branding.toast.logo_reset_heading'),
            variant: 'success'
        );
    }

    public function resetFavicon(SettingsService $settings): void
    {
        $this->checkPrivilege(Setting::class);
        $settings->resetFavicon();

        $this->dispatch('branding-updated');

        Flux::toast(
            text: __('branding.toast.favicon_reset_text'),
            heading: __('branding.toast.favicon_reset_heading'),
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
            Flux::toast(text: __('branding.toast.locale_updated'), variant: 'success');
        } else {
            $this->localeForm->create();
            Flux::toast(text: __('branding.toast.locale_created'), variant: 'success');
        }
    }

    public function deleteLocale(): void
    {
        $this->checkPrivilege(Locale::class);
        if (isset($this->localeForm->id)) {
            if ($this->localeForm->delete()) {
                Flux::toast(
                    text: __('branding.toast.locale_deleted'),
                );
                Flux::modal('delete-locale')->close();
            }
        }
    }

    public function render(): View
    {
        return view('livewire.app.branding.page')->title(__('branding.page.heading'));
    }
}
