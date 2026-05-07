<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Aligns generated route()/url() roots with the actual request host.
 *
 * If APP_URL does not match how the site is opened (e.g. Laragon vhost vs .env),
 * form actions can point at another origin; the session cookie is not sent and
 * POST requests fail CSRF verification (419 Page Expired).
 */
class ForceRootUrlFromRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost() !== '') {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }

        return $next($request);
    }
}
