<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn () => url('/'));
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $simulationPostRoutes = ['simulations.snapshot', 'simulations.runner-state'];

        $acceptsHtml = static fn (\Illuminate\Http\Request $request): bool => $request->accepts([
            'text/html',
            'application/xhtml+xml',
        ]);

        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) use ($simulationPostRoutes, $acceptsHtml) {
            if (in_array($request->route()?->getName(), $simulationPostRoutes, true)) {
                $sim = $request->route('simulation');
                if ($sim instanceof \App\Models\Simulation) {
                    return redirect()->route('simulations.show', $sim)
                        ->with('error', __('Your session expired or this form is no longer valid. Please refresh the page and try again.'));
                }
            }

            if (! $acceptsHtml($request)) {
                return null;
            }

            if ($request->is('admin*')) {
                return redirect()->back(fallback: route('admin.dashboard'))
                    ->with('error', __('Your session expired or this form is no longer valid. Please try again.'));
            }

            return redirect()->back(fallback: url('/'))
                ->with('error', __('Your session expired or this form is no longer valid. Please try again.'));
        });

        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) use ($simulationPostRoutes, $acceptsHtml) {
            if (in_array($request->route()?->getName(), $simulationPostRoutes, true)) {
                $sim = $request->route('simulation');
                if ($sim instanceof \App\Models\Simulation) {
                    return redirect()->route('simulations.show', $sim)
                        ->with('error', __('You are not allowed to perform this action on this simulation.'));
                }

                return redirect()->route('simulations.index')
                    ->with('error', __('You are not allowed to perform that action.'));
            }

            if (! $acceptsHtml($request)) {
                return null;
            }

            if ($request->is('admin*')) {
                return redirect()->route('admin.dashboard')
                    ->with('error', __('You are not allowed to do that.'));
            }

            return redirect()->back(fallback: url('/'))
                ->with('error', __('You are not allowed to do that.'));
        });

        $exceptions->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, \Illuminate\Http\Request $request) use ($simulationPostRoutes, $acceptsHtml) {
            if (in_array($request->route()?->getName(), $simulationPostRoutes, true)) {
                return redirect()->route('simulations.index')
                    ->with('error', __('That simulation could not be found or was removed.'));
            }

            if (! $acceptsHtml($request)) {
                return null;
            }

            if ($request->is('admin*')) {
                return redirect()->route('admin.dashboard')
                    ->with('error', __('That page or item was not found.'));
            }

            if (auth()->check()) {
                return redirect()->route('simulations.index')
                    ->with('error', __('That page or item was not found.'));
            }

            return redirect()->to(url('/'))
                ->with('error', __('That page was not found.'));
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) use ($acceptsHtml) {
            if (! $acceptsHtml($request)) {
                return null;
            }

            if ($request->is('/')) {
                return null;
            }

            return redirect()->to(url('/'))
                ->with('error', __('That page was not found.'));
        });
    })->create();
