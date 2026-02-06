<?php

namespace App\Livewire\App\Branding;

use App\Services\SettingsService;
use Livewire\Form as LivewireForm;
use Illuminate\Support\Facades\Cache;

class Form extends LivewireForm
{
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

    public string $organization_name = '';
    public string $organization_email = '';
    public string $organization_web = '';
    public string $organization_slogan = '';
    public string $organization_description = '';

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
         * Load Organization values from config
         */
        $orgConfig = config('branding.organization');
        $this->organization_name = setting('organization.name', $orgConfig['name'] ?? '');
        $this->organization_email = setting('organization.email', $orgConfig['email'] ?? '');
        $this->organization_web = setting('organization.web', $orgConfig['web'] ?? '');
        $this->organization_slogan = setting('organization.slogan', $orgConfig['slogan'] ?? '');
        $this->organization_description = setting('organization.description', $orgConfig['description'] ?? '');

    }

    public function rules(): array
    {
        return [
            // Colors (optional validation)
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

            // Organization
            'organization_name' => 'required|string|max:255',
            'organization_email' => 'required|email',
            'organization_web' => 'required|url',
            'organization_slogan' => 'nullable|string|max:255',
            'organization_description' => 'nullable|string|max:500',
        ];
    }

    public function save(SettingsService $settings): void
    {
        $settings->setMany([
            'branding.light.primary'           => $this->primary,
            'branding.light.secondary'         => $this->secondary,
            'branding.light.brand'             => $this->brand,
            'branding.light.bg'                => $this->bg,
            'branding.light.text'              => $this->text,
            'branding.light.positive'          => $this->positive,
            'branding.light.negative'          => $this->negative,
            'branding.light.storno'            => $this->storno,
            'branding.light.accent'            => $this->accent,
            'branding.light.accent_foreground' => $this->accent_foreground,
            'branding.light.accent_content' => $this->accent_content,

            'branding.dark.primary'           => $this->primary_dark,
            'branding.dark.secondary'         => $this->secondary_dark,
            'branding.dark.brand'             => $this->brand_dark,
            'branding.dark.bg'                => $this->bg_dark,
            'branding.dark.text'              => $this->text_dark,
            'branding.dark.positive'          => $this->positive_dark,
            'branding.dark.negative'          => $this->negative_dark,
            'branding.dark.storno'            => $this->storno_dark,
            'branding.dark.accent'            => $this->accent_dark,
            'branding.dark.accent_foreground' => $this->accent_foreground_dark,
            'branding.dark.accent_content' => $this->accent_content_dark,

            'organization.name'        => $this->organization_name,
            'organization.email'       => $this->organization_email,
            'organization.web'         => $this->organization_web,
            'organization.slogan'      => $this->organization_slogan,
            'organization.description' => $this->organization_description,
        ]);
    }
}
