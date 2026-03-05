<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Commands\PruneExpiredApplications;
use App\Models\Membership\Member;
use App\Observers\MemberObserver;
use App\Services\MailingService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MailingService::class, function ($app) {

            return new MailingService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LogViewer::auth(function ($request) {
            return true;
        });

        putenv('MAGICK_GHOSTSCRIPT_PATH='.(PHP_OS_FAMILY === 'Darwin'
                ? '/opt/homebrew/bin/gs'
                : '/usr/bin/gs'
        ));

        // Ensure JSON responses use UTF-8 with unescaped Unicode characters
        // Ensure JSON responses use UTF-8 with unescaped Unicode characters
        JsonResource::macro('toResponse', function ($request) {
            return (new \Illuminate\Http\JsonResponse($this))
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        });

        Member::observe(MemberObserver::class);

        Schedule::command(PruneExpiredApplications::class)->daily();
    }
}
