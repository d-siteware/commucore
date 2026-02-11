<?php

namespace App\Livewire\App\Branding;

use App\Models\Locale;
use App\Services\SettingsService;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    // Light mode colors
    public string $primary;
    public string $secondary;
    public string $brand;
    public string $bg;
    public string $text;
    public string $positive;
    public string $negative;
    public string $storno;
    public string $accent;
    public string $accent_foreground;
    public string $accent_content;

    // Dark mode colors
    public string $primary_dark;
    public string $secondary_dark;
    public string $brand_dark;
    public string $bg_dark;
    public string $text_dark;
    public string $positive_dark;
    public string $negative_dark;
    public string $storno_dark;
    public string $accent_dark;
    public string $accent_foreground_dark;
    public string $accent_content_dark;

    // Basic organization info
    public string $organization_name = '';
    public string $organization_email = '';
    public string $organization_web = '';

    // Multilingual fields (now arrays)
    public array $organization_slogan = [];
    public array $organization_description = [];

    // Legal information
    public string $register_id = '';
    public string $registered_date = '';
    public string $court = '';
    public string $tax_id = '';
    public string $vat_id = '';

    public function load(): void
    {
        /**
         * Load Color values from config
         */
        $config = config('branding.branding.colors');

        $this->primary = setting('branding.light.primary', $config['light']['primary']);
        $this->secondary = setting('branding.light.secondary', $config['light']['secondary']);
        $this->brand = setting('branding.light.brand', $config['light']['brand']);
        $this->bg = setting('branding.light.bg', $config['light']['bg']);
        $this->text = setting('branding.light.text', $config['light']['text']);
        $this->positive = setting('branding.light.positive', $config['light']['positive']);
        $this->negative = setting('branding.light.negative', $config['light']['negative']);
        $this->storno = setting('branding.light.storno', $config['light']['storno']);
        $this->accent = setting('branding.light.accent', $config['light']['accent']);
        $this->accent_foreground = setting('branding.light.accent_foreground', $config['light']['accent_foreground']);
        $this->accent_content = setting('branding.light.accent_content', $config['light']['accent_content']);

        $this->primary_dark = setting('branding.dark.primary', $config['dark']['primary']);
        $this->secondary_dark = setting('branding.dark.secondary', $config['dark']['secondary']);
        $this->brand_dark = setting('branding.dark.brand', $config['dark']['brand']);
        $this->bg_dark = setting('branding.dark.bg', $config['dark']['bg']);
        $this->text_dark = setting('branding.dark.text', $config['dark']['text']);
        $this->positive_dark = setting('branding.dark.positive', $config['dark']['positive']);
        $this->negative_dark = setting('branding.dark.negative', $config['dark']['negative']);
        $this->storno_dark = setting('branding.dark.storno', $config['dark']['storno']);
        $this->accent_dark = setting('branding.dark.accent', $config['dark']['accent']);
        $this->accent_foreground_dark = setting('branding.dark.accent_foreground', $config['dark']['accent_foreground']);
        $this->accent_content_dark = setting('branding.dark.accent_content', $config['dark']['accent_content']);

        /**
         * Load Organization values
         */
        $orgConfig = config('branding.organization', []);

        // Basic info
        $this->organization_name = setting('organization.name', $orgConfig['name'] ?? config('app.name'));
        $this->organization_email = setting('organization.email', $orgConfig['email'] ?? '');
        $this->organization_web = setting('organization.web', $orgConfig['web'] ?? '');

        // Multilingual slogan
        $sloganData = setting('organization.slogan', $orgConfig['slogan'] ?? []);
        $this->organization_slogan = is_array($sloganData) ? $sloganData : $this->getDefaultTranslations();

        // Multilingual description
        $descData = setting('organization.description', $orgConfig['description'] ?? []);
        $this->organization_description = is_array($descData) ? $descData : $this->getDefaultTranslations();

        // Legal information
        $this->register_id = setting('organization.register_id', '');
        $this->registered_date = setting('organization.registered_date', '');
        $this->court = setting('organization.court', '');
        $this->tax_id = setting('organization.tax_id', '');
        $this->vat_id = setting('organization.vat_id', '');
    }

    protected function getDefaultTranslations(): array
    {
        $defaults = [];
        foreach (config('app.available_locales', ['de' => 'Deutsch', 'en' => 'English']) as $locale => $name) {
            $defaults[$locale] = '';
        }
        return $defaults;
    }

    public function rules(): array
    {
        $rules = [
            // Colors
            'primary' => 'required|string',
            'secondary' => 'required|string',
            'brand' => 'required|string',
            'bg' => 'required|string',
            'text' => 'required|string',
            'positive' => 'required|string',
            'negative' => 'required|string',
            'storno' => 'required|string',
            'accent' => 'required|string',
            'accent_foreground' => 'required|string',
            'accent_content' => 'required|string',
            'primary_dark' => 'required|string',
            'secondary_dark' => 'required|string',
            'brand_dark' => 'required|string',
            'bg_dark' => 'required|string',
            'text_dark' => 'required|string',
            'positive_dark' => 'required|string',
            'negative_dark' => 'required|string',
            'storno_dark' => 'required|string',
            'accent_dark' => 'required|string',
            'accent_foreground_dark' => 'required|string',
            'accent_content_dark' => 'required|string',

            // Organization basic
            'organization_name' => 'required|string|max:255',
            'organization_email' => 'nullable|email',
            'organization_web' => 'nullable|url',

            // Multilingual fields (validate each locale)
            'organization_slogan' => 'nullable|array',
            'organization_description' => 'nullable|array',

            // Legal
            'register_id' => 'nullable|string|max:255',
            'registered_date' => 'nullable|date',
            'court' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'vat_id' => 'nullable|string|max:255',
        ];

        // Add validation for each locale in slogan and description
        foreach (config('app.available_locales', Locale::getNames()) as $locale => $name) {
            $rules["organization_slogan.{$locale}"] = 'nullable|string|max:255';
            $rules["organization_description.{$locale}"] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    public function save(SettingsService $settings): void
    {
        // Save colors
        $settings->setMany([
            'branding.light.primary' => $this->primary,
            'branding.light.secondary' => $this->secondary,
            'branding.light.brand' => $this->brand,
            'branding.light.bg' => $this->bg,
            'branding.light.text' => $this->text,
            'branding.light.positive' => $this->positive,
            'branding.light.negative' => $this->negative,
            'branding.light.storno' => $this->storno,
            'branding.light.accent' => $this->accent,
            'branding.light.accent_foreground' => $this->accent_foreground,
            'branding.light.accent_content' => $this->accent_content,

            'branding.dark.primary' => $this->primary_dark,
            'branding.dark.secondary' => $this->secondary_dark,
            'branding.dark.brand' => $this->brand_dark,
            'branding.dark.bg' => $this->bg_dark,
            'branding.dark.text' => $this->text_dark,
            'branding.dark.positive' => $this->positive_dark,
            'branding.dark.negative' => $this->negative_dark,
            'branding.dark.storno' => $this->storno_dark,
            'branding.dark.accent' => $this->accent_dark,
            'branding.dark.accent_foreground' => $this->accent_foreground_dark,
            'branding.dark.accent_content' => $this->accent_content_dark,
        ], 'string');

        // Save basic organization info
        $settings->set('organization.name', $this->organization_name, 'string');
        $settings->set('organization.email', $this->organization_email ?? '', 'string');
        $settings->set('organization.web', $this->organization_web ?? '', 'string');

        // Save multilingual slogan (only if has values)
        if (!empty(array_filter($this->organization_slogan))) {
            $settings->set('organization.slogan', $this->organization_slogan, 'json');
        }

        // Save multilingual description (only if has values)
        if (!empty(array_filter($this->organization_description))) {
            $settings->set('organization.description', $this->organization_description, 'json');
        }

        // Save legal information
        $settings->setMany([
            'organization.register_id' => $this->register_id ?? '',
            'organization.registered_date' => $this->registered_date ?? '',
            'organization.court' => $this->court ?? '',
            'organization.tax_id' => $this->tax_id ?? '',
            'organization.vat_id' => $this->vat_id ?? '',
        ], 'string');
    }
}