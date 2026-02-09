<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\LocaleService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function __construct(
        protected LocaleService $localeService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priorität: User > Session > Config
        $locale = Auth::user()?->locale
            ?? session('locale')
            ?? config('app.locale');

        // Service übernimmt Validierung und Fallback
        $this->localeService->setLocale($locale);

        return $next($request);
    }
}