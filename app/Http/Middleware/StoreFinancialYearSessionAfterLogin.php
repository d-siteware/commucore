<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Accounting\FiscalYear;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

final class StoreFinancialYearSessionAfterLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && ! session()->has('fiscalYearId')) {

            $activeFiscalYear = FiscalYear::getActive();

            if ($activeFiscalYear instanceof \App\Models\Accounting\FiscalYear) {
                Session::put('fiscalYearId', $activeFiscalYear->id);
            } else {
                $fiscalYear = FiscalYear::getOrCreate(
                    Carbon::today('Europe/Berlin')->year
                );
                Session::put('fiscalYearId', $fiscalYear->id);
            }
        }

        return $next($request);
    }
}
