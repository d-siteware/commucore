<?php

namespace App\Services;

use App\Models\Locale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class LocaleService
{
    public function getActive(): Collection
    {
        return Cache::remember('active_locales', 3600, function () {
            return Locale::active()->get();
        });
    }

    public function setLocale(string $locale): void
    {
        $localeModel = Locale::where('name', $locale)
            ->where('active', true)
            ->first();

        if ($localeModel && $localeModel->hasLanguageFiles()) {
            App::setLocale($locale);
            session(['locale' => $locale]);
        } else {
            App::setLocale('de'); // Fallback
        }
    }

    public function getCurrentLocale(): Locale
    {
        $currentName = App::getLocale();

        return Locale::where('name', $currentName)->first()
            ?? Locale::fallback();
    }

    public function clearCache(): void
    {
        Cache::forget('active_locales');
    }

    /**
     * Formats a Carbon date using the current application locale.
     */
    public function formatDate(\Carbon\Carbon $date): string
    {
        return $this->getCurrentLocale()->formatDate($date);
    }

    /**
     * Formats a Carbon time using the current application locale.
     */
    public function formatTime(\Carbon\Carbon $date): string
    {
        return $this->getCurrentLocale()->formatTime($date);
    }
}
