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
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Match generated URLs (forms, route(), JSON for fetch) to the browser address.
        // Otherwise APP_URL like http://localhost without :8080, or 127.0.0.1 vs localhost,
        // makes fetch() cross-origin: session cookies are not sent → CSRF token mismatch → 419.
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
