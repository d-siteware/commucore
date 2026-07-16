<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Commands\PruneExpiredApplications;
use App\Listeners\DispatchPaletteCacheOnLogin;
use App\Livewire\App\ApiTokenManager;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use App\Observers\BookingAccountObserver;
use App\Observers\FiscalYearObserver;
use App\Observers\MemberObserver;
use App\Observers\PaletteCacheObserver;
use App\Observers\TransactionObserver;
use App\Services\Accounting\Datev\DatevExportService;
use App\Services\Accounting\DatevSettingsService;
use App\Services\MailingService;
use App\Services\SettingsService;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event as EventFacade;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Opcodes\LogViewer\Facades\LogViewer;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MailingService::class, function ($app): MailingService {
            return new MailingService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LogViewer::auth(function ($request): true {
            return true;
        });

        putenv('MAGICK_GHOSTSCRIPT_PATH='.(PHP_OS_FAMILY === 'Darwin'
                ? '/opt/homebrew/bin/gs'
                : '/usr/bin/gs'
        ));

        JsonResource::macro('toResponse', function ($request): JsonResponse {
            return (new JsonResponse($this))
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        });

        $this->app->singleton(DatevSettingsService::class, fn ($app) => new DatevSettingsService($app->make(SettingsService::class))
        );

        $this->app->singleton(DatevExportService::class, fn ($app) => new DatevExportService($app->make(DatevSettingsService::class))
        );

        BookingAccount::observe(BookingAccountObserver::class);
        FiscalYear::observe(FiscalYearObserver::class);
        Transaction::observe(TransactionObserver::class);

        Member::observe(MemberObserver::class);

        Schedule::command(PruneExpiredApplications::class)
            ->daily();

        EventFacade::listen(LoginEvent::class, DispatchPaletteCacheOnLogin::class);

        Member::observe(PaletteCacheObserver::class);
        Event::observe(PaletteCacheObserver::class);
        Transaction::observe(PaletteCacheObserver::class);

        Blade::directive('isMultiLang', function () {
            return '<?php if (\App\Models\Locale::isMultiLanguage()) :  ?>';
        });

        Blade::directive('endIsMultiLang', function () {
            return '<?php endif; ?>';
        });

        Livewire::component('api.api-token-manager', ApiTokenManager::class);
    }
}
