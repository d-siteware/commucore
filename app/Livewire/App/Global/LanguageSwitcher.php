<?php

declare(strict_types=1);

namespace App\Livewire\App\Global;

use App\Models\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class LanguageSwitcher extends Component
{
    public string $currentLocale;

    public function mount(): void
    {
        $this->currentLocale = app()->getLocale();
    }

    public function switchLanguage(string $locale): Redirector|RedirectResponse
    {
        // Validierung gegen aktive Locales
        if (! in_array($locale, Locale::getNames(), true)) {
            abort(404);
        }

        // In Session speichern
        Session::put('locale', $locale);

        // Für eingeloggte User auch in DB speichern
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        // Seite neu laden, damit Middleware das Locale setzt
        return redirect()->back();
    }

    #[Computed]
    public function availableLocales()
    {
        return Locale::active()->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.global.language-switcher');
    }
}