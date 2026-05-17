<?php

namespace App\Providers;

use App\Services\DefaultAccountsBootstrap;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Reģistrē lietotnes servisus.
     */
    public function register(): void
    {
        // Nav papildu reģistrācijas.
    }

    /**
     * Palaiž lietotnes servisus (boot).
     */
    public function boot(): void
    {
        // Ģenerēto URL (formas, route(), JSON fetch) sakrīt ar pārlūka adresi.
        // Pretējā gadījumā APP_URL kā http://localhost bez :8080 vai 127.0.0.1 vs localhost
        // padara fetch() starp izcelsmēm: sesijas sīkdatnes nesūtās → CSRF neatbilstība → 419.
        if (! $this->app->runningInConsole()) {
            $root = $this->app->make('request')->getSchemeAndHttpHost();
            if ($root !== '') {
                URL::forceRootUrl($root);
            }
        }

        RateLimiter::for('two-factor-recovery-support-page', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('two-factor-recovery-support-submit', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        DefaultAccountsBootstrap::ensureIfEnabled();
    }
}
