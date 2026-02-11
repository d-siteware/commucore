<?php

// Add these to your existing app/Helpers/helpers.php file

use App\Services\SettingsService;
use Illuminate\Support\Facades\App;

if (!function_exists('organization')) {
    /**
     * Get organization setting value
     *
     * @param string $key The setting key (without 'organization.' prefix)
     * @param mixed $default Default value if not found
     * @param string|null $locale Specific locale (null = current locale)
     * @return mixed
     */
    function organization(string $key, mixed $default = null, ?string $locale = null): mixed
    {
        $fullKey = "organization.{$key}";
        $value = app(SettingsService::class)->get($fullKey, $default);

        // Handle empty arrays - return default
        if (is_array($value) && empty($value)) {
            return $default;
        }

        // For JSON values with locale support
        if (is_array($value) && !empty($value)) {
            $firstKey = array_key_first($value);

            // Get supported language codes dynamically from Locale model
            $supportedLanguages = \App\Models\Locale::getNames();

            // Check if it's a multilingual value (has language keys)
            if ($firstKey && in_array($firstKey, $supportedLanguages)) {
                $locale = $locale ?? App::getLocale();

                // Try requested locale, then fallback to 'de', then any available, then default
                if (isset($value[$locale])) {
                    return $value[$locale];
                }

                if (isset($value['de'])) {
                    return $value['de'];
                }

                // Return first available translation
                $firstValue = reset($value);
                if ($firstValue !== false && $firstValue !== '') {
                    return $firstValue;
                }

                return $default;
            }
        }

        return $value ?? $default;
    }
}

if (!function_exists('organization_all')) {
    /**
     * Get all translations for a multilingual organization setting
     *
     * @param string $key The setting key (without 'organization.' prefix)
     * @return array
     */
    function organization_all(string $key): array
    {
        $fullKey = "organization.{$key}";
        $value = app(SettingsService::class)->get($fullKey, []);

        return is_array($value) ? $value : [];
    }
}

if (!function_exists('organization_slogan')) {
    /**
     * Get organization slogan for current or specified locale
     *
     * @param string|null $locale
     * @return string
     */
    function organization_slogan(?string $locale = null): string
    {
        $value = organization('slogan', '', $locale);
        // Ensure we always return a string, never an array
        return is_string($value) ? $value : '';
    }
}

if (!function_exists('organization_description')) {
    /**
     * Get organization description for current or specified locale
     *
     * @param string|null $locale
     * @return string
     */
    function organization_description(?string $locale = null): string
    {
        $value = organization('description', '', $locale);
        // Ensure we always return a string, never an array
        return is_string($value) ? $value : '';
    }
}

if (!function_exists('organization_legal')) {
    /**
     * Get all legal information as an array
     *
     * @return array
     */
    function organization_legal(): array
    {
        $service = app(SettingsService::class);

        return [
            'register_id' => $service->get('organization.register_id', ''),
            'registered_date' => $service->get('organization.registered_date', ''),
            'court' => $service->get('organization.court', ''),
            'tax_id' => $service->get('organization.tax_id', ''),
            'vat_id' => $service->get('organization.vat_id', ''),
        ];
    }
}