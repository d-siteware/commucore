<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

final class LocaleController extends Controller
{
    /**
     * Switch the application locale and redirect back.
     *
     * @param  string  $locale  The desired locale (e.g., 'en', 'hu')
     */
    public function switch(string $locale): RedirectResponse
    {
        if (! in_array($locale, Locale::getNames(), true)) {
            abort(404);
        }

        App::setLocale($locale);

        session()->put('locale', $locale);

        return redirect()->back();
    }
}
